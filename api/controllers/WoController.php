<?php

namespace app\controllers;

use Yii;
use app\models\TransPo;
use app\models\DetailPo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class WoController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cari' => ['get'],
                    'wospk' => ['get'],
                    'wospkselesai' => ['get'],
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

    public function actionWospkselesai() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('view_wo_spk as vws')
                ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_wo = vws.no_wo')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->select("vws.*, tk.nama as sales, tk.lokasi_kntr as wilayah, wm.tgl_keluar as tgl_wo_keluar")
                ->where(['like', 'vws.no_wo', $params['nama']])
                ->andWhere('wm.tgl_keluar is not NULL')
                ->limit(20);
        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionWospk() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('view_wo_spk as vws')
                ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_wo = vws.no_wo')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->select("vws.*, tk.nama as sales, tk.lokasi_kntr as wilayah")
                ->where(['like', 'vws.no_wo', $params['nama']])
                ->andWhere('wm.tgl_keluar IS NULL or wm.tgl_keluar="" or wm.tgl_keluar = "0000-00-00"')
                ->orderBy('vws.no_wo DESC')
                ->limit(20);
        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    protected function findModel($id) {
        if (($model = TransPo::findOne($id)) !== null) {
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
        return $this->render("/expmaster/jabatan", ['models' => $models]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('wo_masuk')
                ->select("*")
                ->where(['like', 'no_wo', $params['no_wo']])
                ->andWhere('wm.tgl_keluar IS NULL or wm.tgl_keluar="" or wm.tgl_keluar = "0000-00-00"')
                ->limit(10);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
