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
            $query = new Query;
            $query->select('SELECT DATE_ADD("' . $tglDelivery . '", INTERVAL 1 YEAR) as tgl');
            $tglAkhirGaransi = $query->createCommand()->query()->read();

            $query->select('SELECT DATEDIFF("' . $tglDelivery . '", "' . date("Y-m-d") . '") as sisa');
            $s = $query->createCommand()->query()->read();

            $sisa = $s['sisa'];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $sisa,));
    }

    public function actionJeniskomplain() {
        $query = new Query;
        $query->from('jenis_komplain')
                ->select("*")
                ->where('stat="' . $_GET['status'] . '" and bag="' . $_GET['bagian'] . '"');

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
        $model = new DetClaim();
        $model->attributes = $params;

        if (isset($params['no_wo']['no_wo']))
            $model->no_wo = $params['no_wo']['no_wo'];

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
        $model = $this->findModel($id);
        $model->attributes = $params;

        if (isset($params['no_wo']['no_wo']))
            $model->no_wo = $params['no_wo']['no_wo'];

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

}

?>
