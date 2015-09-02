<?php

namespace app\controllers;

use Yii;
use app\models\Womasuk;
use app\models\Warna;
use app\models\Smalleks;
use app\models\Smallint;
use app\models\Minieks;
use app\models\Miniint;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class WomasukController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['post'],
                    'excel' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['post'],
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'cariwo' => ['get'],
                    'cari' => ['get'],
                    'copy' => ['get'],
                    'spk' => ['post'],
                    'warna' => ['post'],
                    'getspk' => ['post'],
                    'getsti' => ['post'],
                    'select' => ['post'],
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
        $query->from(['customer', 'chassis', 'tbl_karyawan', 'spk', 'serah_terima_in', 'warna', 'model'])
                ->where('chassis.kd_chassis = spk.kd_chassis
                        AND spk.nik = tbl_karyawan.nik
                        AND spk.kd_customer = customer.kd_cust AND serah_terima_in.no_spk = spk.no_spk AND serah_terima_in.kd_warna = warna.kd_warna AND spk.kd_model = model.kd_model')
                ->select("spk.no_spk as no_spk, tbl_karyawan.nama as sales,customer.nm_customer as customer, customer.nm_pemilik as pemilik, chassis.model_chassis as model_chassis,
                        chassis.merk as merk, chassis.tipe as tipe, serah_terima_in.kd_titipan, serah_terima_in.no_chassis, serah_terima_in.no_mesin,
                        serah_terima_in.tgl_terima, warna.warna as warna, model.model")
                ->andWhere(['like', 'spk.no_spk', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionGetspk() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('spk')
                ->join(' JOIN', 'customer as cs', 'spk.kd_customer = cs.kd_cust')
                ->join('JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->join(' JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
//                ->join(' JOIN', 'serah_terima_in as sti', 'sti.no_spk = spk.no_spk')
//                ->join(' JOIN', 'warna', 'sti.kd_warna = warna.kd_warna')
                ->join(' JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->select("*")
                ->where('spk.no_spk="' . $params['spk']['no_spk'] . '"');


        $command = $query->createCommand();
        $models = $command->queryOne();

        // kode
        $query = new Query;
        $query->from('wo_masuk')
                ->select('*')
                ->orderBy('in_spk_marketing DESC')
                ->where('year(in_spk_marketing) = "' . date("Y") . '"')
                ->limit(1);

        $command = $query->createCommand();
        $asu = $command->query()->read();

        if (empty($asu)) {
            if ($models['jenis'] == 'Mini Bus') {
                $kode = 'NV-' . date("y") . '0001';
            } else {
                $kode = 'NV-' . date("y") . '0001';
            }
        } else {
            $lastKode = substr($asu['no_wo'], -4) + 1;
            if ($models['jenis'] == 'Mini Bus') {
                $kode = 'NB-' . date("y") . substr('000' . $lastKode, -4);
            } else {
                $kode = 'NB-' . date("y") . substr('000' . $lastKode, -4);
            }
        }

        if ($models['jenis'] == 'Mini Bus') {
            $table = 'mini';
        } else {
            $table = 'small';
        }
//============================= EKSTERIOR ================================            
        // PLAT BODY
        $queryplat = new Query;
        $queryplat->from($table . '_eks')
                ->distinct("plat_body")
                ->select("plat_body");
        $commandplat = $queryplat->createCommand();
        $plat = $commandplat->queryAll();

        //VENTILASI ATAS
        $queryventilasi = new Query;
        $queryventilasi->from($table . '_eks')
                ->distinct("ventilasi_atas")
                ->select("ventilasi_atas");
        $commandventilasi = $queryventilasi->createCommand();
        $ventilasi = $commandventilasi->queryAll();

        //KACA SPION
        $queryspion = new Query;
        $queryspion->from($table . '_eks')
                ->distinct("kaca_spion")
                ->select("kaca_spion");
        $commandspion = $queryspion->createCommand();
        $spion = $commandspion->queryAll();

        //KACA DEPAN
        $querykdepan = new Query;
        $querykdepan->from($table . '_eks')
                ->distinct("kaca_depan")
                ->select("kaca_depan");
        $commandkdepan = $querykdepan->createCommand();
        $kdepan = $commandkdepan->queryAll();

        //KACA BELAKANG
        $querykbelakang = new Query;
        $querykbelakang->from($table . '_eks')
                ->distinct("kaca_belakang")
                ->select("kaca_belakang");
        $commandkbelakang = $querykbelakang->createCommand();
        $kbelakang = $commandkbelakang->queryAll();

        //KACA SAMPING
        $queryksamping = new Query;
        $queryksamping->from($table . '_eks')
                ->distinct("kaca_samping")
                ->select("kaca_samping");
        $commandsamping = $queryksamping->createCommand();
        $ksamping = $commandsamping->queryAll();

        //LAMPU DEPAN
        $queryldepan = new Query;
        $queryldepan->from($table . '_eks')
                ->distinct("lampu_depan")
                ->select("lampu_depan");
        $commandldepan = $queryldepan->createCommand();
        $ldepan = $commandldepan->queryAll();

        //LAMPU BELAKANG
        $querylbelakang = new Query;
        $querylbelakang->from($table . '_eks')
                ->distinct("lampu_belakang")
                ->select("lampu_belakang");
        $commandlbelakang = $querylbelakang->createCommand();
        $lbelakang = $commandlbelakang->queryAll();

        //PINTU DEPAN
        $querypdepan = new Query;
        $querypdepan->from($table . '_eks')
                ->distinct("pintu_depan")
                ->select("pintu_depan");
        $commandpdepan = $querypdepan->createCommand();
        $pdepan = $commandpdepan->queryAll();

        //PINTU PENUMPANG
        $queryppenumpang = new Query;
        $queryppenumpang->from($table . '_eks')
                ->distinct("pintu_penumpang")
                ->select("pintu_penumpang");
        $commandppenumpang = $queryppenumpang->createCommand();
        $ppenumpang = $commandppenumpang->queryAll();

        //PINTU BAGASI SAMPING
        $querypbagasi = new Query;
        $querypbagasi->from($table . '_eks')
                ->distinct("pintu_bagasi_samping")
                ->select("pintu_bagasi_samping");
        $commandpbagasi = $querypbagasi->createCommand();
        $pbagasi = $commandpbagasi->queryAll();

        //PINTU BELAKANG
        $querypbelakang = new Query;
        $querypbelakang->from($table . '_eks')
                ->distinct("pintu_belakang")
                ->select("pintu_belakang");
        $commandpbelakang = $querypbelakang->createCommand();
        $pbelakang = $commandpbelakang->queryAll();

        //WYPER SET
        $querywyper = new Query;
        $querywyper->from($table . '_eks')
                ->distinct("wyper_set")
                ->select("wyper_set");
        $commandwyper = $querywyper->createCommand();
        $wyper = $commandwyper->queryAll();

        //ANTI KARAT
        $queryakarat = new Query;
        $queryakarat->from($table . '_eks')
                ->distinct("anti_karat")
                ->select("anti_karat");
        $commandakarat = $queryakarat->createCommand();
        $akarat = $commandakarat->queryAll();

        //STRIP
        $querystrip = new Query;
        $querystrip->from($table . '_eks')
                ->distinct("strip")
                ->select("strip");
        $commandstrip = $querystrip->createCommand();
        $strip = $commandstrip->queryAll();

        //LETTER
        $queryletter = new Query;
        $queryletter->from($table . '_eks')
                ->distinct("letter")
                ->select("letter");
        $commandletter = $queryletter->createCommand();
        $letter = $commandletter->queryAll();

//============================= INTERIOR ================================ 
        if ($models['jenis'] == 'Mini Bus') {

            //PLAVON
            $queryplavon = new Query;
            $queryplavon->from('mini_int')
                    ->distinct("plavon")
                    ->select("plavon");
            $commandplavon = $queryplavon->createCommand();
            $plavon = $commandplavon->queryAll();

            //TRIMMING DECK
            $querytrimming = new Query;
            $querytrimming->from('mini_int')
                    ->distinct("trimming_deck")
                    ->select("trimming_deck");
            $commandtrimming = $querytrimming->createCommand();
            $trimming = $commandtrimming->queryAll();

            //DUCHTING LOUVER
            $queryduchting = new Query;
            $queryduchting->from('mini_int')
                    ->distinct("duchting_louver")
                    ->select("duchting_louver");
            $commandduchting = $queryduchting->createCommand();
            $duchting = $commandduchting->queryAll();

            //LAMPU PLAVON
            $querylplavon = new Query;
            $querylplavon->from('mini_int')
                    ->distinct("lampu_plavon")
                    ->select("lampu_plavon");
            $commandlplavon = $querylplavon->createCommand();
            $lplavon = $commandlplavon->queryAll();

            //LANTAI
            $querylantai = new Query;
            $querylantai->from('mini_int')
                    ->distinct("lantai")
                    ->select("lantai");
            $commandlantai = $querylantai->createCommand();
            $lantai = $commandlantai->queryAll();

            //KARPET
            $querykarpet = new Query;
            $querykarpet->from('mini_int')
                    ->distinct("karpet")
                    ->select("karpet");
            $commandkarpet = $querykarpet->createCommand();
            $karpet = $commandkarpet->queryAll();

            //SEAT 1
            $queryseat1 = new Query;
            $queryseat1->from('mini_int')
                    ->distinct("konf_seat1")
                    ->select("konf_seat1");
            $commandseat1 = $queryseat1->createCommand();
            $seat1 = $commandseat1->queryAll();

            //SEAT 2
            $queryseat2 = new Query;
            $queryseat2->from('mini_int')
                    ->distinct("konf_seat2")
                    ->select("konf_seat2");
            $commandseat2 = $queryseat2->createCommand();
            $seat2 = $commandseat2->queryAll();

            //SEAT 3
            $queryseat3 = new Query;
            $queryseat3->from('mini_int')
                    ->distinct("konf_seat3")
                    ->select("konf_seat3");
            $commandseat3 = $queryseat3->createCommand();
            $seat3 = $commandseat3->queryAll();

            //SEAT 4
            $queryseat4 = new Query;
            $queryseat4->from('mini_int')
                    ->distinct("konf_seat4")
                    ->select("konf_seat4");
            $commandseat4 = $queryseat4->createCommand();
            $seat4 = $commandseat4->queryAll();

            //SEAT 5
            $queryseat5 = new Query;
            $queryseat5->from('mini_int')
                    ->distinct("konf_seat5")
                    ->select("konf_seat5");
            $commandseat5 = $queryseat5->createCommand();
            $seat5 = $commandseat5->queryAll();

            //COVER SEAT
            $querycover = new Query;
            $querycover->from('mini_int')
                    ->distinct("cover_seat")
                    ->select("cover_seat");
            $commandcover = $querycover->createCommand();
            $cover = $commandcover->queryAll();

            //TOTAL SEAT
            $querytseat = new Query;
            $querytseat->from('mini_int')
                    ->distinct("total_seat")
                    ->select("total_seat");
            $commandtseat = $querytseat->createCommand();
            $tseat = $commandtseat->queryAll();

            //MERK AC
            $queryac = new Query;
            $queryac->from('mini_int')
                    ->distinct("merk_ac")
                    ->select("merk_ac");
            $commandac = $queryac->createCommand();
            $ac = $commandac->queryAll();

            $interior = ['plavon' => $plavon, 'trimming' => $trimming, 'duchting' => $duchting, 'lplavon' => $lplavon, 'lantai' => $lantai,
                'karpet' => $karpet, 'seat1', $seat1, 'seat2', $seat2, 'seat3', $seat3, 'seat4', $seat4, 'seat5', $seat5,
                'cover' => $cover, 'total_seat' => $tseat, 'ac' => $ac];
        } else {
            //PLAVON
            $queryplavon = new Query;
            $queryplavon->from('small_int')
                    ->distinct("plavon")
                    ->select("plavon");
            $commandplavon = $queryplavon->createCommand();
            $plavon = $commandplavon->queryAll();

            // BAGASI DALAM
            $querybdalam = new Query;
            $querybdalam->from('small_int')
                    ->distinct("bagasi_dalam")
                    ->select("bagasi_dalam");
            $commanbdalam = $querybdalam->createCommand();
            $bdalam = $commanbdalam->queryAll();

            // BAGASI DALAM
            $querybdalam = new Query;
            $querybdalam->from('small_int')
                    ->distinct("bagasi_dalam")
                    ->select("bagasi_dalam");
            $commanbdalam = $querybdalam->createCommand();
            $bdalam = $commanbdalam->queryAll();

            //DUCHTING LOUVER
            $queryduchting = new Query;
            $queryduchting->from('small_int')
                    ->distinct("duchting_louver")
                    ->select("duchting_louver");
            $commandduchting = $queryduchting->createCommand();
            $duchting = $commandduchting->queryAll();

            //TRIMMING DECK
            $querytrimming = new Query;
            $querytrimming->from('small_int')
                    ->distinct("trimming_deck")
                    ->select("trimming_deck");
            $commandtrimming = $querytrimming->createCommand();
            $trimming = $commandtrimming->queryAll();

            //LAMPU PLAVON
            $querylplavon = new Query;
            $querylplavon->from('small_int')
                    ->distinct("lampu_plavon")
                    ->select("lampu_plavon");
            $commandlplavon = $querylplavon->createCommand();
            $lplavon = $commandlplavon->queryAll();

            //DASHBOARD
            $querydashboard = new Query;
            $querydashboard->from('small_int')
                    ->distinct("lampu_plavon")
                    ->select("lampu_plavon");
            $commanddashboard = $querylplavon->createCommand();
            $dashboard = $commanddashboard->queryAll();

            //LANTAI
            $querylantai = new Query;
            $querylantai->from('small_int')
                    ->distinct("lantai")
                    ->select("lantai");
            $commandlantai = $querylantai->createCommand();
            $lantai = $commandlantai->queryAll();

            //KARPET
            $querykarpet = new Query;
            $querykarpet->from('small_int')
                    ->distinct("karpet")
                    ->select("karpet");
            $commandkarpet = $querykarpet->createCommand();
            $karpet = $commandkarpet->queryAll();

            //PEREDAM
            $queryperedam = new Query;
            $queryperedam->from('small_int')
                    ->distinct("peredam")
                    ->select("peredam");
            $commandperedam = $queryperedam->createCommand();
            $peredam = $commandperedam->queryAll();

            //PEGANGAN TANGAN ATAS
            $queryptatas = new Query;
            $queryptatas->from('small_int')
                    ->distinct("pegangan_tangan_atas")
                    ->select("peredam");
            $commandpatas = $queryptatas->createCommand();
            $patas = $commandpatas->queryAll();

            //PENGAMAN PENUMPANG
            $querypengaman_penumpang = new Query;
            $querypengaman_penumpang->from('small_int')
                    ->distinct("pengaman_penumpang")
                    ->select("pengaman_penumpang");
            $commandpengaman_penumpang = $querypengaman_penumpang->createCommand();
            $pengaman_penumpang = $commandpengaman_penumpang->queryAll();

            //PENGAMAN KACA SAMPING
            $querypengaman_kaca = new Query;
            $querypengaman_kaca->from('small_int')
                    ->distinct("pengaman_kaca_samping")
                    ->select("pengaman_kaca_samping");
            $commandpengaman_kaca = $querypengaman_kaca->createCommand();
            $pengaman_kaca = $commandpengaman_kaca->queryAll();

            //PENGAMAN DRIVER
            $querypengaman_driver = new Query;
            $querypengaman_driver->from('small_int')
                    ->distinct("pengaman_driver")
                    ->select("pengaman_driver");
            $commandpengaman_driver = $querypengaman_driver->createCommand();
            $pengaman_driver = $commandpengaman_driver->queryAll();

            //GORDYN
            $querygordyn = new Query;
            $querygordyn->from('small_int')
                    ->distinct("gordyn")
                    ->select("gordyn");
            $commandgordyn = $querygordyn->createCommand();
            $gordyn = $commandgordyn->queryAll();

            //DRIVER FAN
            $querydriver_fan = new Query;
            $querydriver_fan->from('small_int')
                    ->distinct("driver_fan")
                    ->select("driver_fan");
            $commanddriver_fan = $querydriver_fan->createCommand();
            $driver_fan = $commanddriver_fan->queryAll();

            //RADIO TAPE
            $queryradio_tape = new Query;
            $queryradio_tape->from('small_int')
                    ->distinct("radio_tape")
                    ->select("radio_tape");
            $commandradio_tape = $queryradio_tape->createCommand();
            $radio_tape = $commandradio_tape->queryAll();

            //SPEK SEAT
            $queryspek_seat = new Query;
            $queryspek_seat->from('small_int')
                    ->distinct("spek_seat")
                    ->select("spek_seat");
            $commandspek_seat = $queryspek_seat->createCommand();
            $spek_seat = $commandspek_seat->queryAll();

            //DRIVER SEAT
            $querydriver_seat = new Query;
            $querydriver_seat->from('small_int')
                    ->distinct("driver_seat")
                    ->select("driver_seat");
            $commanddriver_seat = $querydriver_seat->createCommand();
            $driver_seat = $commanddriver_seat->queryAll();

            //COVER SEAT
            $querycover_seat = new Query;
            $querycover_seat->from('small_int')
                    ->distinct("cover_seat")
                    ->select("cover_seat");
            $commandcover_seat = $querycover_seat->createCommand();
            $cover_seat = $commandcover_seat->queryAll();

            //OPTIONAL SEAT
            $queryoptional_seat = new Query;
            $queryoptional_seat->from('small_int')
                    ->distinct("optional_seat")
                    ->select("optional_seat");
            $commandoptional_seat = $queryoptional_seat->createCommand();
            $optional_seat = $commandoptional_seat->queryAll();

            //TOTAL SEAT
            $querytotal_seat = new Query;
            $querytotal_seat->from('small_int')
                    ->distinct("total_seat")
                    ->select("total_seat");
            $commandtotal_seat = $querytotal_seat->createCommand();
            $total_seat = $commandtotal_seat->queryAll();

            //MERK AC
            $querymerk_ac = new Query;
            $querymerk_ac->from('small_int')
                    ->distinct("merk_ac")
                    ->select("merk_ac");
            $commandmerk_ac = $querymerk_ac->createCommand();
            $merk_ac = $commandmerk_ac->queryAll();

            $interior = ['plavon' => $plavon, 'bagasi_dalam' => $bdalam, 'duchting' => $duchting, 'trimming' => $trimming, 'lplavon' => $lplavon, 'dashboard' => $dashboard,
                'lantai' => $lantai, 'karpet' => $karpet, 'peredam' => $peredam, 'pegangan_tangan_atas' => $patas, 'pengaman_penumpang' => $pengaman_penumpang, 'pengaman_kaca' => $pengaman_kaca,
                'pengaman_driver' => $pengaman_driver, 'gordyn' => $gordyn, 'driver_fan' => $driver_fan, 'radio_tape' => $radio_tape, 'spek_seat' => $spek_seat, 'driver_seat' => $driver_seat, 'cover_seat' => $cover_seat,
                'optional_seat' => $optional_seat, 'total_seat' => $total_seat, 'ac' => $merk_ac];
        }


        $exterior = ['plat' => $plat, 'ventilasi' => $ventilasi, 'spion' => $spion, 'kdepan' => $kdepan, 'kbelakang' => $kbelakang,
            'ksamping' => $ksamping, 'ldepan' => $ldepan, 'lbelakang' => $lbelakang, 'pdepan' => $pdepan, 'ppenumpang' => $ppenumpang, 'pbagasi' => $pbagasi,
            'pbelakang' => $pbelakang, 'wyper' => $wyper, 'akarat' => $akarat, 'strip' => $strip, 'letter' => $letter];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'spk' => $models, 'code' => $kode, 'eksterior' => $exterior,'interior'=>$interior));
    }

    public function actionGetsti() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $query = new Query;
        $query->from('serah_terima_in as sti')
//                ->join(' JOIN', 'customer as cs', 'sti.kd_cust = cs.kd_cust')
                ->join(' JOIN', 'warna', 'sti.kd_warna = warna.kd_warna')
                ->select("*")
                ->where('sti.kd_titipan="' . $params['titipan']['kd_titipan'] . '"');


        $command = $query->createCommand();
        $models = $command->queryOne();
//        Yii::error($models);
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'sti' => $models));
    }

    public function actionWarna() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('warna')
                ->select("*");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'warna' => $models));
    }

    public function actionSpk() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('spk')
                ->select("*");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'spk' => $models));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "wo_masuk.no_wo ASC";
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
                ->from('wo_masuk')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
//                ->join('LEFT JOIN', 'small_eks', 'wo_masuk.no_wo = small_eks.no_wo') // customer
//                ->join('LEFT JOIN', 'mini_eks', 'wo_masuk.no_wo = mini_eks.no_wo') // customer
//                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
                ->join('JOIN', 'serah_terima_in', 'spk.no_spk = serah_terima_in.no_spk') // customer
//                ->join('JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'no_spk') {
                    $query->andFilterWhere(['like', 'spk.' . $key, $val]);
                } elseif ($key == 'jenis') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'wo_masuk.' . $key, $val]);
                } elseif ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'kd_titipan') {
                    $query->andFilterWhere(['like', 'serah_terima_in.' . $key, $val]);
                } elseif ($key == 'no_chassis') {
                    $query->andFilterWhere(['like', 'serah_terima_in.' . $key, $val]);
                } elseif ($key == 'no_mesin') {
                    $query->andFilterWhere(['like', 'serah_terima_in.' . $key, $val]);
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

    public function actionView() {
        $params = json_decode(file_get_contents("php://input"), true);

        $model = $this->findModel($params['no_wo']);
        $data = $model->attributes;
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
                ->select("wo_masuk.*,sales.nama as sales, customer.nm_customer as customer, customer.nm_pemilik as pemilik,
                            chassis.model_chassis as model_chassis, chassis.merk as merk, chassis.tipe as tipe, model.model, serah_terima_in.no_chassis as no_rangka,
                            serah_terima_in.no_mesin, chassis.jenis, serah_terima_in.tgl_terima");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $data) {
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
            /// query for kd titipan
            $nowo = \app\models\Serahterimain::findOne($data['kd_titipan']);
            $data['titipan'] = (!empty($nowo)) ? $nowo->attributes : array();
            $data['titipan']['warna'] = (!empty($nowo)) ? $nowo->warna->attributes : array();

//            $quersti = new Query;
//            $quersti->from('serah_terima_in as sti')
//                    ->join(' JOIN', 'warna', 'sti.kd_warna = warna.kd_warna')
//                    ->select("*")
//                    ->where('sti.kd_titipan="' . $data['kd_titipan'] . '"');
//
//
//            $commandsti = $quersti->createCommand();
//            $modelsti = $commandsti->queryAll();
//            $data['titipan'] = $modelsti;
        }




        // quey for kode
        $query8 = new Query;
        $query8->from('wo_masuk')
                ->select('*')
                ->orderBy('in_spk_marketing DESC')
                ->where('year(in_spk_marketing) = "' . date("Y") . '"')
                ->limit(1);

        $command8 = $query8->createCommand();
        $kerek = $command8->query()->read();

        if ($asu['jenis'] == "Small Bus") {
            // kode

            if (empty($kerek)) {

                $kode = 'NV-' . date("y") . '0001';
            } else {
                $lastKode = substr($kerek['no_wo'], -4) + 1;

                $kode = 'NV-' . date("y") . substr('000' . $lastKode, -4);
            }

            // eksterior
            $eksterior = new Query;
            $eksterior->from('small_eks')
                    ->join('JOIN', 'warna', 'small_eks.warna = warna.kd_warna') // customer
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();
            $eks = array();
            foreach ($models2 as $r) {
                $eks = $r;
                $eks['warna'] = ['kd_warna' => $r['kd_warna'], 'warna' => $r['warna']];
                $eks['warna2'] = ['kd_warna' => $r['kd_warna'], 'warna' => $r['warna2']];
            }


            // interior
            $interior = new Query;
            $interior->from('small_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        } else {
            // kode

            if (empty($kerek)) {

                $kode = 'NB-' . date("y") . '0001';
            } else {
                $lastKode = substr($kerek['no_wo'], -4) + 1;

                $kode = 'NB-' . date("y") . substr('000' . $lastKode, -4);
            }
            // eksterior
            $eksterior = new Query;
            $eksterior->from('mini_eks')
                    ->join('JOIN', 'warna', 'mini_eks.warna = warna.kd_warna') // customer
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();
            $eks = array();
            foreach ($models2 as $r) {
                $eks = $r;
                $eks['warna'] = ['kd_warna' => $r['kd_warna'], 'warna' => $r['warna']];
                $eks['warna2'] = ['kd_warna' => $r['warna2'], 'warna' => $r['warna']];
            }

            // interior
            $interior = new Query;
            $interior->from('mini_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        }
        $data['spk'] = ['no_spk' => $model['no_spk']];
        $data['no_wo'] = ['no_wo' => $model['no_wo']];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'det' => $asu, 'code' => $kode, 'eksterior' => $eks, 'interior' => $models3), JSON_PRETTY_PRINT);
    }

    public function actionSelect() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($params['no_wo']);
        $data = $model->attributes;
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
                ->select("wo_masuk.*,sales.nama as sales, customer.nm_customer as customer, customer.nm_pemilik as pemilik,
                            chassis.model_chassis as model_chassis, chassis.merk as merk, chassis.tipe as tipe, model.model, serah_terima_in.no_chassis as no_rangka,
                            serah_terima_in.no_mesin, chassis.jenis, serah_terima_in.tgl_terima, warna.warna");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $data) {
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
            $asu['no_wo'] = (isset($data['no_wo'])) ? $data['no_wo'] : '-';
            $asu['warna'] = (isset($data['warna'])) ? $data['warna'] : '-';

            $nowo = \app\models\Serahterimain::findOne($data['kd_titipan']);
            $data['titipan'] = (!empty($nowo)) ? $nowo->attributes : array();
            $data['titipan']['warna'] = (!empty($nowo)) ? $nowo->warna->attributes : array();
        }
        if ($asu['jenis'] == "Small Bus") {
            // eksterior
            $eksterior = new Query;
            $eksterior->from('small_eks')
                    ->join('JOIN', 'warna', 'small_eks.warna = warna.kd_warna') // customer
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();
            $eks = array();
            foreach ($models2 as $r) {
                $eks = $r;
                $eks['warna'] = ['kd_warna' => $r['kd_warna'], 'warna' => $r['warna']];
                $eks['warna2'] = ['kd_warna' => $r['kd_warna'], 'warna' => $r['warna2']];
            }


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
                    ->join('JOIN', 'warna', 'mini_eks.warna = warna.kd_warna') // customer
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();
            $eks = array();
            foreach ($models2 as $r) {
                $eks = $r;
                $eks['warna'] = ['kd_warna' => $r['kd_warna'], 'warna' => $r['warna']];
                $eks['warna2'] = ['kd_warna' => $r['warna2'], 'warna' => $r['warna']];
            }

            // interior
            $interior = new Query;
            $interior->from('mini_int')
                    ->select("*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $interior->createCommand();
            $models3 = $command3->queryAll();
        }
        $data['spk'] = ['no_spk' => $model['no_spk']];
        $data['no_wo'] = ['no_wo' => $model['no_wo']];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'det' => $asu, 'eksterior' => $eks, 'interior' => $models3), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        Yii::error($params);
//        $model = new Womasuk();
//        $model->attributes = $params['womasuk'];
//
//
//        if ($model->save()) {
////            save small eksterior
//            if ($params['womasuk']['jenis'] == "Small Bus") {
//                $smaleks = new Smalleks();
//                $smaleks->attributes = $params['eksterior'];
//                $smaleks->no_wo = $model->no_wo;
////                $smaleks->warna = (isset($params['eksterior']['warna']['kd_warna'])) ? $params['eksterior']['warna']['kd_warna'] : '';
////                $smaleks->warna2 = (isset($params['eksterior']['warna2']['kd_warna'])) ? $params['eksterior']['warna2']['kd_warna'] : '';
//                //warna 1
//                $warna = Warna::findOne($params['eksterior']['warna']['kd_warna']);
//                if (empty($warna)) {
//                    $warna = new Warna();
//                }
//                $warna->attributes = $params;
//                if ($warna->save()) {
//                    $smaleks->warna = $warna->kd_warna;
//                }
//                //warna 2
//                $warna = Warna::findOne($params['eksterior']['warna2']['kd_warna']);
//                if (empty($warna)) {
//                    $warna = new Warna();
//                }
//                $warna->attributes = $params;
//                if ($warna->save()) {
//                    $smaleks->warna2 = $warna->kd_warna;
//                }
//
//                $smaleks->save();
//
////                save small interior
//                $smallint = new Smallint();
//                $smallint->attributes = $params['interior'];
//                $smallint->no_wo = $model->no_wo;
//                $smallint->save();
//            } else {
//                //  save mini bus ekterior
//                $minieks = new Minieks();
//                $minieks->attributes = $params['eksterior'];
//                $minieks->no_wo = $model->no_wo;
//                $minieks->warna = (isset($params['eksterior']['warna']['kd_warna'])) ? $params['eksterior']['warna']['kd_warna'] : '';
//                $minieks->warna2 = (isset($params['eksterior']['warna2']['kd_warna'])) ? $params['eksterior']['warna2']['kd_warna'] : '';
//                //warna 1
//                $warna = Warna::findOne($params['eksterior']['warna']['kd_warna']);
//                if (empty($warna)) {
//                    $warna = new Warna();
//                }
//                $warna->attributes = $params;
//                if ($warna->save()) {
//                    $smaleks->warna = $warna->kd_warna;
//                }
//                //warna 2
//                $warna = Warna::findOne($params['eksterior']['warna2']['kd_warna']);
//                if (empty($warna)) {
//                    $warna = new Warna();
//                }
//                $warna->attributes = $params;
//                if ($warna->save()) {
//                    $smaleks->warna2 = $warna->kd_warna;
//                }
//
//                $minieks->save();
//
//                // save interior mini bus
//                $miniint = new Miniint();
//                $miniint->attributes = $params['interior'];
//                $miniint->no_wo = $model->no_wo;
//                $miniint->save();
//            }
//            $this->setHeader(200);
//            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
//        } else {
//            $this->setHeader(400);
//            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
//        }
    }

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
        Yii::error($params);
//        $model = $this->findModel($params['womasuk']['no_wo']);
        $model = WoMasuk::find()->where('no_wo="' . $params['eksterior']['no_wo'] . '"')->one();

        $model->attributes = $params['womasuk'];



        if ($model->save()) {
            if ($params['womasuk']['jenis'] == "Small Bus") {

                // small eksterior
                $smalleks = Smalleks::find()->where('no_wo="' . $model->no_wo . '"')->one();
                $smalleks->attributes = $params['eksterior'];
                $smalleks->no_wo = $model->no_wo;
                $smalleks->warna = (isset($params['eksterior']['warna']['kd_warna'])) ? $params['eksterior']['warna']['kd_warna'] : '';
                $smalleks->warna2 = (isset($params['eksterior']['warna2']['kd_warna'])) ? $params['eksterior']['warna2']['kd_warna'] : '';
                $smalleks->save();

                // small interior
                $smallint = Smallint::find()->where('no_wo="' . $model->no_wo . '"')->one();
                $smallint->attributes = $params['interior'];
                $smallint->no_wo = $model->no_wo;
                $smallint->save();
            } else {
                // mini eksterior
                $minieks = Minieks::find()->where('no_wo="' . $model->no_wo . '"')->one();
                $minieks->attributes = $params['eksterior'];
                $minieks->no_wo = $model->no_wo;
                $minieks->warna = (isset($params['eksterior']['warna']['kd_warna'])) ? $params['eksterior']['warna']['kd_warna'] : '';
                $minieks->warna2 = (isset($params['eksterior']['warna2']['kd_warna'])) ? $params['eksterior']['warna2']['kd_warna'] : '';
                $minieks->save();

                // mini interior
                $miniint = Miniint::find()->where('no_wo="' . $model->no_wo . '"')->one();
                $miniint->attributes = $params['interior'];
                $miniint->no_wo = $model->no_wo;
                $miniint->save();
            }

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expmaster/barang", ['models' => $models]);
    }

    public function actionCariwo() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('wo_masuk as wo')
                ->join('LEFT JOIN', 'serah_terima_in as se', 'wo.kd_titipan = se.kd_titipan')
                ->join('LEFT JOIN', 'chassis as ch', ' ch.kd_chassis= se.kd_chassis')
                ->join('LEFT JOIN', 'spk as sp', ' wo.no_spk= sp.no_spk')
                ->join('LEFT JOIN', 'model as mo', ' mo.kd_model= sp.kd_model')
                ->where(['like', 'wo.no_wo', $params['nama']])
                ->limit(10)
                ->select("wo.no_wo as no_wo,ch.merk as merk,ch.jenis as jenis, mo.model as model");

        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach($models as $key => $val){
            if(strtolower($val['jenis'])  == "mini bus"){
                $miniExt = Minieks::find()->where('no_wo="'.$val['no_wo'].'"')->one();
                $models[$key]['kd_warna'] = (!empty($miniExt->waarna->kd_warna)) ? $miniExt->waarna->kd_warna : '';
                $models[$key]['warna'] = (!empty($miniExt->waarna->warna)) ? $miniExt->waarna->warna : '';
            }elseif(strtolower($val['jenis']) == "small bus"){
                $smallExt = Smalleks::find()->where('no_wo="'.$val['no_wo'].'"')->one();
                $models[$key]['kd_warna'] = (!empty($smallExt->waarna->kd_warna)) ? $smallExt->waarna->kd_warna : '';
                $models[$key]['warna'] = (!empty($smallExt->waarna->warna)) ? $smallExt->waarna->warna : '';
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCopy() {
        $params = $_REQUEST;
        $model = Womasuk::find()
                        ->where('no_wo like "%' . $params['nama'] . '%"')
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
