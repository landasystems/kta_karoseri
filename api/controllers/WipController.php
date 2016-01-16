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
                    'excelschedule' => ['get'],
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
                ->join('LEFT JOIN', 'bagian', 'bagian.kd_bag = wip.kd_kerja')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = wip.nik')
                ->where('wip.no_wo = "' . $params['no_wo'] . '"')
                ->orderBy("bagian.urutan ASC")
                ->select('wip.*,bagian.*, tk.nik, tk.nama');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();

        $coba = array();
        if (!empty($detail)) {
            foreach ($detail as $key => $data) {
                $coba[$key] = $data;

                if (!empty($data['plan_start']) && $data['plan_start'] != "-" && isset($data['plan_start'])) {

                    if ($data['plan_start'] == '1970-01-01') {
                        $isips = "";
                    } else {
                        $tgPS = explode('/', $data['plan_start']);
                        $isips = $tgPS[2] . '-' . $tgPS[1] . '-' . $tgPS[0];
                    }
                } else {
                    $isips = "";
                }

                if (!empty($data['plan_finish']) && $data['plan_finish'] != "-" && isset($data['plan_finish'])) {

                    if ($data['plan_finish'] == '1970-01-01') {
                        $isipf = "";
                    } else {
                        $tgPF = explode('/', $data['plan_finish']);
                        $isipf = $tgPF[2] . '-' . $tgPF[1] . '-' . $tgPF[0];
                    }
                } else {
                    $isipf = "";
                }

                if (!empty($data['act_start']) && $data['act_start'] != "-" && isset($data['act_start'])) {

                    if ($data['act_start'] == '1970-01-01') {
                        $isias = "";
                    } else {
                        $tgPF = explode('/', $data['act_start']);
                        $isias = $tgPF[2] . '-' . $tgPF[1] . '-' . $tgPF[0];
                    }
                } else {
                    $isias = "";
                }
                if (!empty($data['act_finish']) && isset($data['act_finish'])) {
                    if ($data['act_finish'] == '1970-01-01') {
                        $isiaf = "";
                    } else {
                        $isiaf = $data['act_finish'];
                    }
                } else {
                    $isiaf = "";
                }



                $coba[$key]['plan_start'] = $isips;
                $coba[$key]['plan_finish'] = $isipf;
                $coba[$key]['act_start'] = $isias;
                $coba[$key]['ket'] = $data['ket'];
                $coba[$key]['act_finish'] = $isiaf;
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
                ->orderBy("bagian.urutan ASC")
                ->select("*");

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
                ->join('LEFT JOIN', 'delivery as d', 'dw.no_wo = d.no_wo')
                ->join('LEFT JOIN', 'spk', 'vw.no_spk = spk.no_spk')
//                ->groupBy('dw.no_wo')
                ->where('dw.kd_kerja="BAG001" and d.no_wo IS NULL and (dw.act_start IS NOT NULL or dw.act_start="" or dw.act_start="0000-00-00")')
                ->orderBy($sort)
                ->select("dw.act_start,dw.kd_kerja, vw.*,spk.jml_hari");

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

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
        \Yii::error($params['detWip']);
        $deleteAll = Wip::deleteAll('no_wo="' . $params['wip']['no_wo']['no_wo'] . '"');
        $tglibur = array();
        $libur = \app\models\TblKalender::find()->where('YEAR(tgl)=2015')->all();
        foreach ($libur as $as) {
            $tglibur[] = $as['tgl'];
        }
        foreach ($params['detWip'] as $data) {
            $model = new Wip();
            $model->no_wo = isset($params['wip']['no_wo']['no_wo']) ? $params['wip']['no_wo']['no_wo'] : '-';
            $model->kd_kerja = $data['proses']['kd_bag'];
            $model->plan_start = (!empty($data['plan_start'])) ? date('d/m/Y', strtotime($data['plan_start'])) : '';
            $model->plan_finish = (!empty($data['plan_finish'])) ? date('d/m/Y', strtotime($data['plan_finish'])) : '';
            $model->act_start = (!empty($data['act_start'])) ? date('d/m/Y', strtotime($data['act_start'])) : '';
            $model->act_finish = (!empty($data['act_finish'])) ? date('Y-m-d', strtotime($data['act_finish'])) : '';
            $model->ket = $data['ket'];
            $model->hasil = isset($data['hasil']) ? $data['hasil'] : null;
            $model->nik = isset($data['pemborong']['nik']) ? $data['pemborong']['nik'] : '-';
//            $model->hk = 3;
            //============================== HITUNG HARI KERJA ===============================
            if (!empty($data['act_finish'])) {

                $babi = date('Y-m-d', strtotime($data['act_start']));
                $pecahP = explode("-", $babi);
                $dateP = (isset($pecahP[2])) ? $pecahP[2] : 0;
                $monthP = (isset($pecahP[1])) ? $pecahP[1] : 0;
                $yearP = (isset($pecahP[0])) ? $pecahP[0] : 0;

                $babi2 = date('Y-m-d', strtotime($data['act_finish']));
                $pecahP2 = explode("-", $babi2);
                $dateP2 = (isset($pecahP2[2])) ? $pecahP2[2] : 0;
                $monthP2 = (isset($pecahP2[1])) ? $pecahP2[1] : 0;
                $yearP2 = (isset($pecahP2[0])) ? $pecahP2[0] : 0;

                // mencari total selisih hari dari tanggal awal dan akhir
                $jdP = GregorianToJD($monthP, $dateP, $yearP);
                $jdP2 = GregorianToJD($monthP2, $dateP2, $yearP2);
                $ad = $jdP2 - $jdP;
                $sHK2 = ($ad == 0) ? '1' : $ad;
// =================HTUNG HARI LIBUR DAN MINGGU BODY WELDING=================
                $libur1 = '';
                $libur2 = '';
                for ($i = 1; $i <= $sHK2; $i++) {

                    // menentukan tanggal pada hari ke-i dari tanggal awal
                    $tanggal = mktime(0, 0, 0, $monthP, $dateP + $i, $yearP);
                    $tglstr = date("Y-m-d", $tanggal);

                    // yang masuk dalam daftar tanggal merah selain minggu
                    if (in_array($tglstr, $tglibur)) {
                        $libur1++;
                    }

                    // yang merupakan hari minggu
                    $minggu = date("l", strtotime($tglstr));

                    if ($minggu == 'Sunday') {
                        $libur2++;
                    }
                }
                Yii::error($libur1 . '-' . $libur2);
                $sHKs = $sHK2 - $libur1 - $libur2;
                $HK = $sHKs + 1;

                $model->hk = $HK;
            } else {
                $model->hk = '';
            }

            $model->save();
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->limit(null);
        $query->offset(null);
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/wip", ['models' => $models]);
    }
    public function actionExcelschedule() {
        session_start();
        $query = $_SESSION['queryas'];
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
