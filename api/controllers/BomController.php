<?php

namespace app\controllers;

use Yii;
use app\models\Bom;
use app\models\BomDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BomController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'rekap' => ['get'],
                    'rekaprealisasiwo' => ['post'],
                    'rekaprealisasimodel' => ['post'],
                    'excel' => ['get'],
                    'excelrealisasiwo' => ['get'],
                    'excelrealisasimodel' => ['get'],
                    'exceltrans' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'chassis' => ['get'],
                    'model' => ['get'],
                    'barang' => ['get'],
                    'kode' => ['get'],
                    'tipe' => ['get'],
                    'cariall' => ['get'],
                    'detail' => ['get'],
                    'cari' => ['get'],
                    'womodel' => ['get'],
                ],
            ]
        ];
    }

    public function actionWomodel() {
        $params = $_REQUEST;
        $kd_chassis = isset($params['kd_chassis']) ? $params['kd_chassis'] : '';
        $kd_model = isset($params['model']) ? $params['model'] : '';
        $query = new Query;
        $query->from('view_wo_spk')
                ->select("no_wo")
                ->where('kd_chassis="' . $kd_chassis . '" and kd_model = "' . $kd_model . '"');

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCariall() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_standar_bahan')
                ->select("*")
                ->where(['like', 'kd_bom', $params['nama']])
                ->orderBy('kd_bom DESC')
                ->limit(25);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_standar_bahan')
                ->join('JOIN', 'chassis', 'trans_standar_bahan.kd_chassis = chassis.kd_chassis')
                ->select("trans_standar_bahan.*, chassis.merk")
                ->where(['like', 'kd_bom', $params['nama']])
                ->andWhere('status = 1')
                ->orderBy('kd_bom DESC')
                ->limit(25);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
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

    public function actionChassis() {
        $query = new Query;
        $query->from('chassis')
                ->select("kd_chassis , jenis")
                ->where('merk="' . $_GET['merk'] . '" and tipe="' . $_GET['tipe'] . '"');

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode = $models['kd_chassis'];
        $jenis = $models['jenis'];

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode, 'jenis' => $jenis));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_standar_bahan')
                ->select('*')
                ->orderBy('kd_bom DESC')
                ->where('year(tgl_buat) = "' . date("Y") . '"')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();

        if (empty($models)) {
            $kode = 'BOM' . date("y") . '0001';
        } else {
            $lastKode = substr($models['kd_bom'], -4) + 1;
            $kode = substr('0000' . $lastKode, strlen($lastKode));
            $kode = 'BOM' . date("y") . substr('0000' . $lastKode, -4);
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
//init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_standar_bahan.kd_bom DESC";
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
                ->from(['trans_standar_bahan', 'chassis', 'model'])
                ->where('trans_standar_bahan.kd_chassis = chassis.kd_chassis and trans_standar_bahan.kd_model=model.kd_model')
                ->orderBy($sort)
                ->select("*");

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
//        if (isset($params['filter'])) {
        $no_wo = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "tgl_terima DESC";
        $offset = 0;
        $limit = 10;

        //limit & offset pagination
        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('view_wo_spk')
                ->select('*');

        //cek optional BOM
//            $optional = \app\models\TransAdditionalBom::findAll(['no_wo' => $no_wo['no_wo']]);
//
//            //jika tidak ada optional ambil dari trans_standar_bahan
//            if (empty($optional) or count($optional) == 0) {
        //create query
//        $query = new Query;
//        $query->offset($offset)
//                ->limit($limit)
//                ->from('det_standar_bahan as dts')
//                ->join('JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
//                ->join('JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
//                ->join('JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
//                ->join('JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
//                ->join('JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
//                ->orderBy('tjb.urutan_produksi ASC, brg.nm_barang ASC')
//                ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
//            } else {
//                //create query
//                $query = new Query;
//                $query->offset($offset)
//                        ->limit($limit)
//                        ->from('det_additional_bom as dts')
//                        ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
//                        ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
//                        ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dts.tran_additional_bom_id')
//                        ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_wo  = tsb.no_wo')
//                        ->orderBy('tjb.urutan_produksi ASC, brg.nm_barang ASC')
//                        ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
//            }
        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            if (isset($filter['kategori']) and isset($filter['kata_kunci'])) {
                $query->where(['like', $filter['kategori'], $filter['kata_kunci']]);
            } else if (isset($filter['tanggal'])) {
                $value = explode(' - ', $filter['tanggal']);
                $start = date("Y-m-d", strtotime($value[0]));
                $end = date("Y-m-d", strtotime($value[1]));
                $query->andFilterWhere(['between', 'tgl_terima', $start, $end]);
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
//        }
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $query->limit(null);
        $query->offset(null);
        $command = $query->createCommand();
        $models = $command->queryAll();

        $data = array();
        $i = 0;
        foreach ($models as $val) {
            //cek optional BOM
//            $optional = \app\models\TransAdditionalBom::findAll(['no_wo' => $val['no_wo']]);
            $optional = \app\models\TransAdditionalBomWo::find()->where(['no_wo' => $val['no_wo']])->all();

            //jika tidak ada optional ambil dari trans_standar_bahan
            if (empty($optional) or count($optional) == 0) {
                //create query
                $query = new Query;
                $query->from('det_standar_bahan as dts')
                        ->join('JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                        ->join('JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                        ->join('JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
                        ->join('JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
                        ->join('JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
                        ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                        ->where('wm.no_wo = "' . $val['no_wo'] . '"')
                        ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
            } else {
                //create query
                $query = new Query;
                $query->from('det_additional_bom as dts')
                        ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                        ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                        ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dts.tran_additional_bom_id')
                        ->join('LEFT JOIN', 'trans_additional_bom_wo as tsbw', ' tsb.id = tsbw.tran_additional_bom_id')
//                        ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_wo  = tsbw.no_wo')
                        ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                        ->where('tsbw.no_wo = "' . $val['no_wo'] . '"')
                        ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, tsbw.no_wo");
            }

            $command = $query->createCommand();
            $models = $command->queryAll();

            foreach ($models as $valDet) {
                $data[$i]['no_wo'] = $valDet['no_wo'];
                $data[$i]['id_jabatan'] = $valDet['id_jabatan'];
                $data[$i]['jabatan'] = $valDet['jabatan'];
                $data[$i]['kd_barang'] = $valDet['kd_barang'];
                $data[$i]['nm_barang'] = $valDet['nm_barang'];
                $data[$i]['satuan'] = $valDet['satuan'];
                $data[$i]['qty'] = $valDet['qty'];
                $data[$i]['harga'] = $valDet['harga'];
                $data[$i]['ket'] = $valDet['ket'];
                $i++;
            }
        }
        return $this->render("/expretur/rekapbom", ['models' => $data, 'filter' => $filter]);
    }

    public function actionRekaprealisasiwo() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);

        $no_wo = $params['no_wo']['no_wo'];

        //cek optional BOM
        $optional = \app\models\TransAdditionalBomWo::find()->where('no_wo = "' . $no_wo . '"')->all();

        //jika tidak ada optional ambil dari trans_standar_bahan
        if (empty($optional) or count($optional) == 0) {
            //create query
            $query = new Query;
            $query->from('det_standar_bahan as dts')
                    ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                    ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                    ->join('LEFT JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
                    ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
                    ->join('LEFT JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
                    ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                    ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
        } else {
            //create query
            $query = new Query;
            $query->from('det_additional_bom as dts')
                    ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                    ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                    ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dts.tran_additional_bom_id')
                    ->join('LEFT JOIN', 'trans_additional_bom_wo as wm', ' tsb.id = wm.tran_additional_bom_id')
                    ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                    ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
        }

        $queryBbk = new Query;
        $queryBbk->from('trans_bbk as tb')
                ->join('LEFT JOIN', 'det_bbk as db', 'tb.no_bbk = db.no_bbk')
                ->select('db.kd_barang, db.jml, db.ket, tb.kd_jab as id_jabatan');

        $query->where(['=', 'wm.no_wo', $no_wo]);
        $queryBbk->where(['=', 'tb.no_wo', $no_wo]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $commandBbk = $queryBbk->createCommand();
        $modelsBbk = $commandBbk->queryAll();

        $detBbk = array();
        foreach ($modelsBbk as $valBbk) {
            $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] = isset($detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
            if ($valBbk['ket'] != "-") {
                $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['ket'][] = $valBbk['ket'];
            }
        }

        $detBom = array();
        foreach ($models as $val) {
            $jKeluar = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]['jml']) ? $detBbk[$val['id_jabatan']][$val['kd_barang']]['jml'] : 0;
            $ket = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) ? join(',', $detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) : '-';

            $detBom[$val['kd_barang']]['no_wo'] = $val['no_wo'];
            $detBom[$val['kd_barang']]['kd_barang'] = $val['kd_barang'];
            $detBom[$val['kd_barang']]['nm_barang'] = $val['nm_barang'];
            $detBom[$val['kd_barang']]['satuan'] = $val['satuan'];
            $detBom[$val['kd_barang']]['harga'] = $val['harga'];
            $detBom[$val['kd_barang']]['qty'] = $val['qty'];
            $detBom[$val['kd_barang']]['jml_keluar'] = $jKeluar;
            $detBom[$val['kd_barang']]['ket'] = $ket;
            $detBom[$val['kd_barang']]['jabatan'] = $val['jabatan'];
        }

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['bbk'] = $queryBbk;
        $_SESSION['filter'] = $no_wo;


        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $detBom, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcelrealisasiwo() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $query->limit(null);
        $query->offset(null);
        $command = $query->createCommand();
        $models = $command->queryAll();

        $bbk = $_SESSION['bbk'];
        $bbk->limit(null);
        $bbk->offset(null);
        $commandbbk = $bbk->createCommand();
        $modelbbk = $commandbbk->queryAll();

        return $this->render("/expretur/r_bomwo", ['models' => $models, 'filter' => $filter, 'modelbbk' => $modelbbk]);
    }

    public function actionExcelrealisasimodel() {
        session_start();
        $filter = $_SESSION['filter'];
        $i = 0;
        $jWo = 1;
        $detBom = array();
        $no_wo = array();
        foreach ($filter['no_wo'] as $val) {
            $noWo = $val['no_wo'];
            $sort = "dts.kd_bom DESC";

            if ($jWo <= 5) {

                //cek optional BOM
//                $optional = \app\models\TransAdditionalBom::find()->where('no_wo = "' . $noWo . '"')->all();
                $optional = \app\models\TransAdditionalBomWo::find()->where(['no_wo' => $noWo])->all();

                //jika tidak ada optional ambil dari trans_standar_bahan
                if (empty($optional) or count($optional) == 0) {
                    $optional = false;
                    //create query
                    $query = new Query;
                    $query->from('det_standar_bahan as dts')
                            ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                            ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                            ->join('LEFT JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
                            ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
                            ->join('LEFT JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
                            ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                            ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
                } else {
                    $optional = true;
                    //create query
                    $query = new Query;
                    $query->from('det_additional_bom as dts')
                            ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                            ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                            ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dts.tran_additional_bom_id')
                            ->join('LEFT JOIN', 'trans_additional_bom_wo as wm', ' tsb.id = wm.tran_additional_bom_id')
//                            ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_wo  = tsbw.no_wo')
                            ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                            ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
                }

                $queryBbk = new Query;
                $queryBbk->from('trans_bbk as tb')
                        ->join('JOIN', 'det_bbk as db', 'tb.no_bbk = db.no_bbk')
                        ->select('db.kd_barang, db.jml, tb.kd_jab as id_jabatan,  db.ket');

                $query->where('wm.no_wo = "' . $noWo . '"');
                $queryBbk->where('tb.no_wo = "' . $noWo . '"');

                $command = $query->createCommand();
                $models = $command->queryAll();

                $commandBbk = $queryBbk->createCommand();
                $modelsBbk = $commandBbk->queryAll();

                $detBbk = array();
                foreach ($modelsBbk as $valBbk) {
                    $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] = isset($detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
                    if ($valBbk['ket'] != "-") {
                        $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['ket'][] = $valBbk['ket'];
                    }
                }

                foreach ($models as $val) {
                    $jKeluar = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]) ? $detBbk[$val['id_jabatan']][$val['kd_barang']]['jml'] : 0;
                    $ket = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) ? join(',', $detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) : '-';

                    $detBom[$val['id_jabatan']]['title'] = $val['jabatan'];
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['kd_barang'] = $val['kd_barang'];
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['nm_barang'] = $val['nm_barang'];
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['satuan'] = $val['satuan'];
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['harga'] = $val['harga'];
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['qty'] = $val['qty'];
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['jml_keluar'][$val['no_wo']] = $jKeluar;
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['ket'] = $ket;
                    $detBom[$val['id_jabatan']]['body'][$val['kd_barang'] . $val['qty']]['jabatan'] = $val['jabatan'];
                }

                $jWo++;
                $no_wo[] = $val['no_wo'];
            }
        }

        return $this->render("/expretur/r_bommodel", ['data' => $detBom, 'no_wo' => $no_wo, 'filter' => $filter]);
    }

    public function actionRekaprealisasimodel() {
        //init variable
        session_start();
        $params = json_decode(file_get_contents("php://input"), true);
        $i = 0;
        $jWo = 1;
        $detBom = array();
        foreach ($params['no_wo'] as $val) {
            $noWo = $val['no_wo'];
            $sort = "dts.kd_bom DESC";

            if ($jWo <= 5) {

                //cek optional BOM
//                $optional = \app\models\TransAdditionalBom::find()->where('no_wo = "' . $noWo . '"')->all();
                $optional = \app\models\TransAdditionalBomWo::find()->where(['no_wo' => $noWo])->all();

                //jika tidak ada optional ambil dari trans_standar_bahan
                if (empty($optional) or count($optional) == 0) {
                    //create query
                    $query = new Query;
                    $query->from('det_standar_bahan as dts')
                            ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                            ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                            ->join('LEFT JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
                            ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
                            ->join('LEFT JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
                            ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                            ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
                } else {
                    //create query
                    $query = new Query;
                    $query->from('det_additional_bom as dts')
                            ->join('LEFT JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                            ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                            ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dts.tran_additional_bom_id')
                            ->join('LEFT JOIN', 'trans_additional_bom_wo as wm', ' tsb.id = wm.tran_additional_bom_id')
//                            ->join('LEFT JOIN', 'wo_masuk as wm', 'wm.no_wo  = tsbw.no_wo')
                            ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
                            ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
                }

                $queryBbk = new Query;
                $queryBbk->from('trans_bbk as tb')
                        ->join('JOIN', 'det_bbk as db', 'tb.no_bbk = db.no_bbk')
                        ->select('db.kd_barang, db.jml, tb.kd_jab as id_jabatan, db.ket');

                $query->where('wm.no_wo = "' . $noWo . '"');
                $queryBbk->where('tb.no_wo = "' . $noWo . '"');

                $command = $query->createCommand();
                $models = $command->queryAll();
                $totalItems = $query->count();

                $commandBbk = $queryBbk->createCommand();
                $modelsBbk = $commandBbk->queryAll();

                $detBbk = array();
                foreach ($modelsBbk as $valBbk) {
                    $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] = isset($detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
                    if ($valBbk['ket'] != "-") {
                        $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['ket'][] = $valBbk['ket'];
                    }
                }

                foreach ($models as $val) {
                    $jKeluar = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]) ? $detBbk[$val['id_jabatan']][$val['kd_barang']]['jml'] : 0;
                    $ket = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) ? join(',', $detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) : '-';

                    $detBom[$i]['no_wo'] = $val['no_wo'];
                    $detBom[$i]['kd_barang'] = $val['kd_barang'];
                    $detBom[$i]['nm_barang'] = $val['nm_barang'];
                    $detBom[$i]['satuan'] = $val['satuan'];
                    $detBom[$i]['harga'] = $val['harga'];
                    $detBom[$i]['qty'] = $val['qty'];
                    $detBom[$i]['jml_keluar'] = $jKeluar;
                    $detBom[$i]['ket'] = $ket;
                    $detBom[$i]['jabatan'] = $val['jabatan'];
                    $i++;
                }

                $jWo++;
            }
        }

        //session_start();  
        $_SESSION['filter'] = $params;

        $totalItems = count($detBom);


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $detBom, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExceltrans() {
        session_start();
        $query = $_SESSION['bom'];
        $kd_bom = $_SESSION['kd_bom'];
        $detail = $_SESSION['detail'];

        $query->limit(null);
        $query->offset(null);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $det = $detail->createCommand();
        $modelsDetail = $det->queryAll();

//        $det = BomDet::find()
//                ->where(['kd_bom' => $kd_bom])
//                ->with(['jabatan', 'barang'])
////                ->orderBy('tbl_jabatan.urutan_produksi ASC, tbl_jabatan.jabatan ASC, barang.nm_barang ASC')
//                ->all();

        $detail = array();
        $i = 0;
        foreach ($modelsDetail as $val) {
            $detail[$val['kd_jab']]['nama_jabatan'] = !empty($val['jabatan']) ? $val['jabatan'] : '-';
            $detail[$val['kd_jab']]['body'][$i]['nama_barang'] = !empty($val['nm_barang']) ? $val['nm_barang'] : '-';
            $detail[$val['kd_jab']]['body'][$i]['satuan'] = !empty($val['satuan']) ? $val['satuan'] : '-';
            $detail[$val['kd_jab']]['body'][$i]['harga'] = !empty($val['harga']) ? $val['harga'] : '0';
            $detail[$val['kd_jab']]['body'][$i]['jumlah'] = !empty($val['qty']) ? $val['qty'] : '-';
            $detail[$val['kd_jab']]['body'][$i]['ket'] = !empty($val['ket']) ? $val['ket'] : '-';
            $i++;
        }
        return $this->render("/expretur/bomtrans", ['model' => $models[0], 'detail' => $detail]);
    }

    public function actionView($id) {
        $query = new Query;
        $query->from(['trans_standar_bahan', 'chassis', 'model'])
                ->where('trans_standar_bahan.kd_model = model.kd_model and trans_standar_bahan.kd_chassis = chassis.kd_chassis and trans_standar_bahan.kd_bom="' . $id . '"')
                ->select("*");

        $command = $query->createCommand();
        $models = $command->query()->read();
        $models['kd_model'] = array('kd_model' => $models['kd_model'], 'model' => $models['model']);

        $det = new Query;
        $det->from('det_standar_bahan as dst')
                ->join('LEFT JOIN', 'tbl_jabatan as j', 'j.id_jabatan = dst.kd_jab')
                ->join('LEFT JOIN', 'barang as b', 'b.kd_barang = dst.kd_barang')
                ->where('dst.kd_bom = "' . $models['kd_bom'] . '"')
                ->orderBy('j.urutan_produksi ASC, j.jabatan ASC, b.nm_barang ASC')
                ->select('dst.*, j.jabatan, b.nm_barang, b.satuan, b.harga as harga_barang, b.harga');
        $commandDet = $det->createCommand();
        $detBom = $commandDet->queryAll();


        session_start();
        $_SESSION['bom'] = $query;
        $_SESSION['kd_bom'] = $models['kd_bom'];
        $_SESSION['detail'] = $det;

        $detail = array();
        foreach ($detBom as $key => $val) {
            $detail[$key]['kd_bom'] = $val['kd_bom'];
            $detail[$key]['qty'] = $val['qty'];
            $detail[$key]['ket'] = $val['ket'];
            $detail[$key]['bagian'] = array('id_jabatan' => $val['kd_jab'], 'jabatan' => $val['jabatan']);
            $detail[$key]['barang'] = array('kd_barang' => $val['kd_barang'], 'nm_barang' => $val['nm_barang'], 'satuan' => $val['satuan'], 'harga' => $val['harga']);
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Bom();
        $model->attributes = $params['bom'];
        $model->kd_model = isset($params['bom']['kd_model']['kd_model']) ? $params['bom']['kd_model']['kd_model'] : '-';
        $model->status = 0;

        if ($model->save()) {
            $detailBom = $params['detailBom'];
            foreach ($detailBom as $val) {
                $det = new BomDet();
                $det->attributes = $val;
                $det->kd_jab = isset($val['bagian']['id_jabatan']) ? $val['bagian']['id_jabatan'] : '-';
                $det->kd_barang = isset($val['barang']['kd_barang']) ? $val['barang']['kd_barang'] : '-';
                $det->kd_bom = $model->kd_bom;
                $det->save();
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
        $model = $this->findModel($id);
        $model->attributes = $params['bom'];
        $model->kd_model = isset($params['bom']['kd_model']['kd_model']) ? $params['bom']['kd_model']['kd_model'] : '-';

        if ($model->save()) {
            $deleteDetail = BomDet::deleteAll(['kd_bom' => $model->kd_bom]);
            $detailBom = $params['detailBom'];
            foreach ($detailBom as $val) {
                $det = new BomDet();
                $det->attributes = $val;
                $det->kd_jab = isset($val['bagian']['id_jabatan']) ? $val['bagian']['id_jabatan'] : '-';
                $det->kd_barang = isset($val['barang']['kd_barang']) ? $val['barang']['kd_barang'] : '-';
                $det->kd_bom = $model->kd_bom;
                $det->save();
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = BomDet::deleteAll(['kd_bom' => $id]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Bom::findOne($id)) !== null) {
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

    public function actionDetail() {
        $det = BomDet::find()
                ->with(['jabatan', 'barang'])
                ->where(['kd_bom' => $models['kd_bom']])
                ->all();

        $detail = array();
        if (!empty($det)) {
            foreach ($det as $key => $val) {
                $detail[] = $val->attributes;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

}

?>
