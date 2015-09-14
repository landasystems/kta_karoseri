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
                ],
            ]
        ];
    }

    public function actionExcelmonitoring() {
        session_start();
        $query = $_SESSION['query'];
        $query->join('LEFT JOIN', 'trans_bbm', 'trans_bbm.no_po = trans_po.nota');
//        $query->join('LEFT JOIN', 'det_bbm', 'det_bbm.kd_barang = det_spp.kd_barang and det_bbm.no_po = trans_po.no_po');
        $query->limit(null);
        $query->offset(null);
        $query->select("trans_spp.*, det_spp.*, trans_po.tanggal as tgl_pch,barang.nm_barang, trans_bbm.tgl_nota as tgl_realisasi");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $periode = $_SESSION['periode'];

        return $this->render("/expretur/monitoring", ['models' => $models, 'periode' => $periode]);
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
        $sort = "tgl_trans DESC";
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

                $query->andFilterWhere(['like', 'trans_spp.' . $key, $val]);
            }
        }
        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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
                ->from('trans_spp')
                ->join('JOIN', 'det_spp', 'trans_spp.no_spp = det_spp.no_spp')
                ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('JOIN', 'view_wo_spk', 'view_wo_spk.no_wo = det_spp.no_wo')
                ->join('JOIN', 'trans_po', 'trans_po.spp = trans_spp.no_spp')
                ->select("det_spp.*,trans_spp.*,barang.nm_barang,barang.satuan,trans_po.nota,view_wo_spk.nm_customer");

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

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = new TransSpp();
        $model->no_spp = $params['form']['no_spp'];
        $model->tgl_trans = $tgl_trans;
        $model->tgl1 = date('Y-m-d', strtotime($params['form']['periode']['startDate']));
        $model->tgl2 = date('Y-m-d', strtotime($params['form']['periode']['endDate']));
        $model->no_proyek = 'Rutin';

        if ($model->save()) {
            foreach ($params['details'] as $val) {
                $det = new DetSpp();
                $det->attributes = $val;
                $det->no_spp = $model->no_spp;
                $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
                $det->saldo = $val['barang']['saldo'];
                $det->p = date('Y-m-d', strtotime($det->p));
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

    public function actionUpdatetgl() {
        $params = json_decode(file_get_contents("php://input"), true);
        \Yii::error($params);
        foreach ($params['asu'] as $key => $data) {
//            $model = DetSpp::find(['no_spp' => $params['wip']['nama']['no_spp'], 'kd_barang' => $key])->all();
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
//        $model->attributes = $params;
        if (empty($model)) {
            $model = new TransSpp();
            $model->no_spp = $params['form']['no_spp'];
        }

        $tgl_trans = date('Y-m-d', strtotime($params['form']['tgl_trans']));
        $model->tgl_trans = $tgl_trans;
        $model->tgl1 = date('Y-m-d', strtotime($params['form']['periode']['startDate']));
        $model->tgl2 = date('Y-m-d', strtotime($params['form']['periode']['endDate']));
        $model->no_proyek = 'Rutin';
//        Yii::error($params);
        if ($model->save()) {
            $deleteAll = DetSpp::deleteAll('no_spp="' . $model->no_spp . '"');
            foreach ($params['details'] as $val) {
                $det = new DetSpp();
                $det->attributes = $val;
                $det->no_spp = $model->no_spp;
                $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
                $det->saldo = $val['barang']['saldo'];
                $det->qty = $val['barang']['qty'];
                $det->p = date('Y-m-d', strtotime($det->p));
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
        echo json_encode(['status' => 1, 'details' => $detail]);
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
//         Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekapspp", ['models' => $models, 'filter' => $filter]);
    }

    public function actionPrint() {
        session_start();
        $nospp = $_SESSION['nospp'];

        $query = new Query;
        $query->where("det_spp.no_spp='" . $nospp . "'")
                ->from('det_spp')
                ->join('JOIN', 'trans_spp', 'trans_spp.no_spp = det_spp.no_spp')
                ->join('LEFT JOIN', 'trans_po', 'trans_po.spp = trans_spp.no_spp')
                ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->select("det_spp.*,trans_spp.*,jenis_brg.jenis_brg,barang.min,barang.max,barang.nm_barang,barang.satuan,trans_po.nota");

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/laporanspprutin", ['models' => $models, 'id' => $nospp]);
    }

}
