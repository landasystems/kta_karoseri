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
                    'delete' => ['delete'],
                    'no_wo' => ['post'],
                    'det_nowo' => ['post'],
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
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionNo_wo() {
        $query = new Query;
        $query->from('wo_masuk')
                ->select('*');

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'no_wo' => $models));
    }
 

    public function actionDet_nowo() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query2 = new Query;
        $query2->from('wo_masuk')
                ->where('no_wo = "'.$params['no_wo'].'" ')
                ->select("no_spk");
        $command2 = $query2->createCommand();
        $models2 = $command2->query()->read();

        $query = new Query;
        $query->from('spk')
                ->join('JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis')
                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model')
                ->join('JOIN', 'tbl_karyawan', 'spk.nik = tbl_karyawan.nik')
                ->where('spk.no_spk="' . $models2['no_spk'] . '"')
                ->select('chassis.merk as merk, model.model as model,tbl_karyawan.nama as sales');
//                ->orderBy('no_wo DESC')
//                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        $smodel = $models['model'];
        $merk = $models['merk'];
        $sales = $models['sales'];

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'model' => $smodel,'merk'=>$merk,'sales'=>$sales));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "delivery.no_wo ASC";
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
                ->join('JOIN', 'wo_masuk', 'delivery.no_wo = wo_masuk.no_wo')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
                ->join('JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->orderBy($sort)
                ->select("delivery.*, chassis.merk as merk, model.model as model");

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

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Delivery();
        $model->attributes = $params;

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

}

?>