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
                    'rekap' => ['get'],
                    'view' => ['get'],
                    'cari' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['post'],
                    'excel' => ['get'],
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
                    ->where('SUBSTR(no_spk,1,1) = "O"')
                    ->limit(1);

            $cek = Spkaroseri::findOne('no_spk = "O' . date("y") . '001"');
            if (empty($cek)) {
                $command = $query->createCommand();
                $models = $command->query()->read();
                $urut = substr($models['no_spk'], 3) + 1;
                $kode = substr('000' . $urut, strlen($urut));
                $kode = "O" . date("y") . $kode;
            } else {
                $kode = "O" . date("y") . "001";
            }
        } else if ($tipe['tipe'] == "stok") {
            $query = new Query;
            $query->from('spk')
                    ->select('no_spk')
                    ->where('SUBSTR(no_spk,1,1) = "S"')
                    ->orderBy('no_spk DESC')
                    ->limit(1);

            $cek = Spkaroseri::findOne('no_spk = "S' . date("y") . '001"');
            if (empty($cek)) {
                $command = $query->createCommand();
                $models = $command->query()->read();
                $urut = substr($models['no_spk'], 3) + 1;
                $kode = substr('000' . $urut, strlen($urut));
                $kode = "S" . date("y") . $kode;
            } else {
                $kode = "S" . date("y") . "001";
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_spk DESC";
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
                ->from('spk as s')
                ->join('Join', 'chassis as c', 's.kd_chassis = c.kd_chassis')
                ->join('Join', 'customer cus', 'cus.kd_cust = s.kd_customer')
                ->orderBy($sort)
                ->select("s.*, c.*, cus.*");

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

    public function actionRekap() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "spk.no_spk DESC";
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
                ->from('spk')
                ->join('LEFT JOIN', 'view_wo_spk as vws', 'spk.no_spk = vws.no_spk')
                ->join('LEFT JOIN', 'serah_terima_in as sti', 'sti.kd_titipan = vws.kd_titipan')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->select("vws.*, tk.nama as sales, sti.tgl_terima as tgl_chassis")
                ->orderBy($sort);

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'sti.tgl_terima') {
                    $tgl = explode(" - ", $val);
                    $start = date("Y-m-d", strtotime($tgl[0]));
                    $end = date("Y-m-d", strtotime($tgl[1]));
                    $query->andFilterWhere(['between', 'sti.tgl_terima', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $query->limit(null);
        $query->offset(null);

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/spk", ['models' => $models, 'filter' => $filter]);
    }

    public function actionView($id) {
        $query = new Query;
        $query->from('spk as s')
                ->join('LEFT JOIN', 'chassis as c', 's.kd_chassis = c.kd_chassis')
                ->join('LEFT JOIN', 'customer cus', 'cus.kd_cust = s.kd_customer')
                ->join('LEFT JOIN', 'model m', 'm.kd_model= s.kd_model')
                ->join('LEFT JOIN', 'tbl_karyawan tb', 's.nik= tb.nik')
                ->where('s.no_spk="' . $id . '"')
//                ->select('s.*');
                ->select("s.*, c.merk, c.tipe, cus.kd_cust, cus.nm_customer, cus.alamat1 , tb.nik, tb.nama, m.kd_model, m.model");
        $command = $query->createCommand();
        $models = $command->query()->read();

        $models['kd_customer'] = array('kd_cust' => $models['kd_cust'], 'nm_customer' => $models['nm_customer'], 'alamat1' => $models['alamat1']);
        $models['kd_bom'] = array('kd_bom' => $models['kd_bom']);
        $models['kd_model'] = array('kd_model' => $models['kd_model'], 'model' => $models['model']);
        $models['nik'] = array('nik' => $models['nik'], 'nama' => $models['nama']);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
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

    public function actionCari() {
        $params = $_REQUEST;
        $model = Spkaroseri::find()
                        ->where('no_spk like "%' . $params['nama'] . '%"')
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

}

?>
