<?php

namespace app\controllers;

use Yii;
use app\models\TransBbk;
use app\models\DetBbk;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BbkController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['get'],
                    'petugas' => ['get'],
                    'listbbk' => ['get'],
                    'detailstok' => ['post'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (excel(isset($this->actions['*']))) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }
        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_bbk')
                ->select('*')
                ->orderBy('no_bbk DESC')
                ->limit(1);

        $cek = TransBbk::findOne('no_bbk = "BK' . date("y") . '0001"');
        if (empty($cek)) {
            $command = $query->createCommand();
            $models = $command->query()->read();
            $urut = substr($models['no_bbk'], 4) + 1;
            $kode = substr('0000' . $urut, strlen($urut));
            $kode = "BK" . date("y") . $kode;
        } else {
            $kode = "BK" . date("y") . "0001";
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionDetailstok() {
        $params = json_decode(file_get_contents("php://input"), true);
        $sisa_pengambilan = 0;
        $stok_sekarang = 0;
        
        if (!empty($params['kd_barang'])) {
            $stok = Barang::find()->where('kd_barang="' . $params['kd_barang']['kd_barang'] . '"')->one();
            $stok_sekarang = $stok->saldo;

            if (!empty($params['no_wo'])) {
                // mencari jumlah barang dari bom
                $query = new Query;
                $query->from('view_bom_wo as vbw, det_standar_bahan as dsb')
                        ->select("sum(dsb.qty) as jml")
                        ->where('vbw.kd_bom = dsb.kd_bom and dsb.kd_barang = "' . $params['kd_barang']['kd_barang'] . '" and vbw.no_wo = "' . $params['no_wo']['no_wo'] . '"');
                $command = $query->createCommand();
                $stokBom = $command->query()->read();

                //mencari jumlah barang yang telah diambil
                $query = new Query;
                $query->from('det_bbk as db, trans_bbk as tb')
                        ->select("sum(db.jml) as jml_keluar")
                        ->where('db.no_bbk = tb.no_bbk and db.kd_barang = "' . $params['kd_barang']['kd_barang'] . '" and db.no_wo = "' . $params['no_wo'] . '"');
                $command = $query->createCommand();
                $stokKeluar = $command->query()->read();

                $sisa_pengambilan = $stokBom['jml'] - $stokKeluar['jml_keluar'];
            }
        }
        $data['sisa_pengambilan'] = $sisa_pengambilan;
        $data['stok_sekarang'] = $stok_sekarang;
        echo json_encode(array('data' => $data));
    }

    public function actionPetugas() {
//        $petugas = \yii\models\User::findOne('id = '.Yii::$app->user->getId());
//        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'petugas' => 'admin'));
    }

    public function actionListbbk() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('trans_bbk')
                ->select("no_bbk")
                ->where('no_bbk like "%' . $param['nama'] . '%"')
                ->limit(15);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_bbk DESC";
        $offset = 0;
        $limit = 10;

        //limit & offset pagination
        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

        //sorting
        if (isset($params['sort'])) {
            $sort = $params['sort'];
            if (isset($params['order'])) {
                if ($params['order'] == "false")
                    $sort.=" ASC";
                else
                    $sort.=" DESC";
            }
        }

        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('trans_bbk as tb')
                ->leftJoin('tbl_karyawan as tk', 'tb.penerima = tk.nik')
                ->leftJoin('tbl_jabatan as tj', 'tj.id_jabatan  = tb.kd_jab')
                ->orderBy($sort)
                ->select("tb.*, tk.nama as penerima, tj.jabatan as bagian");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = TransBbk::find()->where('no_bbk="' . $id . '"')->one();

        $query = new Query;
        $query->from('tbl_jabatan')
                ->select('id_jabatan, jabatan')
                ->where('id_jabatan = "' . $model->kd_jab . '"')
                ->limit(1);
        $command = $query->createCommand();
        $jabatan = $command->query()->read();
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select('nik, nama')
                ->where('nik = "' . $model->penerima . '"')
                ->limit(1);
        $command = $query->createCommand();
        $penerima = $command->query()->read();

        $model->kd_jab = isset($jabatan) ? $jabatan : '';
        $model->penerima = isset($penerima) ? $penerima : '';
        $model->no_wo = array('no_wo' => $model->no_wo);

        $detail = DetBbk::find()->where('no_bbk="' . $model->no_bbk . '"')->all();
        $i = 0;
        $det = array();
        foreach ($detail as $val) {
            $query->from('barang')
                    ->select('kd_barang, nm_barang')
                    ->where('kd_barang = "' . $val['kd_barang'] . '"')
                    ->limit(1);
            $command = $query->createCommand();
            $det[$i]['jml'] = $val['jml'];
            $det[$i]['ket'] = $val['ket'];
            $barang = $command->query()->read();
            $det[$i]['kd_barang'] = isset($barang) ? $barang : '';
            $i++;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $det), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TransBbk();
        $model->attributes = $params['bbk'];
        $model->tanggal = date("Y-m-d");
        $model->status = 0;
        $model->no_wo = isset($params['bbk']['no_wo']['no_wo']) ? $params['bbk']['no_wo']['no_wo'] : '-';
        $model->kd_jab = isset($params['bbk']['kd_jab']['id_jabatan']) ? $params['bbk']['kd_jab']['id_jabatan'] : '-';
        $model->penerima = isset($params['bbk']['penerima']['nik']) ? $params['bbk']['penerima']['nik'] : '-';

        if ($model->save()) {
            $detailBbk = $params['detailBbk'];
            foreach ($detailBbk as $val) {
                $det = new DetBbk();
                $det->attributes = $val;
                $det->kd_barang = $val['kd_barang']['kd_barang'];
                $det->no_bbk = $model->no_bbk;
                $det->save();

                //update stok barang
                $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                $barang->saldo -= $det->jml;
                $barang->save();
            }

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = TransBbk::find()->where('no_bbk="' . $id . '"')->one();
        $model->attributes = $params['bbk'];
        $model->tanggal = date("Y-m-d");
        $model->status = 0;
        $model->no_wo = isset($params['bbk']['no_wo']['no_wo']) ? $params['bbk']['no_wo']['no_wo'] : '-';
        $model->kd_jab = isset($params['bbk']['kd_jab']['id_jabatan']) ? $params['bbk']['kd_jab']['id_jabatan'] : '-';
        $model->penerima = isset($params['bbk']['penerima']['nik']) ? $params['bbk']['penerima']['nik'] : '-';

        if ($model->save()) {
            // mengembalikan stok barang
            $detail = DetBbk::find()->where('no_bbk = "' . $model->no_bbk . '"')->all();
            foreach ($detail as $detbbk) {
                $barang = Barang::find()->where('kd_barang="' . $detbbk->kd_barang . '"')->one();
                $barang->saldo += $detbbk->jml;
                $barang->save();
            }

            //hapus detail bbk
            $del = DetBbk::deleteAll('no_bbk = "' . $model->no_bbk . '"');

            //isi detail dengan yang baru
            $detailBbk = $params['detailBbk'];
            foreach ($detailBbk as $val) {
                $det = new DetBbk();
                $det->attributes = $val;
                $det->kd_barang = $val['kd_barang']['kd_barang'];
                $det->no_bbk = $model->no_bbk;
                $det->save();

                //update stok barang
                $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                $barang->saldo -= $det->jml;
                $barang->save();
            }

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = TransBbk::find()->where('no_bbk="' . $id . '"')->one();
        if ($model->delete()) {
            // mengembalikan stok barang
            $detail = DetBbk::find()->where('no_bbk = "' . $model->no_bbk . '"')->all();
            foreach ($detail as $detbbk) {
                $barang = Barang::find()->where('kd_barang="' . $detbbk->kd_barang . '"')->one();
                $barang->saldo += $detbbk->jml;
                $barang->save();
            }

            //hapus detail bbk
            $delBbk = DetBbk::deleteAll('no_bbk = "' . $id . '"');

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransBbk::findOne($id)) !== null) {
            return $model;
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Bad request'), JSON_PRETTY_PRINT);
            exit;
        }
    }

    private function setHeader($status) {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Nintriva <nintriva.com>");
    }

    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}

?>
