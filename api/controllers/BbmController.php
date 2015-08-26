<?php

namespace app\controllers;

use Yii;
use app\models\TransBbm;
use app\models\DetBbm;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BbmController extends Controller {

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
                    'excel' => ['get'],
                    'excelrekap' => ['get'],
                    'rekap' => ['get'],
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
        $query->from('trans_bbm')
                ->select('*')
                ->orderBy('no_bbm DESC')
                ->limit(1);

        $cek = TransBbm::findOne('no_bbm = "BM' . date("y") . '0001"');
        if (empty($cek)) {
            $command = $query->createCommand();
            $models = $command->query()->read();
            $urut = substr($models['no_bbm'], 4) + 1;
            $kode = substr('0000' . $urut, strlen($urut));
            $kode = "BM" . date("y") . $kode;
        } else {
            $kode = "BM" . date("y") . "0001";
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_bbm DESC";
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
                ->from('trans_bbm as tb')
                ->join('LEFT JOIN', 'supplier as su', 'tb.kd_suplier= su.kd_supplier')
//                ->leftJoin('tbl_jabatan as tj', 'tj.id_jabatan  = tb.kd_jab')
                ->orderBy($sort)
                ->select("tb.*,su.nama_supplier as nm_supplier");

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

    public function actionRekap() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "db.no_bbm ASC";
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
                ->from('det_bbm as db')
                ->join('JOIN', 'trans_bbm as tb', 'tb.no_bbm= db.no_bbm')
                ->join('LEFT JOIN', 'supplier as su', 'tb.kd_suplier= su.kd_supplier')
                ->join('LEFT JOIN', 'trans_po as po', 'tb.no_po= po.nota')
                ->join('LEFT JOIN', 'trans_spp as spp', 'spp.no_spp= po.spp')
                ->join('JOIN', 'barang', 'barang.kd_barang = db.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg as jb', 'barang.jenis = jb.kd_jenis')
                ->orderBy($sort)
                ->select("spp.no_spp,po.nota,tb.tgl_nota as tanggal_nota, db.no_bbm as no_bbm, barang.kd_barang as kd_barang, barang.nm_barang,
                    barang.satuan, db.jumlah as jumlah, tb.surat_jalan, db.no_po, su.nama_supplier, db.keterangan");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tgl_nota') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'tb.tgl_nota', $start, $end]);
                } elseif ($key == 'nama_supplier') {
                    $query->andFilterWhere(['like', 'su.' . $key, $val]);
                } elseif ($key == 'no_bbm') {
                    $query->andFilterWhere(['like', 'tb.' . $key, $val]);
                } elseif ($key == 'nm_barang') {
                    $query->andFilterWhere(['like', 'barang.' . $key, $val]);
                }
            }
        }
        Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $data = $model->attributes;
        $querySup = new Query;
        $querySup->select("*")
                ->from('supplier')
                ->where('kd_supplier="' . $model->kd_suplier . '"');
        $commandSup = $querySup->createCommand();
        $sup = $commandSup->queryOne();

        $queryWo = new Query;
        $queryWo->from('wo_masuk')
//                ->select('id_jabatan, jabatan')
                ->where('no_wo = "' . $model->no_wo . '"')
                ->limit(1);
        $command2 = $queryWo->createCommand();
        $wo = $command2->queryOne();
        $queryDet = new Query;
        $queryDet->from('det_bbm')
                ->select('det_bbm.*, det_bbm.no_po as po')
                ->where('no_bbm = "' . $model->no_bbm . '"');
        $commandDet = $queryDet->createCommand();
        $detail = $commandDet->queryAll();

        foreach ($detail as $key => $ab) {
            $queryBrg = new Query;
            $queryBrg->from('barang')
                    ->select('*')
                    ->where('kd_barang = "' . $ab['kd_barang'] . '"');
            $commandBrg = $queryBrg->createCommand();
            $Brg = $commandBrg->queryOne();
            $detail[$key]['barang'] = $Brg;

//            $query = DetBbm::find()->
//                    where('no_bbm="' . $ab['no_bbm'] . '"')
//                    ->limit(1)
//                    ->one();
//            if (!empty($query)) {
//                $detail[$key]['po'] = $query->attributes;
//            }
        }

//        Yii::error($detail);
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'sup' => $sup, 'wo' => $wo, 'details' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = new TransBbm();
        $model->attributes = $params['form'];
        $findNumber = TransBbm::find()->orderBy('no_bbm DESC')->one();
//         Yii::error();
        $lastNumber = (int) substr($findNumber->no_bbm, -5);
        $model->no_bbm = 'BM' . date('y', strtotime($model->tgl_nota)) . substr('00000' . ($lastNumber + 1), -5);
        $model->kd_suplier = $params['form']['kd_supplier'];
        $model->no_wo = $params['form']['wo']['no_wo'];

        if ($model->save()) {
            $detailBbm = $params['detBbm'];
            foreach ($detailBbm as $val) {
                $det = new DetBbm();
                $det->attributes = $val;
                $det->kd_barang = $val['barang']['kd_barang'];
                $det->no_po = $params['form']['po']['nota'];
                $det->no_bbm = $model->no_bbm;
                $det->save();

                //update stok barang
                $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                $barang->saldo += $det->jumlah;
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
        $model = $this->findModel($id);
        $model->attributes = $params['form'];
        $model->kd_suplier = $params['form']['supplier']['kd_supplier'];
        $model->no_wo = $params['form']['wo']['no_wo'];

        if ($model->save()) {
            $detailBbm = $params['detBbm'];
            foreach ($detailBbm as $val) {
                $det = DetBbm::findOne($val['id']);
                if (empty($det)) {
                    $det = new DetBbm();
                }
                $det->attributes = $val;
                $det->kd_barang = $val['barang']['kd_barang'];
                $det->no_po = (isset($params['form']['po']['nota'])) ? $params['form']['po']['nota'] : '-';
                $det->no_bbm = $model->no_bbm;
                $det->save();

                //update stok barang
                $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                $barang->saldo += $det->jumlah;
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
        $model = $this->findModel($id);
        if ($model->delete()) {
            // mengembalikan stok barang
            $detail = DetBbm::find()->where('no_bbm = "' . $model->no_bbm . '"')->all();
            foreach ($detail as $detbbk) {
                $barang = Barang::find()->where('kd_barang="' . $detbbk->kd_barang . '"')->one();
                $barang->saldo -= $detbbk->jml;
                $barang->save();
            }

            //hapus detail bbk
            $delBbm = DetBbm::deleteAll('no_bbm = "' . $id . '"');

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransBbm::findOne($id)) !== null) {
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/bbm", ['models' => $models,'filter'=>$filter]);
    }
    public function actionExcelrekap() {
        session_start();
        $query = $_SESSION['query'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekapbbm", ['models' => $models]);
    }

}

?>
