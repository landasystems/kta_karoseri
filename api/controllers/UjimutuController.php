<?php

namespace app\controllers;

use Yii;
use app\models\Ujimutu;
use app\models\DetUjimutu;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class UjimutuController extends Controller {

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
                    'kode' => ['get'],
                    'det_nowo' => ['get'],
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
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('view_wo_spk as vws')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->join('LEFT JOIN', 'model', 'vws.kd_model = model.kd_model')
                ->select("vws.*, tk.nama as sales, model.model as model, tk.nama as sales")
                ->andWhere(['like', 'vws.no_wo', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }
    public function actionKode() {
        $query = new Query;
        $query->from('trans_uji_mutu')
                ->select('*')
                ->orderBy('kd_uji DESC')
                ->where('year(tgl) = "' . date("Y") . '"')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();

        if (empty($models)) {
            $kode = 'UJ' . date("y") . '0001';
        } else {
            $lastKode = substr($models['kd_uji'], -4) + 1;
            $kode = 'UJ' . date("y") . substr('000' . $lastKode, -4);
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "kd_uji DESC";
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
                ->from('trans_uji_mutu')
//                ->where('barang.jenis = jenis_brg.kd_jenis')
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
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $query = new Query;
        $query->from('det_uji_mutu as det')
                ->join('JOIN', 'view_wo_spk as vms', 'vms.no_wo = det.no_wo')
                ->select("det.*, vms.no_wo as no_wo, vms.merk as merk")
                ->where("det.kd_uji='" . $model['kd_uji'] . "'")
                ->select('det.*, vms.merk');

        $command = $query->createCommand();
        $models = $command->queryAll();
        $detail = '';
        foreach ($models as $key => $val) {
            $nowo = (isset($val['no_wo'])) ? $val['no_wo'] : '';
            $merk = (isset($val['merk'])) ? $val['merk'] : '';

            $detail[$key] = $val;
            $detail[$key]['no_wo'] = ['no_wo' => $nowo];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $models), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        \Yii::error($params);
        $model = new Ujimutu();

        $model->attributes = $params['ujimutu'];

        if ($model->save()) {
            $detail = $params['det_ujimutu'];
            foreach ($detail as $data) {
                $det = new DetUjimutu();
                $det->attributes = $data;
                $det->kd_uji = $model->kd_uji;
                $det->no_wo = $data['nowo']['no_wo'];
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
        $model->attributes = $params['ujimutu'];

        if ($model->save()) {
            $deleteDetail = DetUjimutu::deleteAll(['kd_uji' => $model->kd_uji]);
            foreach ($params['det_ujimutu'] as $data) {
                $det = new DetUjimutu();
                $det->attributes = $data;
                $det->kd_uji = $model->kd_uji;
                $det->no_wo = $data['nowo']['no_wo'];
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
        $deleteDetail = DetUjimutu::deleteAll(['kd_uji' => $id]);
        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Ujimutu::findOne($id)) !== null) {
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
