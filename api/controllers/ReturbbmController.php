<?php

namespace app\controllers;

use Yii;
use app\models\ReturBbm;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ReturbbmController extends Controller {

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
                    'rekap' => ['get'],
                    'barangmasuk' => ['post'],
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

    public function actionBarangmasuk() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        if ($params['no_bbm']['no_bbm'] != "") {
            $query->from('barang, det_bbm')
                    ->select("barang.kd_barang, barang.nm_barang, det_bbm.jml")
                    ->where(['like', 'barang.nm_barang', $params['barang']])
                    ->orWhere(['like', 'barang.kd_barang', $params['barang']])
                    ->andWhere("barang.nm_barang != '-' && barang.kd_barang != '-'")
                    ->andWhere("det_bbm.no_bbm = '" . $params['no_bbm']['no_bbm'] . "' and det_bbm.kd_barang = barang.kd_barang ");

            $command = $query->createCommand();
            $models = $command->queryAll();
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => $models));
        }
    }

    public function actionKode() {
        $query = new Query;
        $query->from('retur_bbm')
                ->select('*')
                ->orderBy('no_retur_bbm DESC')
                ->limit(1);

        $cek = ReturBbm::findOne('no_retur_bbm = "BM' . date("y") . '0001"');
        if (empty($cek)) {
            $command = $query->createCommand();
            $models = $command->query()->read();
            $urut = substr($models['no_retur_bbm'], 2, 4) + 1;
            $kode = substr('0000' . $urut, strlen($urut));
            $kode = "RM" . date("y") . $kode;
        } else {
            $kode = "RM" . date("y") . "0001";
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tgl ASC";
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
                ->from('retur_bbm as rb')
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
        $sort = "rb.tgl ASC";
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
                ->from('retur_bbm as rb')
                ->join('JOIN', 'trans_bbk as tb', 'tb.no_bbm = rb.no_bbm')
                ->join('JOIN', 'barang', 'barang.kd_barang = rb.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg as jb', 'barang.jenis = jb.kd_jenis')
                
                ->orderBy($sort)
                ->select("rb.tgl as tanggal, rb.no_retur_bbm, tb.no_bbm, tb.no_wo, barang.kd_barang, jb.jenis_brg, barang.nm_barang, barang.satuan,
                       rb.ket ");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tanggal') 
                    {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'rb.tanggal', $start, $end]);
                }elseif($key == 'no_retur_bbm'){
                    $query->andFilterWhere(['like', 'rb.'.$key, $val]);
                } elseif($key == 'no_bbm'){
                    $query->andFilterWhere(['like', 'tb.'.$key, $val]);
                } elseif($key == 'nm_barang'){
                    $query->andFilterWhere(['like', 'barang.'.$key, $val]);
                }
            }
        }
        Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = ReturBbm::find()->where('no_retur_bbm="' . $id . '"')->one();

        $query = new Query;
        $query->from('barang')
                ->select('kd_barang, nm_barang')
                ->where('kd_barang = "' . $model->kd_barang . '"')
                ->limit(1);
        $command = $query->createCommand();
        $barang = $command->query()->read();

        $query = new Query;
        $query->from('trans_bbm')
                ->select('no_bbm')
                ->where('no_bbm = "' . $model->no_bbm . '"')
                ->limit(1);
        $command = $query->createCommand();
        $bbk = $command->query()->read();
        $model->kd_barang = isset($barang) ? $barang : '-';
        $model->no_bbm = isset($bbk) ? $bbk : '-';

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new ReturBbm();
        $model->attributes = $params;
        $model->kd_barang = $params['kd_barang']['kd_barang'];
        $model->no_bbm = $params['no_bbm']['no_bbm'];
        if ($model->alasan == 'Tidak Sesuai') {
            //update stok barang
            $barang = Barang::find()->where('kd_barang="' . $model->kd_barang . '"')->one();
            $barang->saldo -= $model->jml;
            $barang->save();
        }

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
//        print_r($params);
        $model = ReturBbm::find()->where('no_retur_bbm="' . $id . '"')->one();
        if ($model->alasan == 'Tidak Sesuai') {
            //kembalikan stok barang ke semula
            $barang = Barang::find()->where('kd_barang="' . $params['kd_barang']['kd_barang'] . '"')->one();
            $barang->saldo -= $model->jml;
            $barang->save();
        }
        $model->attributes = $params;
        $model->kd_barang = $params['kd_barang']['kd_barang'];
        $model->no_bbm = $params['no_bbm']['no_bbm'];

        if ($model->save()) {
            if ($model->alasan == 'Tidak Sesuai') {
                //update stok barang dengan yang baru
                $barang = Barang::find()->where('kd_barang="' . $params['kd_barang']['kd_barang'] . '"')->one();
                $barang->saldo -= $model->jml;
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
        $model = ReturBbm::find()->where('no_retur_bbm="' . $id . '"')->one();


        if ($model->alasan == 'Tidak Sesuai') {
            //kembalikan stok barang ke semula
            $barang = Barang::find()->where('kd_barang="' . $model->kd_barang . '"')->one();
            $barang->saldo -= $model->jml;
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
        if (($model = ReturBbm::findOne($id)) !== null) {
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
