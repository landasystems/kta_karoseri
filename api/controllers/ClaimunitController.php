<?php

namespace app\controllers;

use Yii;
use app\models\DetClaim;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ClaimunitController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view' => ['get'],
                    'excel' => ['get'],
                    'rekap' => ['get'],
                    'char' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['get'],
                    'jeniskomplain' => ['get'],
                    'sisagaransi' => ['post'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
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

    public function actionRekap() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "dc.no_wo DESC";
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
                ->limit($limit)
                ->from('det_claim as dc')
                ->join('JOIN', 'jenis_komplain as jk', 'jk.kd_jns = dc.kd_jns')
                ->join('JOIN', 'view_wo_spk as vws', 'vws.no_wo = dc.no_wo')
                ->join('LEFT JOIN', 'chassis as ch', 'ch.kd_chassis = vws.kd_chassis')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tbk', 'tbk.nik = spk.nik')
                ->orderBy($sort)
                ->select("dc.*, dc.tgl as tgl_claim,jk.*,ch.*,vws.nm_customer,vws.model,tbk.lokasi_kntr,tbk.nama");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if (isset($key) && $key == 'kategori') {
                    if ($val == 'in') {
                        $query->andWhere("jk.stat = 'Interior'");
                    } elseif ($val == 'ex') {
                        $query->andWhere("jk.stat = 'Eksterior'");
                    }
                } else if ($key == 'tgl_periode') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'dc.tgl', $start, $end]);
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
        $_SESSION['filter'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionSisagaransi() {
        $params = json_decode(file_get_contents("php://input"), true);

        $sisa = 0;

        $query = new Query;
        $query->from('delivery')
                ->select("tgl_delivery")
                ->where('no_wo = "' . $params['no_wo'] . '" and status = 1 ');
        $command = $query->createCommand();
        $tglDelivery = $command->query()->read();

        if (!empty($tglDelivery)) {
            $connection = \Yii::$app->db;
            $sql = 'select DATE_ADD( "' . $tglDelivery['tgl_delivery'] . '", INTERVAL 1 YEAR ) as tgl';
            $tglAkhirGaransi = $connection->createCommand($sql)->query()->read();

            $sql = 'select DATEDIFF("' . $tglAkhirGaransi['tgl'] . '", "' . date("Y-m-d") . '") as sisa';
            $s = $connection->createCommand($sql)->query()->read();

            $sisa = $s['sisa'];
        }

        if ($sisa < 0) {
            $sisa = 0;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $sisa,));
    }

    public function actionJeniskomplain() {
        $query = new Query;
        $query->from('jenis_komplain')
                ->select("*");

        if (empty($_GET['bagian']) or $_GET['bagian'] == 'undefined')
            $query->where('stat="' . $_GET['status'] . '"');
        else
            $query->where('stat="' . $_GET['status'] . '" and bag="' . $_GET['bagian'] . '"');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListwo() {
        if (!empty($_GET['kata'])) {
            $query = new Query;
            $query->from('view_wo_spk as vws')
                    ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                    ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                    ->select("vws.*, tk.nama as sales, tk.lokasi_kntr as wilayah");
            $command = $query->createCommand();
            $models = $command->queryAll();

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => $models));
        }
    }

    public function actionView($no_wo) {

        $query = new Query;
        $query->from('det_claim as dc')
                ->join('LEFT JOIN', 'jenis_komplain as jk', 'dc.kd_jns = jk.kd_jns')
                ->join('LEFT JOIN', 'view_wo_spk as vws', 'dc.no_wo = vws.no_wo')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->select("vws.*, jk.*, dc.*, tk.nama as sales, tk.lokasi_kntr as wilayah")
                ->where("dc.no_wo = '" . $no_wo . "'");
        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($models)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        if (isset($params['no_wo'])) {
            $model = new DetClaim();
            $model->attributes = $params;
            $model->tgl = date('Y-m-d', strtotime($params['tgl']));

            if (isset($params['no_wo']['no_wo']))
                $model->no_wo = $params['no_wo']['no_wo'];

            if ($model->save()) {
                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => 'Pastikan no wo telah terisi'), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        if (isset($params['no_wo'])) {
            $model = $this->findModel($id);
            $model->attributes = $params;
            $model->tgl = date('Y-m-d', strtotime($params['tgl']));
            if (isset($params['no_wo']['no_wo']))
                $model->no_wo = $params['no_wo']['no_wo'];

            if ($model->save()) {
                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => 'Pastikan no wo telah terisi'), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = DetClaim::findOne($id)) !== null) {
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
        return $this->render("/expretur/rekapclaim", ['models' => $models, 'filter' => $filter]);
    }

    public function actionChar() {
        session_start();
        $query = $_SESSION['query'];
        $query2 = $query;
        $filter = $_SESSION['filter'];
        if (!empty($filter['tgl_periode'])) {
            $value = explode(' - ', $filter['tgl_periode']);
            $start = date("d/m/Y", strtotime($value[0]));
            $end = date("d/m/Y", strtotime($value[1]));
        } else {
            $start = '';
            $end = '';
        }

        $query2->groupBy("dc.kd_jns")
                ->select("vws.jenis,jk.bag,jk.jns_komplain, count(jk.jns_komplain) as jumlahnya");
//                ->orderBy("jumlahnya desc");

        $command2 = $query2->createCommand();
        $modelnya = $command2->queryAll();

        $query->groupBy("dc.kd_jns")
                ->select("jk.stat,jk.bag,jk.jns_komplain,count(dc.kd_jns) as jumlah");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $ex = array();
        $in = array();
        $e = 0;
        $i = 0;
        foreach ($models as $key => $val) {

            if ($val['stat'] == 'Eksterior') {
                $ex['jns_komplain'][$e] = $val['jns_komplain'] . " (" . $val['bag'] . ")";
                $ex['jumlah'][$e] = (int) $val['jumlah'];
                $e++;
            } else {
                $in['jns_komplain'][$i] = $val['jns_komplain'];
                $in['jumlah'][$i] = (int) $val['jumlah'];
                $i++;
            }
        }

        $sorted = Yii::$app->landa->array_orderby($modelnya, 'jumlahnya', SORT_DESC);


        $sb = [];
        $mb = [];
        $s = 0;
        $m = 0;



        foreach ($sorted as $key => $val) {
//             rsort($val['jumlahnya']);
            if ($val['jenis'] == "Small Bus") {
                IF ($s <= 9) {
                    $sb['jns_komplain'][$s] = $val['jns_komplain'];
                    $sb['jumlahnya'][$s] = (int) $val['jumlahnya'];
                    $s++;
                }
            } else {
                if ($m <= 9) {
                    $mb['jns_komplain'][$m] = $val['jns_komplain'];
                    $mb['jumlahnya'][$m] = (int) $val['jumlahnya'];
                    $m++;
                }
            }
        }
        
        return json_encode(array('Small_Bus' => $sb, 'Mini_Bus' => $mb, 'Interior' => $in, 'Eksterior' => $ex, 'start' => $start, 'end' => $end), JSON_PRETTY_PRINT);
    }

}

?>