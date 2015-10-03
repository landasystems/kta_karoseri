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
                    'rekap' => ['get'],
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
        $query->from('wo_masuk')
                ->join('LEFT JOIN', 'serah_terima_in as sti', 'sti.kd_titipan = wo_masuk.kd_titipan')
                ->join('LEFT JOIN', 'view_wo_spk', 'view_wo_spk.no_wo = wo_masuk.no_wo')
                ->select("*")
                ->where(['like', 'wo_masuk.no_wo', $params['no_wo']])
                ->andWhere('wo_masuk.tgl_keluar IS NULL or wo_masuk.tgl_keluar=""')
                ->limit(10);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionGetnowo() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query2 = new Query;
        $query2->from('det_wip as wip')
                ->join('JOIN', 'bagian', 'bagian.kd_bag = wip.kd_kerja')
                ->join('JOIN', 'tbl_karyawan as tk', 'tk.nik = wip.nik')
                ->where('wip.no_wo = "' . $params['no_wo'] . '"')
                ->orderBy("bagian.urutan ASC")
                ->select('*');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();

        $coba = array();
        if (!empty($detail)) {
            foreach ($detail as $key => $data) {
                $coba[$key] = $data;
                
                if(!empty($data['plan_start']) && $data['plan_start'] !="-" && isset($data['plan_start'])){
                    $tgPS = explode('/', $data['plan_start']);
                    $isips = $tgPS[2] . '-' . $tgPS[1] . '-' . $tgPS[0];
                }else{
                    $isips = "";
                }
                
                if(!empty($data['plan_finish']) && $data['plan_finish'] !="-" && isset($data['plan_finish'])){
                     $tgPF = explode('/', $data['plan_finish']);
                    $isipf = $tgPF[2] . '-' . $tgPF[1] . '-' . $tgPF[0];
                }else{
                    $isipf = "";
                }
                
                if(!empty($data['act_start']) && $data['act_start'] !="-" && isset($data['act_start'])){
                     $tgPF = explode('/', $data['act_start']);
                    $isias = $tgPF[2] . '-' . $tgPF[1] . '-' . $tgPF[0];
                }else{
                    $isias = "";
                }
                
                

                $coba[$key]['plan_start'] = $isips;
                $coba[$key]['plan_finish'] = $isipf;
                $coba[$key]['act_start'] = $isias;
                $coba[$key]['act_finish'] = $data['act_finish'];
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
            $coba[0]['ket'] = '';
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

    public function actionRekap() {
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
                ->join('JOIN', 'view_wo_spk as vw', 'dw.no_wo = vw.no_wo')
                ->join('LEFT JOIN', 'spk', 'vw.no_spk = spk.no_spk')
//                ->groupBy('dw.no_wo')
                ->where('dw.kd_kerja="BAG001"')
                ->orderBy($sort)
                ->select("dw.plan_start, vw.*,spk.jml_hari");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        session_start();
        $_SESSION['queryas'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['queryas'];
        \Yii::error($query);
        $query->limit(null);
        $query->offset(null);
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/wip", ['models' => $models]);
    }

    

    public function actionDelete() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($params['no_wo']);
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
