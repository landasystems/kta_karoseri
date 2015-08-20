<?php

namespace app\controllers;

use Yii;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BarangController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'cari' => ['get'],
                    'rekappergerakan' => ['get'],
                    'excelpergerakan' => ['get'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } else if (excel(isset($this->actions['*']))) {
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

    public function actionRekappergerakan() {
        $filter = array();

        $bbm = new Query;
        $bbm->from('det_bbm')
                ->join('Join', 'barang', 'barang.kd_barang = det_bbm.kd_barang')
                ->select("det_bbm.tgl_terima, barang.kd_barang, barang.nm_barang, barang.satuan, barang.min, barang.saldo, det_bbm.jumlah")
                ->where('det_bbm.tgl_terima >= "2015-07-01" and det_bbm.tgl_terima <= "2015-07-06"');

        $commandBBM = $bbm->createCommand();
        $modelBBM = $commandBBM->queryAll();

        $data = array();
        foreach ($modelBBM as $valBbm) {
            $data[$valBbm['kd_barang']]['kd_barang'] = $valBbm['kd_barang'];
            $data[$valBbm['kd_barang']]['barang'] = $valBbm['nm_barang'];
            $data[$valBbm['kd_barang']]['satuan'] = $valBbm['satuan'];
            $data[$valBbm['kd_barang']]['stok_minim'] = $valBbm['min'];
            $data[$valBbm['kd_barang']]['saldo_awal'] = $valBbm['saldo'];
            $data[$valBbm['kd_barang']]['stok_keluar'] = 0;
            $data[$valBbm['kd_barang']]['stok_masuk'] = isset($data[$valBbm['kd_barang']]['stok_masuk']) ? $data[$valBbm['kd_barang']]['stok_masuk'] + $valBbm['jumlah'] : $valBbm['jumlah'];
            $data[$valBbm['kd_barang']]['saldo_akhir'] = $data[$valBbm['kd_barang']]['saldo_awal'] + $data[$valBbm['kd_barang']]['stok_masuk'];
        }

        $bbk = new Query;
        $bbk->from('det_bbk')
                ->join('Join', 'trans_bbk', 'trans_bbk.no_bbk = det_bbk.no_bbk')
                ->join('Join', 'barang', 'barang.kd_barang = det_bbk.kd_barang')
                ->select("barang.kd_barang, barang.nm_barang, barang.satuan, barang.min, barang.saldo, det_bbk.jml, trans_bbk.tanggal")
                ->where('trans_bbk.tanggal >= "2015-07-01" and trans_bbk.tanggal <= "2015-07-06"');
        $bbk->limit(6);

        $commandBBK = $bbk->createCommand();
        $modelBBK = $commandBBK->queryAll();

        $i = 1;
        foreach ($modelBBK as $valBbk) {
            $data[$valBbk['kd_barang']]['kd_barang'] = $valBbk['kd_barang'];
            $data[$valBbk['kd_barang']]['barang'] = $valBbk['nm_barang'];
            $data[$valBbk['kd_barang']]['satuan'] = $valBbk['satuan'];
            $data[$valBbk['kd_barang']]['stok_minim'] = $valBbk['min'];
            $data[$valBbk['kd_barang']]['saldo_awal'] = $valBbk['saldo'];
            $data[$valBbk['kd_barang']]['stok_masuk'] = isset($data[$valBbk['kd_barang']]['stok_masuk']) ? $data[$valBbk['kd_barang']]['stok_masuk'] : 0;
            $data[$valBbk['kd_barang']]['stok_keluar'] = isset($data[$valBbk['kd_barang']]['stok_keluar']) ? $data[$valBbk['kd_barang']]['stok_keluar'] + $valBbk['jml'] : $valBbk['jml'];
            $data[$valBbk['kd_barang']]['saldo_akhir'] = (isset($data[$valBbk['kd_barang']]['saldo_akhir']) ? $data[$valBbk['kd_barang']]['saldo_akhir'] : $valBbk['saldo'] ) - $valBbk['jml'];
            $i++;
        }

        session_start();
        $_SESSION['queryBbm'] = $bbm;
        $_SESSION['queryBbk'] = $bbk;
        $_SESSION['filter'] = $filter;

        echo json_encode(array('status' => 1, 'data' => $data));
    }

    public function actionExcelpergerakan() {
        session_start();

        $bbm = $_SESSION['queryBbm'];
        $bbk = $_SESSION['queryBbk'];

        $bbm->offset("");
        $bbm->limit("");
        $commandBbm = $bbm->createCommand();
        $modelBBM = $commandBbm->queryAll();

        $bbk->offset("");
        $bbk->limit("");
        $commandBbk = $bbk->createCommand();
        $modelBBK = $commandBbk->queryAll();

        $data = array();
        foreach ($modelBBM as $valBbm) {
            $data[$valBbm['kd_barang']]['kd_barang'] = $valBbm['kd_barang'];
            $data[$valBbm['kd_barang']]['barang'] = $valBbm['nm_barang'];
            $data[$valBbm['kd_barang']]['satuan'] = $valBbm['satuan'];
            $data[$valBbm['kd_barang']]['stok_minim'] = $valBbm['min'];
            $data[$valBbm['kd_barang']]['saldo_awal'] = $valBbm['saldo'];
            $data[$valBbm['kd_barang']][$valBbm['tgl_terima']]['tgl_keluar'] = '-';
            $data[$valBbm['kd_barang']][$valBbm['tgl_terima']]['jml'] = 0;
            $data[$valBbm['kd_barang']]['stok_masuk'] = isset($data[$valBbm['kd_barang']]['stok_masuk']) ? $data[$valBbm['kd_barang']]['stok_masuk'] + $valBbm['jumlah'] : $valBbm['jumlah'];
            $data[$valBbm['kd_barang']]['saldo_akhir'] = $data[$valBbm['kd_barang']]['saldo_awal'] + $data[$valBbm['kd_barang']]['stok_masuk'];
        }

        $i = 1;
        foreach ($modelBBK as $valBbk) {
            $data[$valBbk['kd_barang']]['kd_barang'] = $valBbk['kd_barang'];
            $data[$valBbk['kd_barang']]['barang'] = $valBbk['nm_barang'];
            $data[$valBbk['kd_barang']]['satuan'] = $valBbk['satuan'];
            $data[$valBbk['kd_barang']]['stok_minim'] = $valBbk['min'];
            $data[$valBbk['kd_barang']]['saldo_awal'] = $valBbk['saldo'];
            $data[$valBbk['kd_barang']]['stok_masuk'] = isset($data[$valBbk['kd_barang']]['stok_masuk']) ? $data[$valBbk['kd_barang']]['stok_masuk'] : 0;
            $data[$valBbk['kd_barang']][$valBbk['tanggal']]['tgl_keluar'] = $valBbk['tanggal'];
            $data[$valBbk['kd_barang']][$valBbk['tanggal']]['jml'] = isset($data[$valBbk['kd_barang']][$valBbk['tanggal']]['jml']) ? $data[$valBbk['kd_barang']][$valBbk['tanggal']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
            $data[$valBbk['kd_barang']]['saldo_akhir'] = (isset($data[$valBbk['kd_barang']]['saldo_akhir']) ? $data[$valBbk['kd_barang']]['saldo_akhir'] : $valBbk['saldo'] ) - $valBbk['jml'];
            $i++;
        }
        $tgl = array('2015-07-01', '2015-07-02', '2015-07-03', '2015-07-04', '2015-07-05', '2015-07-06');
        return $this->render("/expretur/pergerakanbarang", ['models' => $data, 'tgl' => $tgl]);
    }

    public function actionJenis() {
        $query = new Query;
        $query->from('jenis_brg')
                ->select("*");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'jenis_brg' => $models));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('barang')
                ->select('*')
                ->orderBy('kd_barang DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        if (empty($models)) {
            $kode = '100001';
        } else {
            $kode = $models['kd_barang'] + 1;
        }
        Yii::error($command->query());
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "kd_barang ASC";
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
                ->from(['barang', 'jenis_brg'])
                ->where('barang.jenis = jenis_brg.kd_jenis')
                ->orderBy($sort)
                ->select("barang.*, jenis_brg.jenis_brg");

//filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == "kat") {
                    $query->andFilterWhere(['=', $key, $val]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        session_start();
        $_SESSION['query'] = $query;

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
        $model = new Barang();
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
        $ft = $model->foto;
        $model->attributes = $params;
        if (empty($model->foto)) {
            $model->foto = $ft;
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
        if (($model = Barang::findOne($id)) !== null) {
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
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expmaster/barang", ['models' => $models]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('barang')
                ->select("*")
                ->where(['like', 'nm_barang', $params['barang']])
                ->orWhere(['like', 'kd_barang', $params['barang']])
                ->andWhere("nm_barang != '-' && kd_barang != '-'");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
