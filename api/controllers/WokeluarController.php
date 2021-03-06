<?php

namespace app\controllers;

use Yii;
use app\models\Womasuk;
use app\models\Warna;
use app\models\Smalleks;
use app\models\Smallint;
use app\models\Minieks;
use app\models\Miniint;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class WokeluarController extends Controller {

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
                    'delete' => ['post'],
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'cari' => ['get'],
                    'nowo' => ['get'],
                    'warna' => ['post'],
                    'getnowo' => ['post'],
                    'select' => ['post'],
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
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionCari() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from(['customer', 'chassis', 'tbl_karyawan', 'spk', 'serah_terima_in', 'warna', 'model'])
                ->where('chassis.kd_chassis = spk.kd_chassis
                        AND spk.nik = tbl_karyawan.nik
                        AND spk.kd_customer = customer.kd_cust AND serah_terima_in.no_spk = spk.no_spk AND serah_terima_in.kd_warna = warna.kd_warna AND spk.kd_model = model.kd_model')
                ->select("spk.no_spk as no_spk, tbl_karyawan.nama as sales,customer.nm_customer as customer, customer.nm_pemilik as pemilik, chassis.model_chassis as model_chassis,
                        chassis.merk as merk, chassis.tipe as tipe, serah_terima_in.kd_titipan, serah_terima_in.no_chassis, serah_terima_in.no_mesin,
                        serah_terima_in.tgl_terima, warna.warna as warna, model.model")
                ->andWhere(['like', 'spk.no_spk', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionGetnowo() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params['no_spk']['no_spk']);
        $query = new Query;
        $query->from('view_wo_spk as vw')
                ->join('LEFT JOIN', 'wo_masuk', 'wo_masuk.no_wo = vw.no_wo')
                ->join('LEFT JOIN', 'chassis as c', 'c.kd_chassis = vw.kd_chassis')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vw.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->select("vw.*,c.model_chassis,wo_masuk.in_spk_marketing,wo_masuk.tgl_kontrak")
                ->where('vw.no_wo="' . $params['no_wo'] . '" ');
        $command = $query->createCommand();
        $models = $command->queryOne();
        
        $model = Womasuk::find()
                        ->where('no_wo like "%' . $params['no_wo'] . '%" and tgl_keluar is NULL ')
                        ->limit(10)->all();
        $data = array();
        if (!empty($model)) {
            foreach ($model as $key => $val) {
                $models['no_wo'] = $val->attributes;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data'=>$models));
    }

    public function actionWarna() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('warna')
                ->select("*");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'warna' => $models));
    }

    
    public function actionNowo() {
        $params = $_REQUEST;
        $model = Womasuk::find()
                        ->where('no_wo like "%' . $params['nama'] . '%" and tgl_keluar is NULL ')
                        ->limit(10)->all();
        $data = array();
        if (!empty($model)) {
            foreach ($model as $key => $val) {
                $data[] = $val->attributes;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tgl_keluar DESC";
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
                ->from('wo_masuk')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('LEFT JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('LEFT JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('LEFT JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
                ->join('LEFT JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
//                ->join('JOIN', 'serah_terima_in', 'spk.no_spk = serah_terima_in.no_spk') // customer
//                ->join('JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->where('wo_masuk.tgl_keluar IS NOT NULL')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'wo_masuk.' . $key, $val]);
                } elseif ($key == 'nama') {
                    $query->andFilterWhere(['like', 'sales.' . $key, $val]);
                }
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $query = new Query;
        $query->from('wo_masuk')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
                ->join('JOIN', 'serah_terima_in', 'spk.no_spk = serah_terima_in.no_spk') // customer
                ->join('JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->where('wo_masuk.no_wo = "' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $data) {
            $warna = 'dfdf';
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'warna' => $warna), JSON_PRETTY_PRINT);
    }

    public function actionSelect() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($params['no_wo']);
        $query = new Query;
        $query->from('wo_masuk')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
                ->join('LEFT JOIN', 'serah_terima_in', 'spk.no_spk = serah_terima_in.no_spk') // customer
                ->join('LEFT JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->where('wo_masuk.no_wo = "' . $params['no_wo'] . '"')
                ->select("sales.nama as sales,warna.warna as warna, customer.nm_customer as customer, customer.nm_pemilik as pemilik,
                            chassis.model_chassis as model_chassis, chassis.merk as merk, chassis.tipe as tipe, model.model, serah_terima_in.no_chassis as no_rangka,
                            serah_terima_in.no_mesin, chassis.jenis, serah_terima_in.tgl_terima");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $data) {
            $asu['warna'] = (isset($data['warna'])) ? $data['warna'] : '-';
            $asu['customer'] = (isset($data['customer'])) ? $data['customer'] : '-';
            $asu['sales'] = (isset($data['sales'])) ? $data['sales'] : '-';
            $asu['pemilik'] = (isset($data['pemilik'])) ? $data['pemilik'] : '-';
            $asu['model_chassis'] = (isset($data['model_chassis'])) ? $data['model_chassis'] : '-';
            $asu['tipe'] = (isset($data['tipe'])) ? $data['tipe'] : '-';
            $asu['merk'] = (isset($data['merk'])) ? $data['merk'] : '-';
            $asu['model'] = (isset($data['model'])) ? $data['model'] : '-';
            $asu['no_rangka'] = (isset($data['no_rangka'])) ? $data['no_rangka'] : '-';
            $asu['no_mesin'] = (isset($data['no_mesin'])) ? $data['no_mesin'] : '-';
            $asu['jenis'] = (isset($data['jenis'])) ? $data['jenis'] : '-';
            $asu['tgl_terima'] = (isset($data['tgl_terima'])) ? $data['tgl_terima'] : '-';
        }
        if ($asu['jenis'] == "Small Bus") {
            // eksterior
            $eksterior = new Query;
            $eksterior->from('small_eks')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();

            // interior
            $interior = new Query;
            $interior->from('small_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        } else {
            // eksterior
            $eksterior = new Query;
            $eksterior->from('mini_eks')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();

            // interior
            $interior = new Query;
            $interior->from('mini_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        }
        $model['no_spk'] = ['as' => '1111'];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'det' => $asu, 'eksterior' => $models2, 'interior' => $models3), JSON_PRETTY_PRINT);
    }

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
        Yii::error($params);
//        $model = $this->findModel($params['womasuk']['no_wo']);
        $model = WoMasuk::find()->where('no_wo="' . $params['no_wo']['no_wo'] . '"')->one();

        $model->tgl_keluar = date('Y-m-d', strtotime( $params['tgl_keluar']));



        $model->save();
//        if ($model->save()) {

//
//            $this->setHeader(200);
//            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
//        } else {
//            $this->setHeader(400);
//            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
//        }
    }

    public function actionDelete() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = $this->findModel($params['no_wo']);
        $model->tgl_keluar = "";
        $model->save();
    }

    protected function findModel($id) {
        if (($model = Womasuk::findOne($id)) !== null) {
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
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expmaster/barang", ['models' => $models]);
    }

}

?>
