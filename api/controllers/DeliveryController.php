<?php

namespace app\controllers;

use Yii;
use app\models\Delivery;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class DeliveryController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'customer' => ['post'],
                    'delete' => ['delete'],
                    'no_wo' => ['post'],
                    'det_nowo' => ['get'],
                    'rekap' => ['get'],
                    'kode' => ['get'],
                    'excel' => ['get'],
                    'upload' => ['post'],
                    'removegambar' => ['post'],
                ],
            ]
        ];
    }

    public function actionUpload() {
        if (!empty($_FILES)) {
            $tempPath = $_FILES['file']['tmp_name'];
            $newName = \Yii::$app->landa->urlParsing($_FILES['file']['name']);

            $uploadPath = \Yii::$app->params['pathImg'] . $_GET['folder'] . DIRECTORY_SEPARATOR . $newName;

            move_uploaded_file($tempPath, $uploadPath);
            $a = \Yii::$app->landa->createImg($_GET['folder'] . '/', $newName, $_POST['kode']);

            $answer = array('answer' => 'File transfer completed', 'name' => $newName);
            if ($answer['answer'] == "File transfer completed") {
                $delivery = Delivery::findOne($_POST['kode']);
                $foto = json_decode($delivery->foto, true);
                $foto[] = array('name' => $newName);
                $delivery->foto = json_encode($foto);
                $delivery->save();
            }

            echo json_encode($answer);
        } else {
            echo 'No files';
        }
    }

    public function actionRemovegambar() {
        $params = json_decode(file_get_contents("php://input"), true);
        $delivery = Delivery::findOne($params['kode']);
        $foto = json_decode($delivery->foto, true);
        foreach ($foto as $key => $val) {
            if ($val['name'] == $params['nama']) {
                unset($foto[$key]);
                \Yii::$app->landa->deleteImg('delivery/', $params['kode'], $params['nama']);
            }
        }
        $delivery->foto = json_encode($foto);
        $delivery->save();

        echo json_encode($foto);
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
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionCustomer() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('view_wo_spk as vw')
                ->join('LEFT JOIN', 'customer as cu', 'vw.kd_cust = cu.kd_cust')
                ->where('vw.no_wo="' . $params['no_wo'] . '"')
                ->select("vw.kd_cust, vw.nm_customer,cu.alamat1");

        $command = $query->createCommand();
        $models = $command->query()->read();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'customer' => $models));
    }

    public function actionKode() {
        $filter_name = "DU-" . date("y");
        $query = new Query;
        $query->from('delivery')
                ->select("no_delivery")
                ->where(['SUBSTR(no_delivery,1,5)' => $filter_name])
                ->orderBy('no_delivery DESC')
                ->limit(1);
        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = (substr($models['no_delivery'], 5) + 1);
        $kode = $filter_name.substr('000' . $kode_mdl, strlen($kode_mdl));
//        $kode = $filter_name . $kode_mdl;
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "delivery.id DESC";
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
                ->from('delivery')
                ->join('LEFT JOIN', 'wo_masuk', 'delivery.no_wo = wo_masuk.no_wo')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('LEFT JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
                ->join('LEFT JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->join('LEFT JOIN', 'tbl_karyawan', 'tbl_karyawan.nik = spk.nik')
                ->join('LEFT JOIN', 'serah_terima_in', 'serah_terima_in.kd_titipan = wo_masuk.kd_titipan')
                ->join('LEFT JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna')
                ->orderBy($sort)
                ->select("delivery.*, chassis.merk as merk, model.model as model, tbl_karyawan.nama as sales,serah_terima_in.no_mesin,serah_terima_in.no_chassis,warna.warna");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'nama') {
                    $query->andFilterWhere(['like', 'tbl_karyawan.' . $key, $val]);
                } elseif ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        foreach ($models as $key => $val) {
            $customer = \app\models\Customer::findOne($val['kd_cust']);
            if ($val['tujuan'] == "customer") {
                $models[$key]['customer'] = (!empty($customer)) ? $customer->attributes : array();
            } else {
                $models[$key]['customer'] = ['nm_customer' => "", 'alamat1' => ""];
            }
            $nowo = \app\models\Womasuk::findOne($val['no_wo']);
            $models[$key]['nowo'] = (!empty($nowo)) ? $nowo->attributes : array();
        }

        $data = array();
        foreach ($models as $key => $val) {
            $data[$key] = $val;
            $data[$key]['foto'] = json_decode($val['foto'], true);
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "dev.tgl_delivery ASC";
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
                ->from('delivery as dev')
                
                ->join('LEFT JOIN', 'view_wo_spk as vws', 'dev.no_wo = vws.no_wo')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'customer', 'spk.kd_customer = customer.kd_cust')
                ->join('left JOIN ', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->join('left JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
                ->join('left JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->where('tk.department="DPRT005"')
                ->orderBy($sort)
                ->select("dev.*, tk.lokasi_kntr,tk.nama,tk.nik, spk.jml_unit, customer.nm_customer, model.model, chassis.merk, chassis.tipe, chassis.jenis");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tgl_delivery') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'dev.tgl_delivery', $start, $end]);
                } elseif ($key == 'no_delivery') {
                    $query->andFilterWhere(['like', 'dev.' . $key, $val]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'dev.' . $key, $val]);
                } elseif ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                }
            }
        }
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
        $query = new Query;
        $query->from('view_wo_spk')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = view_wo_spk.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->join('JOIN', 'model', 'model.kd_model = view_wo_spk.kd_model')
                ->select("view_wo_spk.*, model.model as model, tk.nama as sales")
                ->where("no_wo='" . $model['no_wo'] . "'");

        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $val) {
            $nowo = (isset($val['no_wo'])) ? $val['no_wo'] : '';
            $merk = (isset($val['merk'])) ? $val['merk'] : '';
            $jancok = (isset($val['model'])) ? $val['model'] : '';
            $cok = (isset($val['sales'])) ? $val['sales'] : '';


            $model['no_wo'] = ['no_wo' => $nowo, 'merk' => $merk, 'model' => $jancok, 'sales' => $cok];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = new Delivery();
        $model->attributes = $params;
        $model->tgl_delivery = date('Y-m-d', strtotime($params['tgl_delivery']));
        if ($params['tujuan'] == 'customer') {
            $model->kd_cust = $params['kd_cust'];
            $model->cabang = "";
        } else {
            $model->kd_cust = "";
            $model->cabang = $params['cabang'];
        }
        if ($model->tujuan == "customer") {
            $model->status = 1;
        } else {
            $model->status = 0;
        }

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        \Yii::error($params);
        $model = $this->findModel($id);
        $model->attributes = $params;
        $model->tgl_delivery = date('Y-m-d', strtotime($params['tgl_delivery']));
        if ($params['tujuan'] == 'customer') {
            $model->kd_cust = $params['kd_cust'];
            $model->cabang = "";
        } else {
            $model->kd_cust = "";
            $model->cabang = $params['cabang'];
        }
//        $model->no_wo = $params['no_wo']['no_wo'];
        if ($model->tujuan == "customer") {
            $model->status = 1;
        } else {
            $model->status = 0;
        }

        if ($model->save()) {
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
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Delivery::findOne($id)) !== null) {
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
        return $this->render("/expretur/deliveryunit", ['models' => $models, 'filter' => $filter]);
    }

}

?>
