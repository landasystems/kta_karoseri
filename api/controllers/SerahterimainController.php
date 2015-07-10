<?php

namespace app\controllers;

use Yii;
use app\models\Serahterimain;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SerahterimainController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'spk' => ['get'],
                    'chassis' => ['get'],
                    'customer' => ['get'],
                    'warna' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
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

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tgl_terima DESC";
        $offset = 0;
        $limit = 10;

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
                ->from('serah_terima_in as se')
                ->join('JOIN', 'customer as cu', 'se.kd_cust = cu.kd_cust')
                ->join('JOIN', 'warna as wa', 'se.kd_warna = wa.kd_warna')
                ->join('JOIN', 'chassis as ch', 'se.kd_chassis= ch.kd_chassis')
                ->orderBy($sort)
                ->select("se.*,cu.*,wa.*,ch.*");

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

    public function actionSpk() {
        $query = new Query;
        $query->from('spk')
                ->select("no_spk");
        $command = $query->createCommand();
        $models = $command->queryAll();
        echo json_encode(array('status' => 1, 'kd_spk' => $models));
    }

    public function actionChassis() {
        $query = new Query;
        $query->from('chassis')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryAll();
        echo json_encode(array('status' => 1, 'list_chassis' => $models));
    }

    public function actionCustomer() {
        $query = new Query;
        $query->from('customer')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryAll();
        echo json_encode(array('status' => 1, 'list_customer' => $models));
    }

    public function actionWarna() {
        $query = new Query;
        $query->from('warna')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryAll();
        echo json_encode(array('status' => 1, 'list_warna' => $models));
    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = Serahterimain::find()->where('kd_titipan="' . $params['kd_titipan'] . '"')->one();
        if (empty($model)) {
            $model = new Serahterimain();
        }
        Yii::error(date('Y-m-d', strtotime('+1 days', strtotime(substr($params['tgl_terima'], 0, 10)))));
        $model->attributes = $params;
//        $model->tgl_terima = date('Y-m-d',  strtotime('+1 day',substr($params['tgl_terima'], 0,10)));
//        $model->serah_terima = date('Y-m-d',  strtotime('+1 day',substr($params['serah_terima'], 0,10)));
//        $model->tgl_prd = date('Y-m-d',  strtotime('+1 day',substr($params['tgl_prd'], 0,10)));
//        $model->tgl_pdc = date('Y-m-d',  strtotime('+1 day',substr($params['tgl_pdc'], 0,10)));

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
        if (($model = Serahterimain::find(array('condition' => 'kd_titipan="' . $id . '"'))->one()) !== null) {
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
