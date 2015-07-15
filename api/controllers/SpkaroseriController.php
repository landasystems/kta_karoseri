<?php

namespace app\controllers;

use Yii;
use app\models\Spkaroseri;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SpkaroseriController extends Controller {

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
                    'kode' => ['post'],
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

    public function actionKode() {
        $tipe = json_decode(file_get_contents("php://input"), true);
        if ($tipe['tipe'] == "finish") {
            $query = new Query;
            $query->from('spk')
                    ->select('no_spk')
                    ->orderBy('no_spk DESC')
                    ->limit(1);

            $cek = Spkaroseri::findOne('no_spk = "O' . date("y") . '01"');
            if (empty($cek)) {
                $command = $query->createCommand();
                $models = $command->query()->read();
                $urut = substr($models['no_spk'], 2) + 1;
                $kode = substr('00' . $urut, strlen($urut));
                $kode = "O" . date("y") . $kode;
            } else {
                $kode = "O" . date("y") . "01";
            }
        } else if ($tipe['tipe'] == "stok") {
            $query = new Query;
            $query->from('spk')
                    ->select('no_spk')
                    ->orderBy('no_spk DESC')
                    ->limit(1);

            $cek = Spkaroseri::findOne('no_spk = "S' . date("y") . '01"');
            if (empty($cek)) {
                $command = $query->createCommand();
                $models = $command->query()->read();
                $urut = substr($models['no_spk'], 2) + 1;
                $kode = substr('00' . $urut, strlen($urut));
                $kode = "S" . date("y") . $kode;
            } else {
                $kode = "S" . date("y") . "01";
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_spk ASC";
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
                ->from('spk as s')
                ->join('Join', 'chassis as c', 's.kd_chassis = c.kd_chassis')
                ->join('Join', 'customer cus', 'cus.kd_cust = s.kd_customer')
                ->orderBy($sort)
                ->select("s.*, c.*, cus.*");

//        $models = Spkaroseri::find()->with(['chassis', 'customer'])
//                ->offset($offset)
//                ->orderBy($sort)
//                ->select("spk.*, chassis.*, customer.*")
//                ->find()
//                ->all();
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
//        $totalItems = 0;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $query = new Query;
        $query->from('spk as s')
                ->join('Join', 'chassis as c', 's.kd_chassis = c.kd_chassis')
                ->join('Join', 'customer cus', 'cus.kd_cust = s.kd_customer')
                ->join('Join', 'model m', 'm.kd_model= s.kd_model')
                ->join('Join', 'tbl_karyawan tb', 's.nik= tb.nik')
                ->where('s.no_spk="' . $id . '"')
                ->select("s.*, c.merk, c.tipe, cus.kd_cust, cus.nm_customer , tb.nik, tb.nama");
        $command = $query->createCommand();
        $models = $command->query()->read();

        $models['kd_customer'] = array('kd_cust' => $models['kd_cust'], 'nm_customer' => $models['nm_customer']);
        $models['kd_bom'] = array('kd_bom' => $models['kd_bom']);
        $models['kd_model'] = array('kd_model' => $models['kd_model']);
        $models['nik'] = array('nik' => $models['nik'], 'nama' => $models['nama']);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($models)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Spkaroseri();
        $model->attributes = $params;
        $model->kd_customer = $params['kd_customer']['kd_cust'];
        $model->nik = $params['nik']['nik'];
        $model->kd_bom = $params['kd_bom']['kd_bom'];
        $model->kd_model = $params['kd_model']['kd_model'];

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
        $model->kd_customer = $params['kd_customer']['kd_cust'];
        $model->nik = $params['nik']['nik'];
        $model->kd_bom = $params['kd_bom']['kd_bom'];
        $model->kd_model = $params['kd_model']['kd_model'];

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
        if (($model = Spkaroseri::find()->where('no_spk = "' . $id . '"')->one()) !== null) {
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
