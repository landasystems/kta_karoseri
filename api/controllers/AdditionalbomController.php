<?php

namespace app\controllers;

use Yii;
use app\models\TransAdditionalBom;
use app\models\Bom;
use app\models\Womasuk;
use app\models\DetAdditionalBom;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class AdditionalbomController extends Controller {

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
                    'chassis' => ['get'],
                    'model' => ['get'],
                    'barang' => ['get'],
                    'kode' => ['get'],
                    'tipe' => ['get'],
                    'cari' => ['get'],
                ],
            ]
        ];
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_additional_bom')
                ->select("*")
                ->where(['like', 'kd_bom', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
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

    public function actionChassis() {
        $query = new Query;
        $query->from('chassis')
                ->select("kd_chassis")
                ->where('merk="' . $_GET['merk'] . '" and tipe="' . $_GET['tipe'] . '"');

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode = $models['kd_chassis'];

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "id DESC";
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
                ->from(['trans_additional_bom', 'chassis', 'model'])
                ->where('trans_additional_bom.kd_chassis = chassis.kd_chassis and trans_additional_bom.kd_model=model.kd_model')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();

        foreach ($models as $key => $val) {
            $bom = Bom::findOne($val['kd_bom']);
            $wo = WoMasuk::findOne($val['no_wo']);
            $model = \app\models\ModelKendaraan::findOne($val['kd_model']);
            if (!empty($bom))
                $models[$key]['bom'] = $bom->attributes;
            if (!empty($wo))
                $models[$key]['wo'] = $wo->attributes;
            if(!empty($model))
                $models[$key]['modelKendaraan'] = $model->attributes;
        }
//        Yii::error($models);
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $det = DetAdditionalBom::find()
                ->with(['jabatan', 'barang'])
                ->where(['trans_additional_bom_id' => $id])
                ->all();

        $detail = array();
        foreach ($det as $key => $val) {
            $detail[$key] = $val->attributes;
            $detail[$key]['bagian'] = (isset($val->jabatan)) ? $val->jabatan->attributes : [];
            $detail[$key]['barang'] = (isset($val->barang)) ? $val->barang->attributes : [];
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TransAdditionalBom();
        $model->attributes = $params['tambahItem'];
        $model->kd_model = $params['tambahItem']['modelKendaraan']['kd_model'];

        if ($model->save()) {
            $detailBom = $params['detTambahItem'];
            foreach ($detailBom as $val) {
                $det = new DetAdditionalBom();
                $det->attributes = $val;
                $det->trans_additional_bom_id = $model->id;
                $det->kd_jab = $val['bagian']['id_jabatan'];
                $det->kd_barang = $val['barang']['kd_barang'];
                $det->kd_bom = $model->kd_bom;
                $det->save();
            }
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
        $model->attributes = $params['tambahItem'];
        $model->kd_model = $params['tambahItem']['modelKendaraan']['kd_model'];

        if ($model->save()) {
            $deleteDetail = DetAdditionalBom::deleteAll(['kd_bom' => $model->kd_bom]);
            $detailBom = $params['detTambahItem'];
            foreach ($detailBom as $val) {
                $det = new DetAdditionalBom();
                $det->attributes = $val;
                $det->trans_additional_bom_id = $model->id;
                $det->kd_jab = $val['bagian']['id_jabatan'];
                $det->kd_barang = $val['barang']['kd_barang'];
                $det->kd_bom = $model->kd_bom;
                $det->save();
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = DetAdditionalBom::deleteAll(['trans_additional_bom_id' => $id]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransAdditionalBom::findOne($id)) !== null) {
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