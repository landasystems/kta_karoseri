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
                    'excel' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['get'],
                    'listbbk' => ['get'],
                    'detailstok' => ['post'],
                    'rekap' => ['get'],
                    'listbarang' => ['post'],
                    'print' => ['get'],
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

    public function actionPrint() {
        $params = json_decode(file_get_contents("php://input"), true);
        $update = TransBbk::updateAll(array('status' => 1), 'no_bbk = "' . $params['no_bbk'] . '"');
    }

    public function actionListbarang() {
        $params = json_decode(file_get_contents("php://input"), true);
        if (!empty($params['no_wo']) and !empty($params['kd_jab'])) {
            //cek optional bom
            $optional = \app\models\TransAdditionalBom::findAll(['no_wo' => $params['no_wo']['no_wo']]);

            //jika tidak ada optional
            if (empty($optional) or count($optional) == 0) {
                $query = new Query;
                $query->from('det_standar_bahan as dsb')
                        ->join('LEFT JOIN', 'barang as b', 'b.kd_barang = dsb.kd_barang')
                        ->join('LEFT JOIN', 'tbl_jabatan as tj', 'tj.id_jabatan = dsb.kd_jab')
                        ->join('LEFT JOIN', 'spk', 'spk.kd_bom = dsb.kd_bom')
                        ->join('LEFT JOIN', 'wo_masuk as wm', 'spk.no_spk = wm.no_spk')
                        ->select('b.saldo as stok, wm.no_wo as no_wo, b.kd_barang as kd_barang, '
                                . 'b.nm_barang as nm_barang, tj.id_jabatan as kd_jabatan, '
                                . 'tj.jabatan as bagian, dsb.qty as jml, dsb.ket as ket')
                        ->where('b.nm_barang like "%' . $params['nama'] . '%" and wm.no_wo = "' . $params['no_wo']['no_wo'] . '" and tj.id_jabatan = "' . $params['kd_jab']['id_jabatan'] . '"');
                $command = $query->createCommand();
                $models = $command->queryAll();
            } else {
                $query = new Query;
                $query->from('det_additional_bom as dsb')
                        ->join('JOIN', 'barang as b', 'b.kd_barang = dsb.kd_barang')
                        ->join('JOIN', 'tbl_jabatan as tj', 'tj.id_jabatan = dsb.kd_jab')
                        ->join('JOIN', 'spk', 'spk.kd_bom = dsb.kd_bom')
                        ->join('JOIN', 'wo_masuk as wm', 'spk.no_spk = wm.no_spk')
                        ->select('b.saldo as stok, wm.no_wo as no_wo, b.kd_barang as kd_barang, '
                                . 'b.nm_barang as nm_barang, tj.id_jabatan as kd_jabatan, '
                                . 'tj.jabatan as bagian, dsb.qty as jml, dsb.ket as ket')
                        ->where('b.nm_barang like "%' . $params['nama'] . '%" and dsb.no_wo = "' . $params['no_wo']['no_wo'] . '" and tj.id_jabatan = "' . $params['kd_jab']['id_jabatan'] . '"');

                $command = $query->createCommand();
                $models = $command->queryAll();
            }

            $queryBbk = new Query;
            $queryBbk->from('trans_bbk as tb')
                    ->join('JOIN', 'det_bbk as db', 'tb.no_bbk = db.no_bbk')
                    ->select('db.kd_barang, db.jml')
                    ->where('tb.no_wo = "' . $params['no_wo']['no_wo'] . '" and tb.kd_jab = "' . $params['kd_jab']['id_jabatan'] . '"');

            $commandBbk = $queryBbk->createCommand();
            $modelsBbk = $commandBbk->queryAll();



            $detBbk = array();
            foreach ($modelsBbk as $valBbk) {
                $detBbk[$valBbk['kd_barang']]['jml_keluar'] = isset($detBbk[$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
            }

            $det = array();
            $i = 0;
            foreach ($models as $val) {
                $det[$i]['kd_barang'] = $val['kd_barang'];
                $det[$i]['nm_barang'] = $val['nm_barang'];
                $det[$i]['stok_sekarang'] = $val['stok'];
                $det[$i]['sisa_pengambilan'] = isset($detBbk[$val['kd_barang']]['jml_keluar']) ? $val['jml'] - $detBbk[$val['kd_barang']]['jml_keluar'] : $val['jml'];
                $i++;
            }

            echo json_encode(array('status' => 1, 'data' => $det));
        }
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_bbk')
                ->select('*')
                ->orderBy('no_bbk DESC')
                ->limit(1);
        $command = $query->createCommand();
        $models = $command->query()->read();

        $cek = TransBbk::findOne('no_bbk = "BK' . date("y") . '00001"');
        if (empty($cek)) {
            $urut = substr($models['no_bbk'], -5) + 1;
            $kode = substr('00000' . $urut, strlen($urut));
            $kode = "BK" . date("y") . $kode;
        } else {
            $kode = "BK" . date("y") . "00001";
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionDetailstok() {
        $params = json_decode(file_get_contents("php://input"), true);
        $sisa_pengambilan = 0;
        $stok_sekarang = 0;
        Yii::error($params);

        if (!empty($params['kd_barang'])) {
            $stok = Barang::find()->where('kd_barang="' . $params['kd_barang'] . '"')->one();
            $stok_sekarang = $stok->saldo;

            if (!empty($params['no_wo'])) {
                // mencari jumlah barang dari bom
                $query = new Query;
                $query->from('view_bom_wo as vbw, det_standar_bahan as dsb')
                        ->select("sum(dsb.qty) as jml")
                        ->where('vbw.kd_bom = dsb.kd_bom and dsb.kd_barang = "' . $params['kd_barang'] . '" and vbw.no_wo = "' . $params['no_wo']['no_wo'] . '"');
                $command = $query->createCommand();
                $stokBom = $command->query()->read();

                //mencari jumlah barang yang telah diambil
                $query = new Query;
                $query->from('det_bbk as db, trans_bbk as tb')
                        ->select("sum(db.jml) as jml_keluar")
                        ->where('db.no_bbk = tb.no_bbk and db.kd_barang = "' . $params['kd_barang'] . '" and tb.no_wo = "' . $params['no_wo']['no_wo'] . '"');
                $command = $query->createCommand();
                $stokKeluar = $command->query()->read();

                $sisa_pengambilan = $stokBom['jml'] - $stokKeluar['jml_keluar'];
            }
        }
        $data['sisa_pengambilan'] = $sisa_pengambilan;
        $data['stok_sekarang'] = $stok_sekarang;
        echo json_encode(array('data' => $data));
    }

    public function actionListbbk() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('trans_bbk')
                ->select("no_bbk")
                ->where('no_bbk like "%' . $param['nama'] . '%"')
                ->orderBy('no_bbk DESC')
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
                    ->select('kd_barang, nm_barang, satuan')
                    ->where('kd_barang = "' . $val['kd_barang'] . '"')
                    ->limit(1);
            $command = $query->createCommand();
            $barang = $command->query()->read();

            $det[$i]['jml'] = $val['jml'];
            $det[$i]['satuan'] = $barang['satuan'];
            $det[$i]['ket'] = $val['ket'];
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

    public function actionRekap() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tanggal DESC";
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
                ->from('view_bbk_rekap as rvb')
                ->join('JOIN', 'tbl_karyawan as tbk', 'tbk.nik = rvb.penerima')
                ->join('LEFT JOIN', 'tbl_jabatan as tbj', 'tbj.id_jabatan = tbk.jabatan')
                ->orderBy($sort)
                ->select("rvb.*,tbk.nama,tbj.jabatan");

//                ->from('trans_bbk as trbk')
//                ->join('JOIN','det_bbk as detbk','detbk.no_bbk = trbk.no_bbk')
//                ->join('LEFT JOIN','barang as brg','brg.kd_barang = detbk.kd_barang')
//                ->join('JOIN','tbl_karyawan as tbk','tbk.nik = trbk.penerima')
//                ->join('JOIN','tbl_jabatan as tbj','tbj.id_jabatan = trbk.kd_jab')
//                ->orderBy($sort)
//                ->select("trbk.*,detbk.*,brg.satuan,brg.nm_barang,tbk.nama,tbj.jabatan");
        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tgl_periode') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'rvb.tanggal', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rbbk", ['models' => $models]);
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
