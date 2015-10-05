<?php

namespace app\controllers;

use Yii;
use app\models\TransSpp;
use app\models\DetSpp;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SpprutinController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'rekap' => ['get'],
                    'cari' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'print' => ['get'],
                    'kekurangan' => ['get'],
                    'detail' => ['get'],
                    'excelspp' => ['get'],
                    'kode' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'updatetgl' => ['post'],
                    'delete' => ['delete'],
                    'listbarang' => ['get'],
                    'requiredpurchase' => ['get'],
                    'getdetail' => ['post'],
                    'excelmonitoring' => ['get'],
                    'lock' => ['post'],
                    'unlock' => ['post'],
                ],
            ]
        ];
    }

    public function actionLock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = TransSpp::findOne($key);
            $status->lock = 1;
            $status->save();
        }
    }

    public function actionUnlock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = TransSpp::findOne($key);
            $status->lock = 0;
            $status->save();
        }
    }

    public function actionExcelmonitoring() {
        session_start();
        $query = new Query;
        $query->from('det_spp')
                ->join('JOIN', 'trans_spp', 'trans_spp.no_spp = det_spp.no_spp')
                ->join('LEFT JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('LEFT JOIN', 'view_wo_spk', 'view_wo_spk.no_wo = det_spp.no_wo')
                ->groupBy('trans_spp.no_spp, det_spp.kd_barang')
                ->orderBy('view_wo_spk.no_wo ASC, barang.nm_barang ASC')
                ->select("det_spp.*,barang.nm_barang,barang.satuan, view_wo_spk.nm_customer, trans_spp.tgl_trans as tgl_trans");

        if (isset($_SESSION['filter'])) {
            foreach ($_SESSION['filter'] as $key => $val) {
                if (isset($key) && $key == 'kategori') {
                    if ($val == 'rutin') {
                        $query->andWhere("trans_spp.no_proyek='Rutin'");
                    } elseif ($val == 'nonrutin') {
                        $query->andWhere("trans_spp.no_proyek='Non Rutin'");
                    }
                } else if ($key == 'tgl_periode') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'trans_spp.tgl_trans', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $periode = $_SESSION['periode'];

        return $this->render("/expretur/monitoring", ['models' => $models, 'periode' => $periode]);
    }

    public function actionKekurangan() {
        session_start();
        $query = new Query;
        $query->from('det_spp')
                ->join('JOIN', 'trans_spp', 'trans_spp.no_spp = det_spp.no_spp')
                ->join('LEFT JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
//                ->join('RIGHT JOIN', 'trans_po', 'trans_po.spp = trans_spp.no_spp')
//                ->join('JOIN', 'detail_po', 'detail_po.nota = trans_po.nota and detail_po.kd_barang = barang.kd_barang')
//                ->join('RIGHT JOIN', 'trans_bbm', 'trans_bbm.no_po =  trans_po.nota')
//                ->join('RIGHT JOIN', 'det_bbm', 'det_bbm.no_bbm = trans_bbm.no_bbm and det_bbm.kd_barang = barang.kd_barang')
                ->join('LEFT JOIN', 'view_wo_spk', 'view_wo_spk.no_wo = det_spp.no_wo')
                ->orderBy('view_wo_spk.no_wo ASC, barang.nm_barang ASC')
//                ->select("det_spp.*,barang.nm_barang,barang.satuan,trans_po.nota, det_spp.p as tgl_trans, det_spp.qty as jumlah_spp, det_bbm.jumlah as jumlah_bbm, (det_spp.qty-det_bbm.jumlah) as selisih");
                ->select("det_spp.*,barang.kd_barang as kode_barang,barang.nm_barang,barang.satuan,det_spp.p as tgl_trans, det_spp.qty as jumlah_spp");
        if (isset($_SESSION['filter'])) {
            foreach ($_SESSION['filter'] as $key => $val) {
                if (isset($key) && $key == 'kategori') {
                    if ($val == 'rutin') {
                        $query->andWhere("trans_spp.no_proyek='Rutin'");
                    } elseif ($val == 'nonrutin') {
                        $query->andWhere("trans_spp.no_proyek='Non Rutin'");
                    }
                } else if ($key == 'tgl_periode') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'trans_spp.tgl_trans', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $periode = $_SESSION['periode'];

//        echo json_encode($models);

        return $this->render("/expretur/kekurangan", ['models' => $models, 'periode' => $periode]);
    }

    public function actionCari() {

        $params = $_REQUEST;

        $query = new Query;
        $query->from('trans_spp')
                ->select("no_spp,no_proyek")
                ->andWhere(['like', 'no_spp', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_spp')
                ->select('*')
                ->orderBy('no_spp DESC')
                ->where('year(tgl_trans) = "' . date("Y") . '"')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();

        if (empty($models)) {
            $kode = date("y") . '00001';
        } else {
            $lastKode = substr($models['no_spp'], -3) + 1;
            $kode = date("y") . substr('0000' . $lastKode, -3);
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
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

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_spp DESC";
        $offset = 0;
        $limit = 10;
        //        Yii::error($params);
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
                ->where("no_proyek='Rutin'")
                ->limit($limit)
                ->from('trans_spp')
                ->orderBy($sort)
                ->select("*");

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'barang') {
                    $brg = $this->searchBrg($val);
                    $query->andFilterWhere(['no_spp' => $brg]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }
        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $data = array();
        foreach ($models as $key => $val) {
            $tg1 = explode("/", $val['tgl1']);
            $tg2 = explode("/", $val['tgl2']);
            $tgl1 = $tg1[2] . '-' . $tg1[1] . '-' . $tg1[0];
            $tgl2 = $tg2[2] . '-' . $tg2[1] . '-' . $tg2[0];
            $data[$key] = $val;
            $data[$key]['tgl1'] = $tgl1;
            $data[$key]['tgl2'] = $tgl2;
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function searchBrg($id) {
        $patern = $id;
        $query = new Query;
        $query->from('det_spp')
                ->select("*")
                ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('JOIN', 'trans_spp', 'trans_spp.no_spp= det_spp.no_spp')
                ->where("trans_spp.no_proyek='Rutin'")
                ->select('det_spp.no_spp, barang.nm_barang');
        $query->andFilterWhere(['like', 'barang.nm_barang', $patern]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $data = array();
        foreach ($models as $key) {
            $data[] = $key['no_spp'];
        }
        return $data;
    }

    public function actionRekap() {

        $params = $_REQUEST;
        $filter = array();
        $sort = "tgl_trans DESC";
        $offset = 0;
        $limit = 10;

        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];


        if (isset($params['sort'])) {
            $sort = $params['sort'];
            if (isset($params['order'])) {
                if ($params['order'] == "false")
                    $sort.=" ASC";
                else
                    $sort.=" DESC";
            }
        }

        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->orderBy($sort)
                ->from('det_spp')
                ->join('JOIN', 'trans_spp', 'trans_spp.no_spp = det_spp.no_spp')
                ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('RIGHT JOIN', 'trans_po', 'trans_po.spp = trans_spp.no_spp')
                ->join('RIGHT JOIN', 'detail_po', 'detail_po.nota = trans_po.nota and detail_po.kd_barang = det_spp.kd_barang')
                ->select("det_spp.*,trans_spp.*,barang.kd_barang as kode_barang,barang.nm_barang,barang.satuan, trans_po.nota");

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if (isset($key) && $key == 'kategori') {
                    if ($val == 'rutin') {
                        $query->andWhere("trans_spp.no_proyek='Rutin'");
                    } elseif ($val == 'nonrutin') {
                        $query->andWhere("trans_spp.no_proyek='Non Rutin'");
                    }
                } else if ($key == 'tgl_periode') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'trans_spp.tgl_trans', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }
        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;
        $_SESSION['periode'] = isset($filter['tgl_periode']) ? $filter['tgl_periode'] : '-';
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $query = new Query;
        $query->select("trans_spp.*, det_spp.*, barang.*, jenis_brg.*, det_spp.saldo as sld, det_spp.qty as jmlspp")
                ->from('trans_spp')
                ->join('JOIN', 'det_spp', 'det_spp.no_spp = trans_spp.no_spp')
                ->join('LEFT JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->where('trans_spp.no_spp = "' . $id . '"');

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['nospp'] = $id;
    }

//    public function actionCreate() {
//        $params = json_decode(file_get_contents("php://input"), true);
//        $model = new TransSpp();
//        $model->no_spp = $params['form']['no_spp'];
//        $model->tgl_trans = $tgl_trans;
//        $model->tgl1 = date('d/m/Y', strtotime($params['form']['periode']['startDate']));
//        $model->tgl2 = date('d/m/Y', strtotime($params['form']['periode']['endDate']));
//        $model->no_proyek = 'Rutin';
//
//        if ($model->save()) {
//            foreach ($params['details'] as $val) {
//                $det = new DetSpp();
//                $det->attributes = $val;
//                $det->no_spp = $model->no_spp;
//                $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
//                $det->saldo = $val['barang']['saldo'];
//                $det->p = date('Y-m-d', strtotime($det->p));
//                $det->no_wo = (empty($val['wo']['no_wo'])) ? '-' : $val['wo']['no_wo'];
//                $det->save();
//            }
//            $this->setHeader(200);
//            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
//        } else {
//            $this->setHeader(400);
//            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
//        }
//    }

    public function actionUpdatetgl() {
        $params = json_decode(file_get_contents("php://input"), true);
        foreach ($params['asu'] as $key => $data) {
            $model = DetSpp::findOne($key);
            $model->a = date('Y-m-d', strtotime($params['wip']['a']));
            $model->save();
        }
        //list
        $detSpp = DetSpp::find()
                ->with(['wo', 'barang'])
                ->where(['no_spp' => $params['wip']['nama']['no_spp']])
                ->all();
        $detail = array();
        foreach ($detSpp as $key => $val) {
            $detail[$key] = $val->attributes;
            $detail[$key]['wo'] = (isset($val->wo)) ? $val->wo->attributes : [];
            $detail[$key]['barang'] = (isset($val->barang)) ? $val->barang->attributes : [];
        }
        $this->setHeader(200);
        echo json_encode(['status' => 1, 'details' => $detail]);
    }

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = TransSpp::findOne($params['form']['no_spp']);
        if (empty($model)) {
            $model = new TransSpp();
            $model->no_spp = $params['form']['no_spp'];
        }

        $tgl_trans = date('Y-m-d', strtotime($params['form']['tgl_trans']));
        $model->tgl_trans = $tgl_trans;
        $model->tgl1 = date('d/m/Y', strtotime($params['form']['periode']['startDate']));
        $model->tgl2 = date('d/m/Y', strtotime($params['form']['periode']['endDate']));
        $model->no_proyek = 'Rutin';
        $model->lock = 1;
        if ($model->save()) {
            $deleteAll = DetSpp::deleteAll('no_spp="' . $model->no_spp . '"');
            foreach ($params['details'] as $val) {
                $det = new DetSpp();
                $det->attributes = $val;
                $det->no_spp = $model->no_spp;
                $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
                $det->saldo = $val['barang']['saldo'];
                $det->qty = $val['barang']['qty'];
                $det->p = (!empty($val['p']) ? date('Y-m-d', strtotime($val['p'])) : null);
                $det->no_wo = (empty($val['wo']['no_wo'])) ? '-' : $val['wo']['no_wo'];
                $det->save();
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
        $deleteDetail = DetSpp::deleteAll('no_spp="' . $id . '"');

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransSpp::findOne($id)) !== null) {
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
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excel", ['models' => $models]);
    }

    public function actionListbarang() {
        $query = new Query();
        $query->from('barang')
                ->select("kd_barang,nm_barang");

        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionDetail($id) {
        $model = $this->findModel($id);
        $data = $model->attributes;

        $detSpp = DetSpp::find()
                ->with(['wo', 'barang'])
                ->where(['no_spp' => $id])
                ->all();
        session_start();
        $_SESSION['nospp'] = $id;

        $detail = array();
        foreach ($detSpp as $key => $val) {
            $detail[$key] = $val->attributes;
            $detail[$key]['wo'] = (isset($val->wo)) ? $val->wo->attributes : [];
            $detail[$key]['barang'] = (isset($val->barang)) ? $val->barang->attributes : [];
        }
        $this->setHeader(200);
        echo json_encode(['status' => 1, 'data' => $data, 'details' => $detail]);
    }

    public function actionGetdetail() {
        $params = json_decode(file_get_contents("php://input"), true);
        $detSpp = DetSpp::find()
                ->with(['wo', 'barang'])
                ->where(['no_spp' => $params['nama']['no_spp']])
                ->all();
        $detail = array();
        foreach ($detSpp as $key => $val) {
            $detail[$key] = $val->attributes;
            $detail[$key]['wo'] = (isset($val->wo)) ? $val->wo->attributes : [];
            $detail[$key]['barang'] = (isset($val->barang)) ? $val->barang->attributes : [];
        }
        $this->setHeader(200);
        echo json_encode(['status' => 1, 'details' => $detail]);
    }

    public function actionRequiredpurchase() {
        $model = Barang::find()
                ->where('kat like "rutin%"')
                ->andWhere('qty <= min')
                ->andWhere('saldo < max')
                ->andWhere('min > 0')
                ->all();
        $data = [];
        if (!empty($model)) {
            foreach ($model as $key => $val) {
                $data[$key]['barang'] = $val->attributes;
            }
        }
        $totalItems = count($data);
        $this->setHeader(200);
        echo json_encode(['status' => 1, 'data' => $data, 'count' => $totalItems]);
    }

    public function actionExcelspp() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $query->limit(null);
        $query->offset(null);
        $query->orderBy('trans_po.nota', 'barang.nm_barang');
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekapspp", ['models' => $models, 'filter' => $filter]);
    }

    public function actionPrint() {
        session_start();
        $query = $_SESSION['query'];
        $nospp = $_SESSION['nospp'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/laporanspprutin", ['models' => $models, 'id' => $nospp]);
    }

}
