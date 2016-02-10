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
                    'kode' => ['post'],
                    'cari' => ['get'],
                    'rekappergerakan' => ['post'],
                    'excelpergerakan' => ['get'],
                    'excelpergerakan2' => ['get'],
                    'rekapbbmbbk' => ['post'],
                    'excelbbmbbk' => ['get'],
                    'upload' => ['post'],
                    'removegambar' => ['post'],
                ],
            ]
        ];
    }

    public function actionUpload() {
        if (!empty($_FILES)) {
            $tempPath = $_FILES['file']['tmp_name'];
            $newName = \Yii::$app->landa->urlParsing($_FILES['file']['name']);

            $uploadPath = \Yii::$app->params['pathImg'] . $_GET['folder'] . DIRECTORY_SEPARATOR . $newName;

            move_uploaded_file($tempPath, $uploadPath);
            $a = \Yii::$app->landa->createImg($_GET['folder'] . '/', $newName, $_POST['kode']);

            $answer = array('answer' => 'File transfer completed', 'name' => $newName);
            if ($answer['answer'] == "File transfer completed") {
                $barang = Barang::findOne($_POST['kode']);
                $foto = json_decode($barang->foto, true);
                $foto[] = array('name' => $newName);
                $barang->foto = json_encode($foto);
                $barang->save();
            }

            echo json_encode($answer);
        } else {
            echo 'No files';
        }
    }

    public function actionRemovegambar() {
        $params = json_decode(file_get_contents("php://input"), true);
        $barang = Barang::findOne($params['kode']);
        $foto = json_decode($barang->foto, true);
        foreach ($foto as $key => $val) {
            if ($val['name'] == $params['nama']) {
                unset($foto[$key]);
                \Yii::$app->landa->deleteImg('barang/', $params['kode'], $params['nama']);
            }
        }
        $barang->foto = json_encode($foto);
        $barang->save();

        echo json_encode($foto);
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
        $params = json_decode(file_get_contents("php://input"), true);

        $tglStart = '';
        $tglEnd = '';

        $start = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $start = explode('-', $start);
        $tgl = array();

        $tglStart = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $tglEnd = date("Y-m-d", strtotime($params['tanggal']['endDate']));


        if (isset($params['limit']))
            $limit = $params['limit'];

        $bbm = new Query;
        $bbm->from('det_bbm')
                ->join('Join', 'trans_bbm', 'trans_bbm.no_bbm = det_bbm.no_bbm')
                ->join('Join', 'barang', 'barang.kd_barang = det_bbm.kd_barang')
                ->select("trans_bbm.tgl_nota as tgl_terima, barang.kd_barang, barang.nm_barang, barang.satuan, barang.min, barang.saldo, det_bbm.jumlah")
                ->orderBy('barang.nm_barang')
                ->where('trans_bbm.tgl_nota >= "' . $tglStart . '" and trans_bbm.tgl_nota <= "' . $tglEnd . '"');

        if (isset($params['barang']))
            $bbm->andFilterWhere(['det_bbm.kd_barang' => $params['barang']['kd_barang']]);

        $commandBBM = $bbm->createCommand();
        $modelBBM = $commandBBM->queryAll();

        $data = array();
        $saldo = array();
        foreach ($modelBBM as $valBbm) {
            $data[$valBbm['kd_barang']]['kd_barang'] = $valBbm['kd_barang'];
            $data[$valBbm['kd_barang']]['barang'] = $valBbm['nm_barang'];
            $data[$valBbm['kd_barang']]['satuan'] = $valBbm['satuan'];
            $data[$valBbm['kd_barang']]['stok_minim'] = $valBbm['min'];
            $data[$valBbm['kd_barang']]['saldo'] = $valBbm['saldo'];
            $data[$valBbm['kd_barang']]['stok_keluar'] = 0;
            $data[$valBbm['kd_barang']]['stok_masuk'] = isset($data[$valBbm['kd_barang']]['stok_masuk']) ? $data[$valBbm['kd_barang']]['stok_masuk'] + $valBbm['jumlah'] : $valBbm['jumlah'];

            $saldo[$valBbm['kd_barang']] = isset($saldo[$valBbm['kd_barang']]) ? $saldo[$valBbm['kd_barang']] - $valBbm['jumlah'] : $this->stokakhirmingguan($valBbm['kd_barang'], $tglEnd, $valBbm['saldo']) - $valBbm['jumlah'];

            $data[$valBbm['kd_barang']]['saldo_awal'] = $saldo[$valBbm['kd_barang']];
            $data[$valBbm['kd_barang']]['saldo_akhir'] = $data[$valBbm['kd_barang']]['saldo_awal'] + $data[$valBbm['kd_barang']]['stok_masuk'];
        }

        $bbk = new Query;
        $bbk->from('det_bbk')
                ->join('Join', 'trans_bbk', 'trans_bbk.no_bbk = det_bbk.no_bbk')
                ->join('Join', 'barang', 'barang.kd_barang = det_bbk.kd_barang')
                ->select("barang.kd_barang, barang.nm_barang, barang.satuan, barang.min, barang.saldo, det_bbk.jml, trans_bbk.tanggal")
                ->orderBy('barang.nm_barang')
                ->where('trans_bbk.tanggal >= "' . $tglStart . '" and trans_bbk.tanggal <= "' . $tglEnd . '"');

        if (isset($params['barang']))
            $bbk->andWhere(['det_bbk.kd_barang' => $params['barang']['kd_barang']]);

        $commandBBK = $bbk->createCommand();
        $modelBBK = $commandBBK->queryAll();

        $i = 1;
        foreach ($modelBBK as $valBbk) {
            $data[$valBbk['kd_barang']]['kd_barang'] = $valBbk['kd_barang'];
            $data[$valBbk['kd_barang']]['barang'] = $valBbk['nm_barang'];
            $data[$valBbk['kd_barang']]['satuan'] = $valBbk['satuan'];
            $data[$valBbk['kd_barang']]['stok_minim'] = $valBbk['min'];
            $data[$valBbk['kd_barang']]['saldo'] = $valBbk['saldo'];
            $data[$valBbk['kd_barang']]['stok_masuk'] = isset($data[$valBbk['kd_barang']]['stok_masuk']) ? $data[$valBbk['kd_barang']]['stok_masuk'] : 0;
            $data[$valBbk['kd_barang']]['stok_keluar'] = isset($data[$valBbk['kd_barang']]['stok_keluar']) ? $data[$valBbk['kd_barang']]['stok_keluar'] + $valBbk['jml'] : $valBbk['jml'];

            $saldo[$valBbk['kd_barang']] = isset($saldo[$valBbk['kd_barang']]) ? $saldo[$valBbk['kd_barang']] + $valBbk['jml'] : $this->stokakhirmingguan($valBbk['kd_barang'], $tglEnd, $valBbk['saldo']) + $valBbk['jml'];
            $data[$valBbk['kd_barang']]['saldo_awal'] = $saldo[$valBbk['kd_barang']];
            $data[$valBbk['kd_barang']]['saldo_akhir'] = (isset($data[$valBbk['kd_barang']]['saldo_akhir']) ? $data[$valBbk['kd_barang']]['saldo_akhir'] : $data[$valBbk['kd_barang']]['saldo_awal'] ) - $valBbk['jml'];
            $i++;
        }

        session_start();
        $_SESSION['queryBbm'] = $bbm;
        $_SESSION['queryBbk'] = $bbk;
        $_SESSION['periode'] = date("d/m/Y", strtotime($tglStart)) . ' - ' . date("d/m/Y", strtotime($tglEnd));
        $_SESSION['tanggal'] = $tgl;
        $_SESSION['tanggalEnd'] = $tglEnd;

        $sorted = Yii::$app->landa->array_orderby($data, 'barang', SORT_ASC);
        echo json_encode(array('status' => 1, 'data' => $sorted));
    }

    public function stokakhirmingguan($kd_barang, $tanggal, $saldoSaatIni) {
        $end = $start = explode('-', $tanggal);
        $tglAfter = mktime(0, 0, 0, $end[1], $end[2] + 1, $end[0]);
        $tglAfter = date("Y-m-d", $tglAfter);

        $jmlbbk = new query;
        $jmlbbk->from('trans_bbk')
                ->join('JOIN', 'det_bbk', 'det_bbk.no_bbk = trans_bbk.no_bbk')
                ->select('sum(det_bbk.jml) as jml_bbk')
                ->where('det_bbk.kd_barang="' . $kd_barang . '" and (trans_bbk.tanggal >= "' . $tanggal . '" and trans_bbk.tanggal <= "' . date("Y-m-d") . '")');
        $cJmlBbk = $jmlbbk->createCommand();
        $mJmlBbk = $cJmlBbk->queryOne();

        $jmlbbm = new query;
        $jmlbbm->from('det_bbm')
                ->select('sum(det_bbm.jumlah) as jml_bbm')
                ->where('det_bbm.kd_barang= "' . $kd_barang . '" and (tgl_terima >= "' . $tanggal . '" and det_bbm.tgl_terima <= "' . date("Y-m-d") . '")');
        $cJmlBbm = $jmlbbm->createCommand();
        $mJmlBbm = $cJmlBbm->queryOne();

        $stok = ($saldoSaatIni + $mJmlBbk['jml_bbk']) - $mJmlBbm['jml_bbm'];
        return $stok;
    }

    public function actionExcelpergerakan2() {
        session_start();

        $tgl = isset($_SESSION['tanggal']) ? $_SESSION['tanggal'] : array();
        $bbm = $_SESSION['queryBbm'];
        $bbk = $_SESSION['queryBbk'];

//        $bbm->where(null);
//        $bbm->where(['det_bbm.tgl_terima' => $tgl]);
        $commandBbm = $bbm->createCommand();
        $modelBBM = $commandBbm->queryAll();

//        $bbk->where(null);
//        $bbk->where(['trans_bbk.tanggal' => $tgl]);
        $commandBbk = $bbk->createCommand();
        $modelBBK = $commandBbk->queryAll();

        $data = array();
        $saldo = array();
        foreach ($modelBBM as $valBbm) {
            $data[$valBbm['kd_barang']]['kd_barang'] = $valBbm['kd_barang'];
            $data[$valBbm['kd_barang']]['barang'] = $valBbm['nm_barang'];
            $data[$valBbm['kd_barang']]['satuan'] = $valBbm['satuan'];
            $data[$valBbm['kd_barang']]['stok_minim'] = $valBbm['min'];
            $data[$valBbm['kd_barang']]['stok_keluar'] = 0;
            $data[$valBbm['kd_barang']]['stok_masuk'] = isset($data[$valBbm['kd_barang']]['stok_masuk']) ? $data[$valBbm['kd_barang']]['stok_masuk'] + $valBbm['jumlah'] : $valBbm['jumlah'];

            $saldo[$valBbm['kd_barang']] = isset($saldo[$valBbm['kd_barang']]) ? $saldo[$valBbm['kd_barang']] - $valBbm['jumlah'] : $this->stokakhirmingguan($valBbm['kd_barang'], $_SESSION['tanggalEnd'], $valBbm['saldo']) - $valBbm['jumlah'];

            $data[$valBbm['kd_barang']]['saldo_awal'] = $saldo[$valBbm['kd_barang']];
            $data[$valBbm['kd_barang']]['saldo_akhir'] = $data[$valBbm['kd_barang']]['saldo_awal'] + $data[$valBbm['kd_barang']]['stok_masuk'];
        }

        foreach ($modelBBK as $valBbk) {
            $data[$valBbk['kd_barang']]['kd_barang'] = $valBbk['kd_barang'];
            $data[$valBbk['kd_barang']]['barang'] = $valBbk['nm_barang'];
            $data[$valBbk['kd_barang']]['satuan'] = $valBbk['satuan'];
            $data[$valBbk['kd_barang']]['stok_minim'] = $valBbk['min'];
            $data[$valBbk['kd_barang']]['stok_masuk'] = isset($data[$valBbk['kd_barang']]['stok_masuk']) ? $data[$valBbk['kd_barang']]['stok_masuk'] : 0;
            $data[$valBbk['kd_barang']]['stok_keluar'] = isset($data[$valBbk['kd_barang']]['stok_keluar']) ? $data[$valBbk['kd_barang']]['stok_keluar'] + $valBbk['jml'] : $valBbk['jml'];

            $saldo[$valBbk['kd_barang']] = isset($saldo[$valBbk['kd_barang']]) ? $saldo[$valBbk['kd_barang']] + $valBbk['jml'] : $this->stokakhirmingguan($valBbk['kd_barang'], $_SESSION['tanggalEnd'], $valBbk['saldo']) + $valBbk['jml'];

            $data[$valBbk['kd_barang']]['saldo_awal'] = $saldo[$valBbk['kd_barang']];
            $data[$valBbk['kd_barang']]['saldo_akhir'] = (isset($data[$valBbk['kd_barang']]['saldo_akhir']) ? $data[$valBbk['kd_barang']]['saldo_akhir'] : $data[$valBbk['kd_barang']]['saldo_awal'] ) - $valBbk['jml'];
        }

        $periode = $_SESSION['periode'];
        return $this->render("/expretur/pergerakanbarang2", ['models' => $data, 'tgl' => $tgl, 'periode' => $periode]);
    }

    public function actionExcelpergerakan() {
        session_start();

        $tgl = isset($_SESSION['tanggal']) ? $_SESSION['tanggal'] : '';
        $bbm = $_SESSION['queryBbm'];
        $bbk = $_SESSION['queryBbk'];

        $bbm->where(null);
        $bbm->where(['det_bbm.tgl_terima' => $tgl]);
        $commandBbm = $bbm->createCommand();
        $modelBBM = $commandBbm->queryAll();

        $bbk->where(null);
        $bbk->where(['trans_bbk.tanggal' => $tgl]);
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

        $periode = $_SESSION['periode'];
        return $this->render("/expretur/pergerakanbarang", ['models' => $data, 'tgl' => $tgl, 'periode' => $periode]);
    }

    public function actionRekapbbmbbk() {
        $params = json_decode(file_get_contents("php://input"), true);
        $tglStart = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $tglEnd = date("Y-m-d", strtotime($params['tanggal']['endDate']));

        if (isset($params['limit']))
            $limit = $params['limit'];

        $bbm = new Query;
        $bbm->from('det_bbm')
                ->join('Join', 'trans_bbm', 'trans_bbm.no_bbm = det_bbm.no_bbm')
                ->join('Join', 'barang', 'barang.kd_barang = det_bbm.kd_barang')
                ->join('Join', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->select("trans_bbm.tgl_nota as tgl_terima, barang.kd_barang, barang.nm_barang, barang.satuan, barang.min, barang.saldo, det_bbm.jumlah, jenis_brg.jenis_brg as golongan")
                ->orderBy('jenis_brg.jenis_brg ASC, barang.nm_barang ASC')
                ->where('trans_bbm.tgl_nota >= "' . $tglStart . '" and trans_bbm.tgl_nota <= "' . $tglEnd . '"');

        if (isset($params['barang']))
            $bbm->andFilterWhere(['det_bbm.kd_barang' => $params['barang']['kd_barang']]);

        $bbk = new Query;
        $bbk->from('det_bbk')
                ->join('Join', 'trans_bbk', 'trans_bbk.no_bbk = det_bbk.no_bbk')
                ->join('Join', 'barang', 'barang.kd_barang = det_bbk.kd_barang')
                ->join('Join', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->select("barang.kd_barang, barang.nm_barang, barang.satuan, barang.min, barang.saldo, det_bbk.jml, trans_bbk.tanggal, jenis_brg.jenis_brg as golongan")
                ->orderBy('jenis_brg.jenis_brg ASC, barang.nm_barang ASC')
                ->where('trans_bbk.tanggal >= "' . $tglStart . '" and trans_bbk.tanggal <= "' . $tglEnd . '"');

        if (isset($params['barang']))
            $bbk->andWhere(['det_bbk.kd_barang' => $params['barang']['kd_barang']]);

        session_start();
        $_SESSION['queryBbm'] = $bbm;
        $_SESSION['queryBbk'] = $bbk;
        $_SESSION['periode'] = date("d/m/Y", strtotime($tglStart)) . ' - ' . date("d/m/Y", strtotime($tglEnd));
    }

    public function actionExcelbbmbbk() {
        session_start();

        $bbm = $_SESSION['queryBbm'];
        $bbk = $_SESSION['queryBbk'];

        $commandBbm = $bbm->createCommand();
        $modelBBM = $commandBbm->queryAll();

        $commandBbk = $bbk->createCommand();
        $modelBBK = $commandBbk->queryAll();

        $data = array();
        foreach ($modelBBM as $valBbm) {
            $data[$valBbm['kd_barang']]['kd_barang'] = $valBbm['kd_barang'];
            $data[$valBbm['kd_barang']]['barang'] = $valBbm['nm_barang'];
            $data[$valBbm['kd_barang']]['golongan'] = $valBbm['golongan'];
            $data[$valBbm['kd_barang']]['satuan'] = $valBbm['satuan'];
            $data[$valBbm['kd_barang']]['golongan'] = $valBbm['golongan'];
            $data[$valBbm['kd_barang']]['jmlBbk'] = 0;
            $data[$valBbm['kd_barang']]['jmlBbm'] = isset($data[$valBbm['kd_barang']]['jmlBbm']) ? $data[$valBbm['kd_barang']]['jmlBbm'] + $valBbm['jumlah'] : $valBbm['jumlah'];
        }

        foreach ($modelBBK as $valBbk) {
            $data[$valBbk['kd_barang']]['kd_barang'] = $valBbk['kd_barang'];
            $data[$valBbk['kd_barang']]['barang'] = $valBbk['nm_barang'];
            $data[$valBbk['kd_barang']]['satuan'] = $valBbk['satuan'];
            $data[$valBbk['kd_barang']]['golongan'] = $valBbk['golongan'];
            $data[$valBbk['kd_barang']]['saldo_awal'] = $valBbk['saldo'];
            $data[$valBbk['kd_barang']]['jmlBbm'] = isset($data[$valBbk['kd_barang']]['jmlBbm']) ? $data[$valBbk['kd_barang']]['jmlBbm'] : 0;
            $data[$valBbk['kd_barang']]['jmlBbk'] = isset($data[$valBbk['kd_barang']]['jmlBbk']) ? $data[$valBbk['kd_barang']]['jmlBbk'] + $valBbk['jml'] : $valBbk['jml'];
        }
        $periode = $_SESSION['periode'];
        return $this->render("/expretur/laporanbbmbbk", ['models' => $data, 'periode' => $periode]);
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
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('barang')
                ->select('*')
                ->orderBy('kd_barang DESC')
                ->where(['jenis' => $params['kd_jenis']['kd_jenis']])
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();

        if (empty($models)) {
            $kode = $params['kd_jenis']['kd'] . '00001';
        } else {
            $kode = $models['kd_barang'] + 1;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "nm_barang DESC";
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
                ->from(['barang', 'jenis_brg'])
                ->where('barang.jenis = jenis_brg.kd_jenis')
                ->orderBy($sort)
                ->select("barang.*, jenis_brg.*");

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
        $data = array();
        foreach ($models as $key => $val) {
            $data[$key] = $val;
            $data[$key]['foto'] = json_decode($val['foto'], true);
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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
        $model->jenis = $params['jenis']['kd_jenis'];
        if (isset($params['foto'])) {
            $model->foto = json_encode($params['foto']);
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
        $model = $this->findModel($id);
        $model->attributes = $params;
        if (isset($params['foto'])) {
            $model->foto = json_encode($params['foto']);
        }
        $model->jenis = $params['jenis']['kd_jenis'];

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

        session_start();

        $trash = new \app\models\BarangTrash();
        $trash->attributes = $model->attributes;
        $trash->tgl_hapus = date("Y-m-d h:i:s");
        $trash->user_id = $_SESSION['user']['id'];
        $trash->save();

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

<<<<<<< HEAD
    public function actionCari() {
=======
    public function actionCari()
    {
        $params = $_REQUEST;
        $query = new Query;
        $barang = $params['barang'];
        
        if (strlen($barang) > 6) {
            $barang = substr($barang, 0, 6);
        } else {
            $barang = $barang;
        }
        
        $query->from('barang')
                ->select("*")
                ->where(['like', 'nm_barang', $barang])
                ->orWhere(['like', 'kd_barang', $barang])
                ->andWhere("nm_barang != '-' && kd_barang != '-'");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCariBarcode()
    {
>>>>>>> branch 'master' of https://github.com/landasystems/kta_karoseri.git
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
