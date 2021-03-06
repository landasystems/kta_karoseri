<?php

namespace app\controllers;

use Yii;
use app\models\ReturBbk;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ReturbbkController extends Controller {

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
                    'kode' => ['get'],
                    'excel' => ['get'],
                    'rekap' => ['get'],
                    'barangkeluar' => ['post'],
                    'lock' => ['post'],
                    'unlock' => ['post'],
                ],
            ]
        ];
    }

    public function actionLock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = ReturBbk::findOne($key);
            $status->lock = 1;
            $status->save();
        }
    }

    public function actionUnlock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = ReturBbk::findOne($key);
            $status->lock = 0;
            $status->save();
        }
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

    public function actionBarangkeluar() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        if ($params['no_bbk']['no_bbk'] != "") {
            $query->from('barang, det_bbk')
                    ->select("barang.kd_barang, barang.nm_barang, det_bbk.jml")
                    ->where(['like', 'barang.nm_barang', $params['barang']])
                    ->orWhere(['like', 'barang.kd_barang', $params['barang']])
                    ->andWhere("barang.nm_barang != '-' && barang.kd_barang != '-'")
                    ->andWhere("det_bbk.no_bbk = '" . $params['no_bbk']['no_bbk'] . "' and det_bbk.kd_barang = barang.kd_barang ");

            $command = $query->createCommand();
            $models = $command->queryAll();
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => $models));
        }
    }

    public function actionKode() {
        $query = new Query;
        $query->from('retur_bbk')
                ->select('*')
                ->orderBy('no_retur_bbk DESC')
                ->where('year(tgl) = "' . date("Y") . '"')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();

        if (empty($models)) {
            $kode = 'RM' . date("y") . '00001';
        } else {
            $lastKode = substr($models['no_retur_bbk'], -5) + 1;
            $kode = 'RM' . date("y") . substr('0000' . $lastKode, -5);
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_retur_bbk DESC";
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
                ->from('retur_bbk as rb')
                ->leftJoin('barang as b', 'rb.kd_barang = b.kd_barang')
                ->orderBy($sort)
                ->select("rb.*, b.nm_barang");

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
        $params = $_REQUEST;
        $filter = array();
        $sort = "rb.no_retur_bbk DESC";
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
                ->from('retur_bbk as rb')
                ->join('JOIN', 'trans_bbk as tb', 'tb.no_bbk = rb.no_bbk')
                ->join('JOIN', 'barang', 'barang.kd_barang = rb.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg as jb', 'barang.jenis = jb.kd_jenis')
                ->orderBy($sort)
                ->select("rb.tgl as tanggal, rb.no_retur_bbk, tb.no_bbk, tb.no_wo, barang.kd_barang, jb.jenis_brg, barang.nm_barang, barang.satuan,
                       rb.ket ");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'rb.tgl', $start, $end]);
                } elseif ($key == 'no_retur_bbk') {
                    $query->andFilterWhere(['like', 'rb.' . $key, $val]);
                } elseif ($key == 'no_bbk') {
                    $query->andFilterWhere(['like', 'tb.' . $key, $val]);
                } elseif ($key == 'nm_barang') {
                    $query->andFilterWhere(['like', 'barang.' . $key, $val]);
                }
            }
        }
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = ReturBbk::find()->where('no_retur_bbk="' . $id . '"')->one();

        $query = new Query;
        $query->from('barang')
                ->select('kd_barang, nm_barang')
                ->where('kd_barang = "' . $model->kd_barang . '"')
                ->limit(1);
        $command = $query->createCommand();
        $barang = $command->query()->read();

        $query = new Query;
        $query->from('det_bbk')
                ->select('sum(jml) as jumlah')
                ->where('kd_barang = "' . $model->kd_barang . '" and no_bbk = "' . $model->no_bbk . '"');
        $command = $query->createCommand();
        $bbk = $command->query()->read();

        $model->kd_barang = array('kd_barang' => $barang['kd_barang'], 'nm_barang' => $barang['nm_barang'], 'jml' => empty($bbk['jumlah']) ? 0 : $bbk['jumlah']);
        $model->no_bbk = array('no_bbk' => $model->no_bbk);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new ReturBbk();
        $model->attributes = $params;
        $model->kd_barang = $params['kd_barang']['kd_barang'];
        $model->no_bbk = $params['no_bbk']['no_bbk'];
        if ($model->alasan == 'Tidak Sesuai') {
            //update stok barang
            $barang = Barang::find()->where('kd_barang="' . $model->kd_barang . '"')->one();
            $barang->saldo += $model->jml;
            $barang->save();
        }

//        echo json_encode($barang);

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
        $model = ReturBbk::find()->where('no_retur_bbk="' . $id . '"')->one();
        if ($model->alasan == 'Tidak Sesuai') {
            //kembalikan stok barang ke semula
            $barang = Barang::find()->where('kd_barang="' . $params['kd_barang']['kd_barang'] . '"')->one();
            $barang->saldo -= $model->jml;
            $barang->save();
        }
        $model->attributes = $params;
        $model->kd_barang = $params['kd_barang']['kd_barang'];
        $model->no_bbk = $params['no_bbk']['no_bbk'];

        if ($model->save()) {
            if ($model->alasan == 'Tidak Sesuai') {
                //update stok barang dengan yang baru
                $barang = Barang::find()->where('kd_barang="' . $params['kd_barang']['kd_barang'] . '"')->one();
                $barang->saldo += $model->jml;
                $barang->save();
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = ReturBbk::find()->where('no_retur_bbk="' . $id . '"')->one();


        if ($model->alasan == 'Tidak Sesuai') {
            //kembalikan stok barang ke semula
            $barang = Barang::find()->where('kd_barang="' . $model->kd_barang . '"')->one();
            $barang->saldo += $model->jml;
            $barang->save();
        }

        if ($model->delete()) {

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = ReturBbk::findOne($id)) !== null) {
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
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/returbbk", ['models' => $models, 'filter' => $filter]);
    }

}

?>
