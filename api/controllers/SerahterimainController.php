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
                    'warna' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'cari' => ['get'],
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

    public function actionCari() {
        $params = $_REQUEST;
        $model = Serahterimain::find()
                        ->where('kd_titipan like "%' . $params['nama'] . '%"')
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
        $sort = "se.tgl_terima DESC";
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
                ->join('LEFT JOIN', 'customer as cu', 'se.kd_cust = cu.kd_cust')
                ->join('LEFT JOIN', 'warna as wa', 'se.kd_warna = wa.kd_warna')
                ->join('LEFT JOIN', 'chassis as ch', 'se.kd_chassis= ch.kd_chassis')
                ->join('LEFT JOIN', 'view_wo_spk as vi', 'vi.kd_titipan= se.kd_titipan')
                ->orderBy($sort)
                ->select("se.*,cu.*,wa.*,ch.*,vi.no_wo as no_wo,wa.id as warna_id, wa.warna as warna_chassis");

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

        foreach ($models as $key => $val) {
            $models[$key]['spk'] = array('no_spk' => $val['no_spk']);
            $models[$key]['customer'] = array('kd_Cust' => $val['kd_cust'], 'nm_customer' => $val['nm_customer']);
            $models[$key]['chassis'] = array('kd_chassis' => $val['kd_chassis']);
            $models[$key]['warna'] = array('id' => $val['warna_id'], 'kd_warna' => $val['kd_warna'], 'warna' => $val['warna']);
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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

        $model->attributes = $params;
        $model->status = 0;

        $warna = \app\models\Warna::find()->where('kd_warna="' . $params['warna']['kd_warna'] . '"')->one();
        if (empty($warna)) {
            $warna = new \app\models\Warna();
            $warna->attributes = $params;
            $warna->save();
        }
        $model->kd_warna = $warna->kd_warna;
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
        $warna = \app\models\Warna::findOne($params['warna']['kd_warna']);
        if (empty($warna)) {
            $warna = new \app\models\Warna();
        }
        $warna->attributes = $params;
        if ($warna->save()) {
            $model->kd_warna = $warna->kd_warna;
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
