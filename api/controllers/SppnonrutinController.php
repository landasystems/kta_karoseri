<?php

namespace app\controllers;

use Yii;
use app\models\TransSpp;
use app\models\DetSpp;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SppnonrutinController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'cari' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'detail' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'listbarang' => ['get'],
                ],
            ]
        ];
    }

    public function actionCari() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_spp')
                ->select("no_spp,no_proyek")
                ->andWhere(['like', 'no_spp', $params['nama']]);

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
//        Yii::error($allowed);

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
        $sort = "tgl_trans DESC";
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
                ->where("no_proyek='Non Rutin'")
                ->limit($limit)
                ->from('trans_spp')
                ->orderBy($sort)
                ->select("*");

        //filter
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
//        Yii::error($params);
        $model = new TransSpp();
        $tgl_trans = date('Y-m-d', strtotime($params['form']['tgl_trans']));
        $lastNumber = TransSpp::find()
                ->where('year(tgl_trans)="' . $tgl_trans . '"')
                ->orderBy('no_spp DESC')
                ->one();
        $number = (empty($lastNumber)) ? 1 : (int) substr($lastNumber->no_spp, 3) + 1;
        $model->no_spp = date('y', $tgl_trans) . substr("000" . $number, -3);
        $model->tgl_trans = $tgl_trans;
        $model->tgl1 = date('Y-m-d', strtotime($params['form']['periode']['startDate']));
        $model->tgl2 = date('Y-m-d', strtotime($params['form']['periode']['endDate']));
        $model->no_proyek = 'Non Rutin';

        if ($model->save()) {
            foreach ($params['details'] as $val) {
                $det = new DetSpp();
                $det->attributes = $val;
                $det->no_spp = $model->no_spp;
                $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
                $det->saldo = $val['barang']['saldo'];
                $det->p = date('Y-m-d', strtotime($det->p));
                $det->no_wo = (empty($val['wo']['no_wo'])) ? '-' : $val['wo']['no_wo'];
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
//        $model->attributes = $params;
        $tgl_trans = date('Y-m-d', strtotime($params['form']['tgl_trans']));
        $model->tgl_trans = $tgl_trans;
        $model->tgl1 = date('Y-m-d', strtotime($params['form']['periode']['startDate']));
        $model->tgl2 = date('Y-m-d', strtotime($params['form']['periode']['endDate']));
        $model->no_proyek = 'Non Rutin';
//        Yii::error($params);
        if ($model->save()) {
            $deleteAll = DetSpp::deleteAll('no_spp="' . $model->no_spp . '"');
            foreach ($params['details'] as $val) {
                $det = new DetSpp();
                $det->attributes = $val;
                $det->no_spp = $model->no_spp;
                $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
                $det->saldo = $val['barang']['saldo'];
                $det->p = date('Y-m-d', strtotime($det->p));
                $det->no_wo = (empty($val['wo']['no_wo'])) ? '-' : $val['wo']['no_wo'];
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
        $deleteDetail = DetSpp::deleteAll('no_spp="'.$id.'"');
        
        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransSpp::findOne($id)) !== null) {
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
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excel", ['models' => $models]);
    }

    public function actionListbarang() {
        $query = new Query();
        $query->from('barang')
                ->select("kd_barang,nm_barang");

        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionDetail($id) {
        $detSpp = DetSpp::find()
                ->with(['wo', 'barang'])
                ->where(['no_spp' => $id])
                ->all();
        $detail = array();
        foreach ($detSpp as $key => $val) {
            $detail[$key] = $val->attributes;
            $detail[$key]['wo'] = (isset($val->wo)) ? $val->wo->attributes : [];
            $detail[$key]['barang'] = (isset($val->barang)) ? $val->barang->attributes : [];
        }
        $this->setHeader(200);
        echo json_encode(['status' => 1, 'details' => $detail]);
    }

}
