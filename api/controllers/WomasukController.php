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
                    'bukaprint' => ['post'],
                    'proyek' => ['get'],
                    'sqlprint' => ['get'],
                    'print' => ['get'],
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

    public function actionBukaprint() {
        $params = json_decode(file_get_contents("php://input"), true);
        \Yii::error($params);
    }

    public function actionProyek() {
        $params = $_REQUEST;
        $filter_name = $params['kd'] . "-2" . date("y");
        $kata = strlen($params['kd']);
        if ($kata == 2) {
            $jml = 6;
        } else {
            $jml = 7;
        }
        $query = new Query;
        $query->from('wo_masuk ')
                ->select("no_wo")
                ->where(['SUBSTR(no_wo,1,' . $jml . ')' => $filter_name])
                ->orderBy('no_wo DESC')
                ->limit(1);
        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = (substr($models['no_wo'], $jml) + 1);
//        $kode = $filter_name.substr('0000' . $kode_mdl, strlen($kode_mdl));
        $kode = $filter_name . $kode_mdl;
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $kode, 'hasil' => $models));
    }

    public function actionGetspk() {
        $params = json_decode(file_get_contents("php://input"), true);
       
        if (!empty($params['spk']['no_spk'])) {
            $spk = $params['spk']['no_spk'];
        } else {
            $spk = $params['no_wo'];
        }
        $query = new Query;
        $query->from('spk')
                ->join(' JOIN', 'customer as cs', 'spk.kd_customer = cs.kd_cust')
                ->join('JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->join(' JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
//                ->join(' JOIN', 'serah_terima_in as sti', 'sti.no_spk = spk.no_spk')
//                ->join(' JOIN', 'warna', 'sti.kd_warna = warna.kd_warna')
                ->join(' JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->select("*")
                ->where('spk.no_spk="' . $spk . '"');


        $command = $query->createCommand();
        $models = $command->queryOne();
        
        $query2 = new Query;
        $query2->from('view_wo_spk')
                ->where('no_spk = "' . $spk . '"')
                ->select("jenis");
        $command2 = $query2->createCommand();
        $models2 = $command->query()->read();
        
        $jenis = (!empty($models2['jenis'])) ? $models2['jenis'] : $params['jenis'];
        
        // jenis kendaaraan
        if (!empty($params['spk']['no_spk'])) {
        } else {
            $query2 = new Query;
            $query2->from('view_wo_spk')
                    ->where('nowo = "' . $params['no_wo'] . '"')
                    ->select("jenis");
            $command2 = $query2->createCommand();
            $models2 = $command->query()->read();
        }

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
            if ($jenis == 'Mini Bus') {
//                $kode = 'NV-' . date("y") . '0001';
                $kode = 'NV-';
            } else {
//                $kode = 'NV-' . date("y") . '0001';
                $kode = 'NV-';
            }
        } else {
            $lastKode = substr($asu['no_wo'], -4) + 1;
            if ($jenis == 'Mini Bus') {
//                $kode = 'NB-' . date("y") . substr('000' . $lastKode, -4);
                $kode = 'NB-';
            } else {
//                $kode = 'NB-' . date("y") . substr('000' . $lastKode, -4);
                $kode = 'NB-';
            }
        }

        if ($jenis == 'Mini Bus') {
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
        if ($jenis == 'Mini Bus') {

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
            $queryseat_satu = new Query;
            $queryseat_satu->from('mini_int')
                    ->distinct("konf_seat1")
                    ->select("konf_seat1");
            $commandseat_satu = $queryseat_satu->createCommand();
            $seat_satu = $commandseat_satu->queryAll();

            //SEAT 2
            $queryseat_dua = new Query;
            $queryseat_dua->from('mini_int')
                    ->distinct("konf_seat2")
                    ->select("konf_seat2");
            $commandseat_dua = $queryseat_dua->createCommand();
            $seat_dua = $commandseat_dua->queryAll();

            //SEAT 3
            $queryseat_tiga = new Query;
            $queryseat_tiga->from('mini_int')
                    ->distinct("konf_seat3")
                    ->select("konf_seat3");
            $commandseat_tiga = $queryseat_tiga->createCommand();
            $seat_tiga = $commandseat_tiga->queryAll();

            //SEAT 4
            $queryseat_empat = new Query;
            $queryseat_empat->from('mini_int')
                    ->distinct("konf_seat4")
                    ->select("konf_seat4");
            $commandseat_empat = $queryseat_empat->createCommand();
            $seat_empat = $commandseat_empat->queryAll();

            //SEAT 5
            $queryseat_lima = new Query;
            $queryseat_lima->from('mini_int')
                    ->distinct("konf_seat5")
                    ->select("konf_seat5");
            $commandseat_lima = $queryseat_lima->createCommand();
            $seat_lima = $commandseat_lima->queryAll();

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
                'karpet' => $karpet, 'seat_satu' => $seat_satu, 'seat_dua' => $seat_dua, 'seat_tiga' => $seat_tiga, 'seat_empat' => $seat_empat, 'seat_lima' => $seat_lima,
                'cover_seat' => $cover, 'total_seat' => $tseat, 'ac' => $ac];
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
                    ->distinct("dashboard")
                    ->select("dashboard");
            $commanddashboard = $querydashboard->createCommand();
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
                    ->select("pegangan_tangan_atas");
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
//        \Yii::error($exterior);
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'spk' => $models, 'code' => $kode, 'eksterior' => $exterior, 'interior' => $interior));
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
        $sort = "wo_masuk.no_wo DESC";
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
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('lEFT JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('LEFT JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('LEFT JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
//                ->join('LEFT JOIN', 'small_eks', 'wo_masuk.no_wo = small_eks.no_wo') // customer
//                ->join('LEFT JOIN', 'mini_eks', 'wo_masuk.no_wo = mini_eks.no_wo') // customer
//                ->join('JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
                ->join('LEFT JOIN', 'serah_terima_in', 'wo_masuk.kd_titipan = serah_terima_in.kd_titipan') // customer
//                ->join('JOIN', 'warna', 'serah_terima_in.kd_warna = warna.kd_warna') // customer
                ->orderBy($sort)
//                ->groupBy('no_wo')
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
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('LEFT JOIN', 'chassis', 'spk.kd_chassis = chassis.kd_chassis') // model chassis, merk, jenis, 
                ->join('LEFT JOIN', 'tbl_karyawan as sales', 'spk.nik= sales.nik') // sales
                ->join('LEFT JOIN', 'customer', 'spk.kd_customer = customer.kd_cust') // customer
                ->join('LEFT JOIN', 'model', 'spk.kd_model = model.kd_model') // customer
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

        $query2 = new Query;
        $query2->from('view_wo_spk')
                ->where('nowo = "' . $params['no_wo'] . '"')
                ->select("jenis");
        $command2 = $query2->createCommand();
        $models2 = $command->query()->read();
        
        $jenis = (!empty($models2['jenis'])) ? $models2['jenis'] : $params['jenis'];
        
        if ($jenis == "Small Bus") {
            // eksterior
            $eksterior = new Query;
            $eksterior->from('small_eks')
                    ->join('JOIN', 'warna', 'small_eks.warna = warna.kd_warna') // customer
                    ->select("warna.*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();
            $eks = array();
            foreach ($models2 as $r) {
                $eks = $r;
                $eks['warna'] = $r;
            }
            $eksterior2 = new Query;
            $eksterior2->from('small_eks')
                    ->join('JOIN', 'warna', 'small_eks.warna2 = warna.kd_warna') // customer
                    ->select("warna.*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $eksterior2->createCommand();
            $models3 = $command3->queryAll();
//            $eks = array();
            foreach ($models3 as $r) {
//                $eks = $r;
                $eks['warna2'] = $r;
            }
        } else {
            // eksterior
            // ====================== WARNA ================================
            $eksterior = new Query;
            $eksterior->from('mini_eks')
                    ->join('JOIN', 'warna', 'mini_eks.warna = warna.kd_warna') // customer
                    ->select("warna.*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command2 = $eksterior->createCommand();
            $models2 = $command2->queryAll();
            $eks = array();
            foreach ($models2 as $r) {
                $eks = $r;
                $eks['warna'] = $r;
            }
            $eksterior2 = new Query;
            $eksterior2->from('mini_eks')
                    ->join('JOIN', 'warna', 'mini_eks.warna2 = warna.kd_warna') // customer
                    ->select("warna.*")
                    ->where('no_wo="' . $params['no_wo'] . '"');

            $command3 = $eksterior2->createCommand();
            $models3 = $command3->queryAll();
//            $eks = array();
            foreach ($models3 as $r) {
//                $eks = $r;
                $eks['warna2'] = $r;
            }
//======================================
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


        //=================== EKTERIOR===================
        if ($jenis == "Small Bus") {
            $table = 'small';
        } else {
            $table = 'mini';
        }
        $querydriver_seat = new Query;
        $querydriver_seat->from($table . '_eks')
                ->where("no_wo='" . $model['no_wo'] . "'")
                ->select("*");
        $commanddriver_seat = $querydriver_seat->createCommand();
        $driver_seat = $commanddriver_seat->queryAll();
//        $coba = array();
        foreach ($driver_seat as $key => $asi) {
            $eks['plat'] = $asi;
            $eks['ventilasi'] = $asi;
            $eks['spion'] = $asi;
            $eks['kdepan'] = $asi;
            $eks['kbelakang'] = $asi;
            $eks['ksamping'] = $asi;
            $eks['ldepan'] = $asi;
            $eks['lbelakang'] = $asi;
            $eks['pdepan'] = $asi;
            $eks['ppenumpang'] = $asi;
            $eks['pbagasi'] = $asi;
            $eks['pbelakang'] = $asi;
            $eks['wyper'] = $asi;
            $eks['akarat'] = $asi;
            $eks['strip'] = $asi;
            $eks['letter'] = $asi;
            $eks['lain2'] = $asi['lain2'];
        }
//        ============= INTERIOR ===================
        if ($jenis == "Small Bus") {
            $queryint = new Query;
            $queryint->from('small_int')
                    ->where("no_wo='" . $model['no_wo'] . "'")
                    ->select("*");
            $commandint = $queryint->createCommand();
            $as = $commandint->queryAll();
            foreach ($as as $key => $asa) {
                $models3['plavon'] = $asa;
                $models3['bdalam'] = $asa;
                $models3['duchting'] = $asa;
                $models3['trimming'] = $asa;
                $models3['lplavon'] = $asa;
                $models3['dashboard'] = $asa;
                $models3['lantai'] = $asa;
                $models3['karpet'] = $asa;
                $models3['peredam'] = $asa;
                $models3['pegangan_atas'] = $asa;
                $models3['pengaman_penumpang'] = $asa;
                $models3['pengaman_driver'] = $asa;
                $models3['pengaman_kaca'] = $asa;
                $models3['gordyn'] = $asa;
                $models3['driver_fan'] = $asa;
                $models3['radio_tape'] = $asa;
                $models3['spek_seat'] = $asa;
                $models3['driver_seat'] = $asa;
                $models3['optional_seat'] = $asa;
                $models3['total_seat'] = $asa;
                $models3['cover_seat'] = $asa;
                $models3['ac'] = $asa;
                $models3['lain2'] = $asa['lain2'];
            }
        } else {
            $queryint = new Query;
            $queryint->from('mini_int')
                    ->where("no_wo='" . $model['no_wo'] . "'")
                    ->select("*");
            $commandint = $queryint->createCommand();
            $as = $commandint->queryAll();
            foreach ($as as $key => $asa) {
                $models3['plavon'] = $asa;
                $models3['bdalam'] = $asa;
                $models3['duchting'] = $asa;
                $models3['trimming'] = $asa;
                $models3['lplavon'] = $asa;
                $models3['lantai'] = $asa;
                $models3['karpet'] = $asa;
                $models3['seat_satu'] = $asa;
                $models3['seat_dua'] = $asa;
                $models3['seat_tiga'] = $asa;
                $models3['seat_empat'] = $asa;
                $models3['seat_lima'] = $asa;
                $models3['total_seat'] = $asa;
                $models3['cover_seat'] = $asa;
                $models3['ac'] = $asa;
                $models3['lain2'] = $asa['lain2'];
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'det' => $asu, 'eksterior' => $eks, 'interior' => $models3), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params['no_spk']);
        $model = new Womasuk();
        $model->attributes = $params['womasuk'];
        $model->no_spk = $params['womasuk']['spk']['no_spk'];
        $model->kode = $params['womasuk']['kode'];
        $model->tgl_kontrak = date('Y-m-d', strtotime($params['womasuk']['tgl_kontrak']));


        if ($model->save()) {

            // EKTERIOR
            if (!empty($params['eksterior'])) {
                if ($params['womasuk']['jenis'] == "Small Bus") {
                    $table = new Smalleks();
                } else {
                    $table = new Minieks();
                }
                $eks = $table;
                $eks->no_wo = $params['womasuk']['no_wo'];
                if (!empty($params['eksterior']['plat']['plat_body'])) {
                    $eks->plat_body = $params['eksterior']['plat']['plat_body'];
                }
                if (!empty($params['eksterior']['spion']['kaca_spion'])) {
                    $eks->kaca_spion = $params['eksterior']['spion']['kaca_spion'];
                }
                if (!empty($params['eksterior']['kdepan']['kaca_depan'])) {
                    $eks->kaca_depan = $params['eksterior']['kdepan']['kaca_depan'];
                }
                if (!empty($params['eksterior']['kbelakang']['kaca_belakang'])) {
                    $eks->kaca_belakang = $params['eksterior']['kbelakang']['kaca_belakang'];
                }
                if (!empty($params['eksterior']['ksamping']['kaca_samping'])) {
                    $eks->kaca_samping = $params['eksterior']['ksamping']['kaca_samping'];
                }
                if (!empty($params['eksterior']['ldepan']['lampu_depan'])) {
                    $eks->lampu_depan = $params['eksterior']['ldepan']['lampu_depan'];
                }
                if (!empty($params['eksterior']['lbelakang']['lampu_belakang'])) {
                    $eks->lampu_belakang = $params['eksterior']['lbelakang']['lampu_belakang'];
                }
                if (!empty($params['eksterior']['pdepan']['pintu_depan'])) {
                    $eks->pintu_depan = $params['eksterior']['pdepan']['pintu_depan'];
                }
                if (!empty($params['eksterior']['ppenumpang']['pintu_penumpang'])) {
                    $eks->pintu_penumpang = $params['eksterior']['ppenumpang']['pintu_penumpang'];
                }
                if (!empty($params['eksterior']['pbagasi']['pintu_bagasi_samping'])) {
                    $eks->pintu_bagasi_samping = $params['eksterior']['pbagasi']['pintu_bagasi_samping'];
                }
                if (!empty($params['eksterior']['pbelakang']['pintu_belakang'])) {
                    $eks->pintu_belakang = $params['eksterior']['pbelakang']['pintu_belakang'];
                }
                if (!empty($params['eksterior']['wyper']['wyper_set'])) {
                    $eks->wyper_set = $params['eksterior']['wyper']['wyper_set'];
                }
                if (!empty($params['eksterior']['warna']['kd_warna'])) {
//                    $warna = Warna::findOne($params['eksterior']['warna']['kd_warna']);
//                    if (empty($warna)) {
//                        $warna = new Warna();
//                    }
//                    $warna->attributes = $params;
//                    if ($warna->save()) {
                    $eks->warna = $params['eksterior']['warna']['kd_warna'];
//                    }
                }
                if (!empty($params['eksterior']['warna2']['kd_warna'])) {
                    //warna 2
//                    $warna = Warna::findOne($params['eksterior']['warna2']['kd_warna']);
//                    if (empty($warna)) {
//                        $warna = new Warna();
//                    }
//                    $warna->attributes = $params;
//                    if ($warna->save()) {
                    $eks->warna2 = $params['eksterior']['warna2']['kd_warna'];
//                    }
                }
                if (!empty($params['eksterior']['strip']['strip'])) {
                    $eks->strip = $params['eksterior']['strip']['strip'];
                }
                if (!empty($params['eksterior']['letter']['letter'])) {
                    $eks->letter = $params['eksterior']['letter']['letter'];
                }
                if (!empty($params['eksterior']['lain2'])) {
                    $eks->lain2 = $params['eksterior']['lain2'];
                }
                $eks->save();
            }

            // INTERIOR
            if (!empty($params['interior'])) {
                if ($params['womasuk']['jenis'] == "Mini Bus") {
                    $int = new Miniint();
                    $int->no_wo = $params['womasuk']['no_wo'];
                    if (!empty($params['interior']['plavon']['plavon'])) {
                        $int->plavon = $params['interior']['plavon']['plavon'];
                    }
                    if (!empty($params['interior']['trimming']['trimming_deck'])) {
                        $int->trimming_deck = $params['interior']['trimming']['trimming_deck'];
                    }
                    if (!empty($params['interior']['duchting']['duchting_louver'])) {
                        $int->duchting_louver = $params['interior']['duchting']['duchting_louver'];
                    }
                    if (!empty($params['interior']['lplavon']['lampu_plavon'])) {
                        $int->lampu_plavon = $params['interior']['lplavon']['lampu_plavon'];
                    }
                    if (!empty($params['interior']['lantai']['lantai'])) {
                        $int->lantai = $params['interior']['lantai']['lantai'];
                    }
                    if (!empty($params['interior']['karpet']['karpet'])) {
                        $int->karpet = $params['interior']['karpet']['karpet'];
                    }
                    if (!empty($params['interior']['seat_satu']['konf_seat1'])) {
                        $int->konf_seat1 = $params['interior']['seat_satu']['konf_seat1'];
                    }
                    if (!empty($params['interior']['seat_dua']['konf_seat2'])) {
                        $int->konf_seat2 = $params['interior']['seat_dua']['konf_seat2'];
                    }
                    if (!empty($params['interior']['seat_tiga']['konf_seat3'])) {
                        $int->konf_seat3 = $params['interior']['seat_tiga']['konf_seat3'];
                    }
                    if (!empty($params['interior']['seat_empat']['konf_seat4'])) {
                        $int->konf_seat4 = $params['interior']['seat_empat']['konf_seat4'];
                    }
                    if (!empty($params['interior']['seat_lima']['konf_seat5'])) {
                        $int->konf_seat5 = $params['interior']['seat_lima']['konf_seat5'];
                    }
                    if (!empty($params['interior']['seat_lima']['konf_seat5'])) {
                        $int->konf_seat5 = $params['interior']['seat_lima']['konf_seat5'];
                    }
                    if (!empty($params['interior']['cover_seat']['cover_seat'])) {
                        $int->cover_seat = $params['interior']['cover_seat']['cover_seat'];
                    }
                    if (!empty($params['interior']['total_seat']['total_seat'])) {
                        $int->total_seat = $params['interior']['total_seat']['total_seat'];
                    }
                    if (!empty($params['interior']['ac']['merk_ac'])) {
                        $int->merk_ac = $params['interior']['ac']['merk_ac'];
                    }
                    if (!empty($params['interior']['lain2'])) {
                        $int->lain2 = $params['interior']['lain2'];
                    }
                    $int->save();
                } else {
                    $int = new Smallint();
                    $int->no_wo = $params['womasuk']['no_wo'];

                    if (!empty($params['interior']['plavon']['plavon'])) {
                        $int->plavon = $params['interior']['plavon']['plavon'];
                    }
                    if (!empty($params['interior']['trimming']['trimming_deck'])) {
                        $int->trimming_deck = $params['interior']['trimming']['trimming_deck'];
                    }
                    if (!empty($params['interior']['duchting']['duchting_louver'])) {
                        $int->duchting_louver = $params['interior']['duchting']['duchting_louver'];
                    }
                    if (!empty($params['interior']['lplavon']['lampu_plavon'])) {
                        $int->lampu_plavon = $params['interior']['lplavon']['lampu_plavon'];
                    }
                    if (!empty($params['interior']['lantai']['lantai'])) {
                        $int->lantai = $params['interior']['lantai']['lantai'];
                    }
                    if (!empty($params['interior']['karpet']['karpet'])) {
                        $int->karpet = $params['interior']['karpet']['karpet'];
                    }
                    if (!empty($params['interior']['bdalam']['bagasi_dalam'])) {
                        $int->bagasi_dalam = $params['interior']['bdalam']['bagasi_dalam'];
                    }
                    if (!empty($params['interior']['dashboard']['dashboard'])) {
                        $int->dashboard = $params['interior']['dashboard']['dashboard'];
                    }
                    if (!empty($params['interior']['peredam']['peredam'])) {
                        $int->peredam = $params['interior']['peredam']['peredam'];
                    }
                    if (!empty($params['interior']['pegangan_atas']['pegangan_tangan_atas'])) {
                        $int->pegangan_tangan_atas = $params['interior']['pegangan_atas']['pegangan_tangan_atas'];
                    }
                    if (!empty($params['interior']['pengaman_penumpang']['pengaman_penumpang'])) {
                        $int->pengaman_penumpang = $params['interior']['pengaman_penumpang']['pengaman_penumpang'];
                    }
                    if (!empty($params['interior']['pengaman_kaca']['pengaman_kaca_samping'])) {
                        $int->pengaman_kaca_samping = $params['interior']['pengaman_kaca']['pengaman_kaca_samping'];
                    }
                    if (!empty($params['interior']['pengaman_driver']['pengaman_driver'])) {
                        $int->pengaman_driver = $params['interior']['pengaman_driver']['pengaman_driver'];
                    }
                    if (!empty($params['interior']['gordyn']['gordyn'])) {
                        $int->gordyn = $params['interior']['gordyn']['gordyn'];
                    }
                    if (!empty($params['interior']['driver_fan']['driver_fan'])) {
                        $int->driver_fan = $params['interior']['driver_fan']['driver_fan'];
                    }
                    if (!empty($params['interior']['radio_tape']['radio_tape'])) {
                        $int->radio_tape = $params['interior']['radio_tape']['radio_tape'];
                    }
                    if (!empty($params['interior']['spek_seat']['spek_seat'])) {
                        $int->spek_seat = $params['interior']['spek_seat']['spek_seat'];
                    }
                    if (!empty($params['interior']['driver_seat']['driver_seat'])) {
                        $int->driver_seat = $params['interior']['driver_seat']['driver_seat'];
                    }
                    if (!empty($params['interior']['cover_seat']['cover_seat'])) {
                        $int->cover_seat = $params['interior']['cover_seat']['cover_seat'];
                    }
                    if (!empty($params['interior']['optional_seat']['optional_seat'])) {
                        $int->optional_seat = $params['interior']['optional_seat']['optional_seat'];
                    }
                    if (!empty($params['interior']['total_seat']['total_seat'])) {
                        $int->total_seat = $params['interior']['total_seat']['total_seat'];
                    }
                    if (!empty($params['interior']['ac']['merk_ac'])) {
                        $int->merk_ac = $params['interior']['ac']['merk_ac'];
                    }
                    if (!empty($params['interior']['lain2'])) {
                        $int->lain2 = $params['interior']['lain2'];
                    }
                    $int->save();
                }
            }

            // UPDATE STI CUSTOMER
            $kdtitipan = (!empty($params['womasuk']['titipan']['kd_titipan'])) ? $params['womasuk']['titipan']['kd_titipan'] : $params['kd_titipan'];
//            if (!empty($params['kd_titipan'])) {
                $cus = '';
                $query = new Query;
                $query->from('spk')
                        ->where("no_spk='" . $model->no_spk . "'")
                        ->select("*");
                $command = $query->createCommand();
                $cus = $command->query()->read();
//            }
            $sti = \app\models\Serahterimain::find()->where('kd_titipan="' . $kdtitipan . '"')->one();
            $sti->kd_cust = (!empty($params['womasuk']['spk']['kd_customer'])) ? $params['womasuk']['spk']['kd_customer'] : $cus['kd_customer'];
            $sti->no_spk = (!empty($params['womasuk']['spk']['no_spk'])) ? $params['womasuk']['spk']['no_spk'] : $cus['no_spk'];
            $sti->save();


            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($params['womasuk']['no_wo']);
        $model->attributes = $params['womasuk'];
        $model->kode = $params['womasuk']['kode'];


        if ($model->save()) {
// EKTERIOR
            if ($params['womasuk']['jenis'] == "Small Bus") {
                $eks = Smalleks::find()->where('no_wo="' . $params['womasuk']['no_wo'] . '"')->one();
                if (empty($eks)) {
                    $eks = new Smalleks();
                    $eks->no_wo = $params['womasuk']['no_wo'];
                }
            } else {
                $eks = Minieks::find()->where('no_wo="' . $params['womasuk']['no_wo'] . '"')->one();
                if (empty($eks)) {
                    $eks = new Minieks();
                    $eks->no_wo = $params['womasuk']['no_wo'];
                }
            }
//            $eks = $table;

            if (!empty($params['eksterior']['plat']['plat_body'])) {
                $eks->plat_body = $params['eksterior']['plat']['plat_body'];
            }
            if (isset($params['eksterior']['ventilasi']['ventilasi_atas'])) {
                $eks->ventilasi_atas = $params['eksterior']['ventilasi']['ventilasi_atas'];
            }
            if (isset($params['eksterior']['spion']['kaca_spion'])) {
                $eks->kaca_spion = $params['eksterior']['spion']['kaca_spion'];
            }
            if (isset($params['eksterior']['kdepan']['kaca_depan'])) {
                $eks->kaca_depan = $params['eksterior']['kdepan']['kaca_depan'];
            }
            if (isset($params['eksterior']['kbelakang']['kaca_belakang'])) {
                $eks->kaca_belakang = $params['eksterior']['kbelakang']['kaca_belakang'];
            }
            if (isset($params['eksterior']['ksamping']['kaca_samping'])) {
                $eks->kaca_samping = $params['eksterior']['ksamping']['kaca_samping'];
            }
            if (isset($params['eksterior']['ldepan']['lampu_depan'])) {
                $eks->lampu_depan = $params['eksterior']['ldepan']['lampu_depan'];
            }
            if (isset($params['eksterior']['lbelakang']['lampu_belakang'])) {
                $eks->lampu_belakang = $params['eksterior']['lbelakang']['lampu_belakang'];
            }
            if (isset($params['eksterior']['pdepan']['pintu_depan'])) {
                $eks->pintu_depan = $params['eksterior']['pdepan']['pintu_depan'];
            }
            if (isset($params['eksterior']['ppenumpang']['pintu_penumpang'])) {
                $eks->pintu_penumpang = $params['eksterior']['ppenumpang']['pintu_penumpang'];
            }
            if (isset($params['eksterior']['pbagasi']['pintu_bagasi_samping'])) {
                $eks->pintu_bagasi_samping = $params['eksterior']['pbagasi']['pintu_bagasi_samping'];
            }
            if (isset($params['eksterior']['pbelakang']['pintu_belakang'])) {
                $eks->pintu_belakang = $params['eksterior']['pbelakang']['pintu_belakang'];
            }
            if (isset($params['eksterior']['wyper']['wyper_set'])) {
                $eks->wyper_set = $params['eksterior']['wyper']['wyper_set'];
            }

            if (!empty($params['eksterior']['warna']['kd_warna'])) {
//                    $warna = Warna::findOne($params['eksterior']['warna']['kd_warna']);
//                    if (empty($warna)) {
//                        $warna = new Warna();
//                    }
//                    $warna->attributes = $params;
//                    if ($warna->save()) {
                $eks->warna = $params['eksterior']['warna']['kd_warna'];
//                    }
            }
            if (!empty($params['eksterior']['warna2']['kd_warna'])) {
                //warna 2
//                    $warna = Warna::findOne($params['eksterior']['warna2']['kd_warna']);
//                    if (empty($warna)) {
//                        $warna = new Warna();
//                    }
//                    $warna->attributes = $params;
//                    if ($warna->save()) {
                $eks->warna2 = $params['eksterior']['warna2']['kd_warna'];
//                    }
            }
            if (isset($params['eksterior']['strip']['strip'])) {
                $eks->strip = $params['eksterior']['strip']['strip'];
            }
            if (isset($params['eksterior']['letter']['letter'])) {
                $eks->letter = $params['eksterior']['letter']['letter'];
            }
            if (isset($params['eksterior']['lain2'])) {
                $eks->lain2 = $params['eksterior']['lain2'];
            }
            $eks->save();

// INTERIOR
            if ($params['womasuk']['jenis'] == "Mini Bus") {
                $int = Miniint::find()->where('no_wo="' . $params['womasuk']['no_wo'] . '"')->one();
                if (empty($int)) {
                    $int = new Miniint();
                    $int->no_wo = $params['womasuk']['no_wo'];
                }

                if (!empty($params['interior']['plavon']['plavon'])) {
                    $int->plavon = $params['interior']['plavon']['plavon'];
                }
                if (!empty($params['interior']['trimming']['trimming_deck'])) {
                    $int->trimming_deck = $params['interior']['trimming']['trimming_deck'];
                }
                if (!empty($params['interior']['duchting']['duchting_louver'])) {
                    $int->duchting_louver = $params['interior']['duchting']['duchting_louver'];
                }
                if (!empty($params['interior']['lplavon']['lampu_plavon'])) {
                    $int->lampu_plavon = $params['interior']['lplavon']['lampu_plavon'];
                }
                if (!empty($params['interior']['lantai']['lantai'])) {
                    $int->lantai = $params['interior']['lantai']['lantai'];
                }
                if (!empty($params['interior']['karpet']['karpet'])) {
                    $int->karpet = $params['interior']['karpet']['karpet'];
                }
                if (!empty($params['interior']['seat_satu']['konf_seat1'])) {
                    $int->konf_seat1 = $params['interior']['seat_satu']['konf_seat1'];
                }
                if (!empty($params['interior']['seat_dua']['konf_seat2'])) {
                    $int->konf_seat2 = $params['interior']['seat_dua']['konf_seat2'];
                }
                if (!empty($params['interior']['seat_tiga']['konf_seat3'])) {
                    $int->konf_seat3 = $params['interior']['seat_tiga']['konf_seat3'];
                }
                if (!empty($params['interior']['seat_empat']['konf_seat4'])) {
                    $int->konf_seat4 = $params['interior']['seat_empat']['konf_seat4'];
                }
                if (!empty($params['interior']['seat_lima']['konf_seat5'])) {
                    $int->konf_seat5 = $params['interior']['seat_lima']['konf_seat5'];
                }
                if (!empty($params['interior']['seat_lima']['konf_seat5'])) {
                    $int->konf_seat5 = $params['interior']['seat_lima']['konf_seat5'];
                }
                if (!empty($params['interior']['cover_seat']['cover_seat'])) {
                    $int->cover_seat = $params['interior']['cover_seat']['cover_seat'];
                }
                if (!empty($params['interior']['total_seat']['total_seat'])) {
                    $int->total_seat = $params['interior']['total_seat']['total_seat'];
                }
                if (!empty($params['interior']['ac']['merk_ac'])) {
                    $int->merk_ac = $params['interior']['ac']['merk_ac'];
                }
                if (!empty($params['interior']['lain2'])) {
                    $int->lain2 = $params['interior']['lain2'];
                }
                $int->save();
            } else {
                $int = Smallint::find()->where('no_wo="' . $params['womasuk']['no_wo'] . '"')->one();
                if (empty($int)) {
                    $int = new Smallint();
                    $int->no_wo = $params['womasuk']['no_wo'];
                }
                if (!empty($params['interior']['plavon']['plavon'])) {
                    $int->plavon = $params['interior']['plavon']['plavon'];
                }
                if (!empty($params['interior']['trimming']['trimming_deck'])) {
                    $int->trimming_deck = $params['interior']['trimming']['trimming_deck'];
                }
                if (!empty($params['interior']['duchting']['duchting_louver'])) {
                    $int->duchting_louver = $params['interior']['duchting']['duchting_louver'];
                }
                if (!empty($params['interior']['lplavon']['lampu_plavon'])) {
                    $int->lampu_plavon = $params['interior']['lplavon']['lampu_plavon'];
                }
                if (!empty($params['interior']['lantai']['lantai'])) {
                    $int->lantai = $params['interior']['lantai']['lantai'];
                }
                if (!empty($params['interior']['karpet']['karpet'])) {
                    $int->karpet = $params['interior']['karpet']['karpet'];
                }
                if (!empty($params['interior']['bdalam']['bagasi_dalam'])) {
                    $int->bagasi_dalam = $params['interior']['bdalam']['bagasi_dalam'];
                }
                if (!empty($params['interior']['dashboard']['dashboard'])) {
                    $int->dashboard = $params['interior']['dashboard']['dashboard'];
                }
                if (!empty($params['interior']['peredam']['peredam'])) {
                    $int->peredam = $params['interior']['peredam']['peredam'];
                }
                if (!empty($params['interior']['pegangan_atas']['pegangan_tangan_atas'])) {
                    $int->pegangan_tangan_atas = $params['interior']['pegangan_atas']['pegangan_tangan_atas'];
                }
                if (!empty($params['interior']['pengaman_penumpang']['pengaman_penumpang'])) {
                    $int->pengaman_penumpang = $params['interior']['pengaman_penumpang']['pengaman_penumpang'];
                }
                if (!empty($params['interior']['pengaman_kaca']['pengaman_kaca_samping'])) {
                    $int->pengaman_kaca_samping = $params['interior']['pengaman_kaca']['pengaman_kaca_samping'];
                }
                if (!empty($params['interior']['pengaman_driver']['pengaman_driver'])) {
                    $int->pengaman_driver = $params['interior']['pengaman_driver']['pengaman_driver'];
                }
                if (!empty($params['interior']['gordyn']['gordyn'])) {
                    $int->gordyn = $params['interior']['gordyn']['gordyn'];
                }
                if (!empty($params['interior']['driver_fan']['driver_fan'])) {
                    $int->driver_fan = $params['interior']['driver_fan']['driver_fan'];
                }
                if (!empty($params['interior']['radio_tape']['radio_tape'])) {
                    $int->radio_tape = $params['interior']['radio_tape']['radio_tape'];
                }
                if (!empty($params['interior']['spek_seat']['spek_seat'])) {
                    $int->spek_seat = $params['interior']['spek_seat']['spek_seat'];
                }
                if (!empty($params['interior']['driver_seat']['driver_seat'])) {
                    $int->driver_seat = $params['interior']['driver_seat']['driver_seat'];
                }
                if (!empty($params['interior']['cover_seat']['cover_seat'])) {
                    $int->cover_seat = $params['interior']['cover_seat']['cover_seat'];
                }
                if (!empty($params['interior']['optional_seat']['optional_seat'])) {
                    $int->optional_seat = $params['interior']['optional_seat']['optional_seat'];
                }
                if (!empty($params['interior']['total_seat']['total_seat'])) {
                    $int->total_seat = $params['interior']['total_seat']['total_seat'];
                }
                if (!empty($params['interior']['ac']['merk_ac'])) {
                    $int->merk_ac = $params['interior']['ac']['merk_ac'];
                }
                if (!empty($params['interior']['lain2'])) {
                    $int->lain2 = $params['interior']['lain2'];
                }
                $int->save();
            }

// UPDATE STI CUSTOMER
//            $spk = \app\models\Spk::findOne('no_spk='.$params['womasuk']['spk']['no_spk']);
            $spk = \app\models\Spkaroseri::find()->where('no_spk="' . $params['womasuk']['spk']['no_spk'] . '"')->one();
            $sti = \app\models\Serahterimain::find()->where('kd_titipan="' . $params['womasuk']['titipan']['kd_titipan'] . '"')->one();
            $sti->kd_cust = $spk->kd_customer;
            $sti->no_spk = $spk->no_spk;
            $sti->save();


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
        foreach ($models as $key => $val) {
            if (strtolower($val['jenis']) == "mini bus") {
                $miniExt = Minieks::find()->where('no_wo="' . $val['no_wo'] . '"')->one();
                $models[$key]['kd_warna'] = (!empty($miniExt->waarna->kd_warna)) ? $miniExt->waarna->kd_warna : '';
                $models[$key]['warna'] = (!empty($miniExt->waarna->warna)) ? $miniExt->waarna->warna : '';
            } elseif (strtolower($val['jenis']) == "small bus") {
                $smallExt = Smalleks::find()->where('no_wo="' . $val['no_wo'] . '"')->one();
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
    public function actionSqlprint() {
        $params = $_REQUEST;
        $query = new Query;
        $query->select("*")
                ->from('view_wo_spk')
                ->where('no_wo = "' . $params['kd'] . '"');

        session_start();
        $_SESSION['queryprint'] = $query;
        $_SESSION['no_wo'] = $params['kd'];
    }
    public function actionPrint() {
        session_start();
        $query = $_SESSION['queryprint'];
        $nowo = $_SESSION['no_wo'];
        $command = $query->createCommand();
        $models = $command->query()->read();
       
        return $this->render("/expretur/print_womasuk", ['models' => $models, 'id' => $nowo]);
    }

}

?>
