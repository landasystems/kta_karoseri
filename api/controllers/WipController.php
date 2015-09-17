<?php

namespace app\controllers;

use Yii;
use app\models\Womasuk;
use app\models\Warna;
use app\models\Smalleks;
use app\models\Smallint;
use app\models\Minieks;
use app\models\Wip;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class WipController extends Controller {

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
                    'delete' => ['post'],
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'warna' => ['post'],
                    'getnowo' => ['post'],
                    'select' => ['post'],
                    'karyawan' => ['get'],
                    'proses' => ['get'],
                    'cari' => ['get'],
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

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('view_wo_spk')
                ->join(' JOIN', 'spk', 'view_wo_spk.no_spk = spk.no_spk')
                ->join('JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
//                ->join(' JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
                ->join(' JOIN', 'wo_masuk', 'view_wo_spk.no_wo = wo_masuk.no_wo')
                ->join(' JOIN', 'serah_terima_in as sti', 'sti.no_spk = view_wo_spk.no_spk')
//                ->join(' JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->select("*")
                ->where(['like', 'wo_masuk.no_wo', $params['no_wo']])
                ->andWhere('wo_masuk.tgl_keluar IS NOT NULL or wo_masuk.tgl_keluar=""')
                ->limit(10);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionGetnowo() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $query2 = new Query;
        $query2->from('det_wip as wip')
                ->join('JOIN', 'bagian', 'bagian.kd_bag = wip.kd_kerja')
                ->join('JOIN', 'tbl_karyawan as tk', 'tk.nik = wip.nik')
                ->where('wip.no_wo = "' . $params['no_wo'] . '"')
                ->select('*');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();

        $coba = array();
        if (!empty($detail)) {
            foreach ($detail as $key => $data) {
                $coba[$key] = $data;
                $coba[$key]['pemborong'] = ['nama' => $data['nama'], 'nik' => $data['nik']];
                $coba[$key]['proses'] = ['bagian' => $data['bagian'], 'kd_bag' => $data['kd_bag']];
            }
        } else {
            $coba[0]['id'] = 0;
            $coba[0]['no_wo'] = '';
            $coba[0]['kd_kerja'] = '';
            $coba[0]['plan_start'] = '';
            $coba[0]['plan_finish'] = '';
            $coba[0]['act_start'] = '';
            $coba[0]['act_finish'] = '';
            $coba[0]['keterangan'] = '';
        }
        // hitung umur
        // memecah string tanggal awal untuk mendapatkan
        // tanggal, bulan, tahun
        $pecah1 = explode("-", $params['tgl_terima']);
        $date1 = $pecah1[2];
        $month1 = $pecah1[1];
        $year1 = $pecah1[0];

        // memecah string tanggal akhir untuk mendapatkan
        // tanggal, bulan, tahun
        $pecah2 = explode("-", date('Y-m-d'));
        $date2 = $pecah2[2];
        $month2 = $pecah2[1];
        $year2 = $pecah2[0];

        // mencari total selisih hari dari tanggal awal dan akhir
        $jd1 = GregorianToJD($month1, $date1, $year1);
        $jd2 = GregorianToJD($month2, $date2, $year2);

        $selisih = $jd2 - $jd1;

        echo json_encode(array('status' => 1, 'umur' => $selisih, 'detail' => $coba));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "dw.no_wo DESC";
        $offset = 0;
        $limit = 10;

        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

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
                ->from('det_wip as dw')
                ->join('JOIN', 'view_wo_spk as vws', 'dw.no_wo = vws.no_wo')
                ->join('JOIN', 'bagian', 'bagian.kd_bag = dw.kd_kerja')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->limit(null);
        $query->offset(null);
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/schedule", ['models' => $models]);
    }

    public function actionKaryawan() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select("*")
                ->where(['like', 'nama', $params['karyawan']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionProses() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('bagian')
                ->select("*")
                ->where(['like', 'bagian', $params['proses']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $query = new Query;
        $query->from('wo_masuk')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
                ->join('JOIN', 'serah_terima_in', 'spk.no_spk = serah_terima_in.no_spk') // customer
                ->join('JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->where('wo_masuk.no_wo = "' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $data) {
            $warna = 'dfdf';
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'warna' => $warna), JSON_PRETTY_PRINT);
    }

    public function actionSelect() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($params['no_wo']);
        $query = new Query;
        $query->from('wo_masuk')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
                ->join('LEFT JOIN', 'serah_terima_in', 'spk.no_spk = serah_terima_in.no_spk') // customer
                ->join('LEFT JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->where('wo_masuk.no_wo = "' . $params['no_wo'] . '"')
                ->select("sales.nama as sales,warna.warna as warna, customer.nm_customer as customer, customer.nm_pemilik as pemilik,
                            chassis.model_chassis as model_chassis, chassis.merk as merk, chassis.tipe as tipe, model.model, serah_terima_in.no_chassis as no_rangka,
                            serah_terima_in.no_mesin, chassis.jenis, serah_terima_in.tgl_terima");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $data) {
            $asu['warna'] = (isset($data['warna'])) ? $data['warna'] : '-';
            $asu['customer'] = (isset($data['customer'])) ? $data['customer'] : '-';
            $asu['sales'] = (isset($data['sales'])) ? $data['sales'] : '-';
            $asu['pemilik'] = (isset($data['pemilik'])) ? $data['pemilik'] : '-';
            $asu['model_chassis'] = (isset($data['model_chassis'])) ? $data['model_chassis'] : '-';
            $asu['tipe'] = (isset($data['tipe'])) ? $data['tipe'] : '-';
            $asu['merk'] = (isset($data['merk'])) ? $data['merk'] : '-';
            $asu['model'] = (isset($data['model'])) ? $data['model'] : '-';
            $asu['no_rangka'] = (isset($data['no_rangka'])) ? $data['no_rangka'] : '-';
            $asu['no_mesin'] = (isset($data['no_mesin'])) ? $data['no_mesin'] : '-';
            $asu['jenis'] = (isset($data['jenis'])) ? $data['jenis'] : '-';
            $asu['tgl_terima'] = (isset($data['tgl_terima'])) ? $data['tgl_terima'] : '-';
        }
        if ($asu['jenis'] == "Small Bus") {
            // eksterior
            $eksterior = new Query;
            $eksterior->from('small_eks')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();

            // interior
            $interior = new Query;
            $interior->from('small_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        } else {
            // eksterior
            $eksterior = new Query;
            $eksterior->from('mini_eks')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();

            // interior
            $interior = new Query;
            $interior->from('mini_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        }
        $model['no_spk'] = ['as' => '1111'];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'det' => $asu, 'eksterior' => $models2, 'interior' => $models3), JSON_PRETTY_PRINT);
    }

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params['detWip']);
        $deleteAll = Wip::deleteAll('no_wo="' . $params['wip']['no_wo']['no_wo'] . '"');

        foreach ($params['detWip'] as $data) {
            $model = new Wip();
            $model->no_wo = $params['wip']['no_wo']['no_wo'];
            $model->kd_kerja = $data['proses']['kd_bag'];
            $model->plan_start = date('Y-m-d', strtotime($data['plan_start']));
            $model->plan_finish = date('Y-m-d', strtotime($data['plan_finish']));
            $model->act_start = date('Y-m-d', strtotime($data['act_start']));
            $model->act_finish = date('Y-m-d', strtotime($data['act_finish']));
            $model->keterangan = $data['keterangan'];
            $model->nik = $data['pemborong']['nik'];
            $model->save();
        }

//        $model->attributes = $params;





        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionDelete() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = $this->findModel($params['no_wo']);
//        $model = WoMasuk::deleteAll(['no_wo' => $params['no_wo']]);
        if ($params['jenis'] == "Small Bus") {
            $eks = Smalleks::deleteAll(['no_wo' => $params['no_wo']]);
            $int = Smallint::deleteAll(['no_wo' => $params['no_wo']]);
        } else {
            $eks = Minieks::deleteAll(['no_wo' => $params['no_wo']]);
            $int = Miniint::deleteAll(['no_wo' => $params['no_wo']]);
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
        if (($model = Womasuk::findOne($id)) !== null) {
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
