<?php

namespace app\controllers;

use Yii;
use app\models\Kerja;
use app\models\Spk;
use app\models\DetSpkerja;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SpkController extends Controller {

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
                    'kerja' => ['post'],
                    'customer' => ['post'],
                    'jabatan' => ['post'],
                    'kode' => ['get'],
                    'updtst' => ['get'],
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

    public function actionKerja() {
        $params = json_decode(file_get_contents("php://input"), true);

        $query = new Query;
        $query->from('kerja')
                ->where('kd_jab="' . $params['id_jabatan'] . '"')
                ->select("*");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $query2 = new Query;
        $query2->from('kerja')
                ->where('kd_jab="' . $params['id_jabatan'] . '"')
                ->select("*");
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();
        $coba = array();
        if (empty($detail)) {
            $coba[0]['nm_kerja'] = '';
        } else {
            foreach ($detail as $key => $asu) {
                $coba[$key]['nm_kerja'] = $asu;
            }
        }



        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kerja' => $models, 'detail' => $coba));
    }

    public function actionModel() {
        $query = new Query;
        $query->from('wo_masuk')
                ->select("no_wo");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'wo' => $models));
    }

    public function actionCustomer() {
        $params = json_decode(file_get_contents("php://input"), true);

        $query2 = new Query;
        $query2->from(['trans_spkerja', 'tbl_jabatan'])
                ->where('trans_spkerja.kd_jab = tbl_jabatan.id_jabatan  and trans_spkerja.no_wo = "' . $params['no_wo'] . '" ')
                ->select("tbl_jabatan.jabatan as jabatan, tbl_jabatan.id_jabatan, tbl_jabatan.krj");
        $command2 = $query2->createCommand();
        $models2 = $command2->query()->read();

        if (!empty($models2)) {
            // detail
            $query = new Query;
            $query->from(['det_spkerja', 'kerja'])
                    ->where('det_spkerja.kd_ker = kerja.kd_ker  and det_spkerja.no_wo = "' . $params['no_wo'] . '" and det_spkerja.kd_jab="' . $models2['id_jabatan'] . '"')
//                ->join('LEFT JOIN', 'kerja', 'kerja.kd_jab = ds.kd_jab')
//                ->where('ds.no_wo = "' . $params['no_wo'] . '" ')
                    ->select("kerja.nm_kerja");
            $command = $query->createCommand();
            $detail = $command->queryAll();
            $coba = array();
            if (!empty($detail)) {
                foreach ($detail as $key => $data) {
                    $coba[$key]['nm_kerja'] = $data;
                }
            } else {
                $coba[0]['nm_kerja'] = '';
            }

            // list kerja
            $query3 = new Query;
            $query3->from('kerja')
                    ->where('kd_jab="' . $models2['id_jabatan'] . '"')
                    ->select("*");

            $command3 = $query3->createCommand();
            $models3 = $command3->queryAll();


            $jabatan = '';
            $jabatan1['spk']['jabatan'] = [
                'id_jabatan' => $models2['id_jabatan'],
                'jabatan' => $models2['jabatan'],
                'krj' => $models2['krj'],
            ];
        } else {
            // list kerja
            $query4 = new Query;
            $query4->from('tbl_jabatan')
                    ->select("*");

            $command4 = $query4->createCommand();
            $jabatan = $command4->queryAll();
            $coba = array();
            $coba[0]['nm_kerja'] = '';
            $models3 = array();
            $jabatan1 = '';
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'asu' => $jabatan1, 'jabatan' => $jabatan, 'detail' => $coba, 'kerja' => $models3));
    }

    public function actionJabatan() {
        $query4 = new Query;
        $query4->from('tbl_jabatan')
                ->select("*");

        $command4 = $query4->createCommand();
        $jabatan = $command4->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'jabatan' => $jabatan));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_spkerja.id DESC";
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
                ->from('trans_spkerja')
                ->join('JOIN', 'view_wo_spk as vw', 'trans_spkerja.no_wo = vw.no_wo')
                ->join('JOIN', 'tbl_jabatan', 'trans_spkerja.kd_jab = tbl_jabatan.id_jabatan')
                ->orderBy($sort)
                ->select("trans_spkerja.id as id_spk,trans_spkerja.no_wo,vw.nm_customer, vw.model,tbl_jabatan.jabatan, vw.merk, vw.tipe");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'jabatan') {
                    $query->andFilterWhere(['like', 'tbl_jabatan.' . $key, $val]);
                } else {
                    $query->andFilterWhere(['like', 'trans_spkerja.' . $key, $val]);
                }
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
        $data = $model->attributes;
// cus dan model
        $query2 = new Query;
        $query2->from('trans_spkerja')
                ->join('JOIN', 'view_wo_spk as vw', 'trans_spkerja.no_wo = vw.no_wo')
                ->join('JOIN', 'tbl_jabatan', 'trans_spkerja.kd_jab = tbl_jabatan.id_jabatan')
                ->where(' trans_spkerja.no_wo = "' . $model['no_wo'] . '"')
                ->select("trans_spkerja.id as id_spk,trans_spkerja.no_wo,vw.nm_customer, vw.model,tbl_jabatan.jabatan, vw.merk, vw.tipe");
        $command2 = $query2->createCommand();
        $models2 = $command2->query()->read();

        $data = ['nm_customer' => $models2['nm_customer'], 'model' => $models2['model'],'merk' => $models2['merk'],'tipe' => $models2['tipe']];
        //no wo
        $nowo = Spk::find()
                ->where(['id' => $model['id']])
                ->One();
        $no_wo = (isset($nowo->no_wo)) ? $nowo->no_wo : '';
        $data['no_wo'] = [
            'no_wo' => $no_wo
        ];

        $query3 = new Query;
        $query3->from('tbl_jabatan')
                ->select('*')
                ->where('id_jabatan="' . $model['kd_jab'] . '"');

        $command3 = $query3->createCommand();
        $listjabatan = $command3->queryAll();
        $cc = array();
        foreach ($listjabatan as $key => $dd) {
            $data['jabatan'] = $dd;
        }


        //detail
        $query = new Query;
        $query->from(['det_spkerja', 'kerja'])
                ->where('det_spkerja.kd_ker = kerja.kd_ker  and det_spkerja.no_wo = "' . $model['no_wo'] . '" and det_spkerja.kd_jab="' . $model['kd_jab'] . '"')
//                ->join('LEFT JOIN', 'kerja', 'kerja.kd_jab = ds.kd_jab')
//                ->where('ds.no_wo = "' . $params['no_wo'] . '" ')
                ->select("kerja.nm_kerja, kerja.no, kerja.kd_ker, kerja.kd_jab,kerja.jenis");
        $command = $query->createCommand();
        $detail = $command->queryAll();
        $coba = array();
        foreach ($detail as $key => $asu) {
            $coba[$key]['nm_kerja'] = $asu;
        }


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'detail' => $coba, 'jabatan' => $listjabatan), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        Yii::error($params);
        $model = new Spk();
        $model->no_wo = $params['spk']['no_wo']['no_wo'];
        $model->kd_jab = $params['spk']['jabatan']['id_jabatan'];
        $model->status = 0;

        if ($model->save()) {
            $detail = $params['detailSpk'];
            foreach ($detail as $data) {
                $kerja = Kerja::findOne($data['nm_kerja']['no']);
                if (empty($kerja)) {
                    $kerja = new Kerja();
                }
                $query = new Query;
                $query->from('kerja')
                        ->select('*')
                        ->orderBy('kd_ker DESC')
                        ->limit(1);

                $command = $query->createCommand();
                $models = $command->query()->read();
                $lastKode = substr($models['kd_ker'], -3) + 1;

                $kode = 'KER' . $lastKode;

                $kerja->attributes = $data['nm_kerja'];
                $kerja->kd_ker = $kode;
                $kerja->kd_jab = $params['spk']['jabatan']['id_jabatan'];
                $kerja->jenis = $params['spk']['no_wo']['jenis'];
//                $kerja->save();
                if ($kerja->save()) {
                    $detsp = new DetSpkerja();
                    $detsp->no_wo = $params['spk']['no_wo']['no_wo'];
                    $detsp->kd_jab = $params['spk']['jabatan']['id_jabatan'];
                    $detsp->kd_ker = $kerja->kd_ker;
                    $detsp->save();
                }
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
        Yii::error($params);
        $model = $this->findModel($id);
        $model->no_wo = $params['spk']['no_wo']['no_wo'];
        $model->kd_jab = $params['spk']['jabatan']['id_jabatan'];


        if ($model->save()) {
            $deleteDetail = DetSpkerja::deleteAll(['no_wo' => $params['spk']['no_wo']['no_wo']]);
            $detail = $params['detailSpk'];
            foreach ($detail as $data) {
                $kerja = Kerja::findOne($data['nm_kerja']['no']);
                if (empty($kerja)) {
                    $kerja = new Kerja();
                }
                $query = new Query;
                $query->from('kerja')
                        ->select('*')
                        ->orderBy('kd_ker DESC')
                        ->limit(1);

                $command = $query->createCommand();
                $models = $command->query()->read();
                $lastKode = substr($models['kd_ker'], -3) + 1;

                $kode = 'KER' . $lastKode;

                $kerja->attributes = $data['nm_kerja'];
                $kerja->kd_ker = $kode;
                $kerja->kd_jab = $params['spk']['jabatan']['id_jabatan'];
                $kerja->jenis = $kerja->jenis;
//                $kerja->save();
                if ($kerja->save()) {

                    $detsp = new DetSpkerja();
                    $detsp->no_wo = $params['spk']['no_wo']['no_wo'];
                    $detsp->kd_jab = $params['spk']['jabatan']['id_jabatan'];
                    $detsp->kd_ker = $kerja->kd_ker;
                    $detsp->save();
                }
            }

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }
      public function actionUpdtst($id) {
        $model = Spk::findOne(['id' => $id]);
        $model->status = 1;
        $model->save();
    }
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = DetSpkerja::deleteAll(['no_wo' => $model['no_wo']]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Spk::findOne($id)) !== null) {
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
