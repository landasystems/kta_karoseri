<?php

namespace app\controllers;

use Yii;
use app\models\Kpb;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class KpbController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'jabkpb' => ['get'],
                    'listbahan' => ['post'],
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

    public function actionJabkpb() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jabatan as tj')
                ->join('JOIN', 'det_standar_bahan as dsb', 'dsb.kd_jab = tj.id_jabatan')
                ->select("tj.*")
                ->where(['dsb.kd_bom' => $param['key']]);
//                ->orWhere(['like', 'id_jabatan', $param['key']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => array_unique($models, SORT_REGULAR)));
    }

    public function actionListbahan() {
//        print_r($_REQUEST);
        $param = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('det_standar_bahan as dsb')
                ->join('Join', 'tbl_jabatan as tj', 'dsb.kd_jab = tj.id_jabatan')
                ->join('Join', 'pekerjaan as p', 'tj.krj = p.kd_kerja')
                ->join('Join', 'barang as b', 'dsb.kd_barang = b.kd_barang')
                ->select("dsb.*, b.nm_barang, b.satuan, p.*")
                ->where(['dsb.kd_bom' => $param['kd_bom'], 'dsb.kd_jab' => $param['kd_jab']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        if (empty($models)) {
            $list = array();
        } else {
            $list = $models;
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => array_unique($list, SORT_REGULAR)));
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

}
?>
