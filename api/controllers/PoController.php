<?php

namespace app\controllers;

use Yii;
use app\models\TransPo;
use app\models\DetailPo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class PoController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'rekap' => ['get'],
                    'fluktuasi' => ['post'],
                    'view' => ['get'],
                    'listsupplier' => ['get'],
                    'listspp' => ['get'],
                    'kode' => ['get'],
                    'test' => ['get'],
                    'cari' => ['get'],
                    'updtst' => ['get'],
                    'excel' => ['get'],
                    'excelbeli' => ['get'],
                    'excelpantau' => ['get'],
                    'excelfluktuasi' => ['get'],
                    'brgspp' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'bukaprint' => ['post'],
                    'delete' => ['delete'],
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
            $status = TransPo::findOne($key);
            $status->status = 0;
            $status->save();
        }
    }

    public function actionUnlock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = TransPo::findOne($key);
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
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionJmlprint() {
        $query = new Query;
        $query->from('jml_laporan as jl')
                ->select('jl.jumlah')
                ->where(['id' => 1]);
        $command = $query->createCommand();
        $models = $command->query()->read();

//        $model = \app\models\JmlLaporan::findOne(['id' => 1]);
//        $model->jumlah = ($models + 1);
//        $model->save();

        return $models['jumlah'] + 1;
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_po')
                ->select('*')
                ->orderBy('nota DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();

        $cek = TransPo::find()
                ->where('nota = "PCH' . date("y") . '0001"')
                ->One();
        if (!empty($cek)) {
            $kode_mdl = (substr($models['nota'], -4) + 1);
            $kode = substr('0000' . $kode_mdl, strlen($kode_mdl));
            $kode = "PCH" . date("y") . $kode;
        } else {
            $kode = "PCH" . date("y") . '0001';
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_po.nota DESC";
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
                ->from('trans_po')
//                ->join('JOIN', 'detail_po', 'detail_po.nota = trans_po.nota')
                ->join('JOIN', 'supplier', 'supplier.kd_supplier = trans_po.suplier')
//                ->join('RIGHT JOIN', 'barang', 'barang.kd_barang = detail_po.kd_barang')
                ->orderBy($sort)
                ->select("trans_po.*,supplier.nama_supplier");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'nm_barang') {
                    $brg = $this->searchBrg($val);
                    foreach ($brg as $brg_val) {
                        $query->orFilterWhere(['=', 'trans_po.nota', $brg_val]);
                    }
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }


        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        $data = array();
        $i = 0;
        foreach ($models as $key => $val) {
            $data[$key] = $val;
            if ($data[$i]['bayar'] == '0') {
                $data[$i]['bayar'] = 'Tunai';
            } else {
                $data[$i]['bayar'] = 'Kredit';
            }
            if ($data[$i]['status'] == '0') {
                $data[$i]['status_nama'] = 'Belum';
            } else {
                $data[$i]['status_nama'] = 'Sudah';
            }
            
            $sup = \app\models\Supplier::find()
                    ->where(['kd_supplier' => $data[$i]['suplier']])
                    ->One();
            $supplier = (isset($sup->nama_supplier)) ? $sup->nama_supplier : '';
//            $data[$i]['suplier'] = $supplier;
            $i++;
            
            
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_po.nota DESC";
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
                ->from('detail_po as dpo')
                ->join('JOIN', 'trans_po', 'trans_po.nota = dpo.nota')
                ->join('LEFT JOIN', 'det_spp', "det_spp.no_spp = trans_po.spp and det_spp.kd_barang = dpo.kd_barang")
                ->join('LEFT JOIN', 'supplier', 'supplier.kd_supplier = trans_po.suplier')
                ->join('JOIN', 'det_bbm', 'det_bbm.no_po = trans_po.nota and det_bbm.kd_barang = dpo.kd_barang')
                ->join('LEFT JOIN', 'trans_bbm', 'trans_bbm.no_bbm = det_bbm.no_bbm')
                ->join('JOIN', 'barang', 'barang.kd_barang = dpo.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->orderBy($sort)
                ->select("dpo.*,det_spp.*,trans_po.* ,jenis_brg.jenis_brg, supplier.nama_supplier,trans_bbm.surat_jalan,det_bbm.tgl_terima, det_bbm.no_bbm, barang.kd_barang as kode_barang,barang.nm_barang, barang.satuan,barang.harga as hrg_barang");
        //filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'kategori') {
                    if ($val == 'yes') {
                        $query->where("spp !='-'");
                    } elseif ($val == 'no') {
                        $query->where("spp ='-'");
                    }
                } elseif ($key == 'tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'dpo.tgl_pengiriman', $start, $end]);
                } else if ($key == 'nota') {
                    $query->andFilterWhere(['like', 'dpo.nota', $val]);
                } else if ($key == 'spp') {
                    $query->andFilterWhere(['like', 'trans_po.' . $key, $val]);
                } else if ($key == 'nama_supplier') {
                    $query->andFilterWhere(['like', 'supplier.' . $key, $val]);
                } else if ($key == 'nm_barang') {
                    $query->andFilterWhere(['like', 'barang.' . $key, $val]);
                } else if ($key == 'trans_po.bayar') {
//                        $query->where("trans_po.".$key." ='.$val.'");
                    $query->andFilterWhere(['=', $key, $val]);
                }
            }
        }
//        Yii::error($query);
        $data = $this->retRekap($query);

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data['data'], 'totalItems' => $data['totalItems']), JSON_PRETTY_PRINT);
    }

    public function retRekap($query) {
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();
        $data = array();
        $i = 0;
        foreach ($models as $key => $val) {
            $data[$key] = $val;
            if ($data[$i]['bayar'] == '0') {
                $data[$i]['bayar'] = 'Tunai';
            } else {
                $data[$i]['bayar'] = 'Kredit';
            }
            $i++;
        }

        return ['data' => $data, 'totalItems' => $totalItems];
    }

    public function hitung_hari($m, $y) {

        $hasil = cal_days_in_month(CAL_GREGORIAN, $m, $y);

        return $hasil;
    }

    public function actionFluktuasi() {
        session_start();
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "dpo.tgl_pengiriman ASC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
                ->from('detail_po as dpo')
                ->join('JOIN', 'trans_po', 'trans_po.nota = dpo.nota')
                ->join('LEFT JOIN', 'det_spp', "det_spp.no_spp = trans_po.spp and det_spp.kd_barang = dpo.kd_barang")
                ->join('LEFT JOIN', 'supplier', 'supplier.kd_supplier = trans_po.suplier')
                ->join('JOIN', 'det_bbm', 'det_bbm.no_po = trans_po.nota and det_bbm.kd_barang = dpo.kd_barang')
                ->join('LEFT JOIN', 'trans_bbm', 'trans_bbm.no_bbm = det_bbm.no_bbm')
                ->join('JOIN', 'barang', 'barang.kd_barang = dpo.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->orderBy($sort)
                ->groupBy('dpo.harga')
                ->select("dpo.*,trans_po.bayar,supplier.nama_supplier,trans_bbm.surat_jalan,det_bbm.tgl_terima, det_bbm.no_bbm, barang.kd_barang as kode_barang,barang.nm_barang, barang.satuan,barang.harga as hrg_barang");
        //filter

        if (isset($params['bulan']) && isset($params['tahun'])) {


            if ($params['bulan']) {
                $m = $params['bulan'];
            }

            if ($params['tahun']) {
                $y = $params['tahun'];
            }

            $d = $this->hitung_hari($m, $y);

            $start = $y . '-01-01';

            $finish = $y . '-' . $m . '-' . $d;

            $query->andFilterWhere(['between', 'dpo.tgl_pengiriman', $start, $finish]);
        }

        $data = $this->retRekap($query);


        $query->offset(0);
        $_SESSION['queryfluktuasi'] = $query;
        $_SESSION['filterfluktuasi'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data['data'], 'totalItems' => $data['totalItems']), JSON_PRETTY_PRINT);
    }

    public function searchBrg($id) {
        $patern = $id;
        $query = new Query;
        $query->from('detail_po as dpo')
                ->select("*")
                ->join('JOIN', 'barang', 'barang.kd_barang = dpo.kd_barang')
//                ->orderBy('')
//                ->where("barang.nm_barang LIKE %".$patern."%")
                ->select('dpo.nota, barang.nm_barang');
        $query->andFilterWhere(['like', 'barang.nm_barang', $patern]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $data = array();
        foreach ($models as $key) {
            $data[] = $key['nota'];
        }


        $this->setHeader(200);

        return $data;
    }

    public function actionListsupplier() {
        $query = new Query;
        $query->from('supplier')
                ->select("*")
                ->orderBy('kd_supplier ASC');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $data = $model->attributes;
        //supplier
        $sup = \app\models\Supplier::find()
                ->where(['kd_supplier' => $model['suplier']])
                ->One();
        $supplier = (isset($sup->nama_supplier)) ? $sup->nama_supplier : '';
        $kode = (isset($sup->kd_supplier)) ? $sup->kd_supplier : '';
        $data['supplier'] = ['kd_supplier' => $kode, 'nama_supplier' => $supplier];
        //spp
        $spp = \app\models\TransSpp::find()
                ->where(['no_spp' => $model['spp']])
                ->One();
        $no_spp = (isset($spp->no_spp)) ? $spp->no_spp : '';
        $no_proyek = (isset($spp->no_proyek)) ? $spp->no_proyek : '';
        $data['listspp'] = ['no_spp' => $no_spp, 'no_proyek' => $no_proyek];
        $cek = $data['status'];
        $det = DetailPo::find()
                ->with(['barang'])
                ->orderBy('nota')
                ->where(['nota' => $model['nota']])
                ->all();


        $detail = array();
        $no = 1;
        foreach ($det as $key => $val) {
            $detail[$key] = $val->attributes;
            $hargaBarang = (isset($val->barang->harga)) ? $val->barang->harga : '';
            $namaBarang = (isset($val->barang->nm_barang)) ? $val->barang->nm_barang : '';
            $satuanBarang = (isset($val->barang->satuan)) ? $val->barang->satuan : '';
            $detail[$key]['data_barang'] = ['no' => $no, 'tgl_pengiriman' => $val->tgl_pengiriman, 'kd_barang' => $val->kd_barang, 'nm_barang' => $namaBarang, 'harga' => $hargaBarang, 'satuan' => $satuanBarang];
            $no++;
        }
        session_start();
        if ($cek == 1 and $_SESSION['user']['id'] != "1") {
            $msg = 'Detail PO sudah dicetak, silahkan menghubungi admin untuk mencetak ulang';
            $print = 1;
        } else {
            $msg = '';
            $print = 0;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'detail' => $detail, 'msg' => $msg, 'print' => $print), JSON_PRETTY_PRINT);
    }

    public function actionUpdtst($id) {
        $model = TransPo::findOne(['nota' => $id]);
        $model->status = 1;
        $model->save();
    }

    public function actionBukaprint() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $centang = $params['nota'];
        foreach ($centang as $key => $val) {
            $status = TransPo::findOne($key);
            $status->status = 0;
            $status->save();
        }
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TransPo();
        $model->attributes = $params['formpo'];
        $model->suplier = $params['formpo']['supplier']['kd_supplier'];
        $model->lock = 1;
        if (!empty($model->tanggal)) {
            $model->tanggal = date("Y-m-d", strtotime($params['formpo']['tanggal']));
        } else {
            $model->tanggal = NULL;
        }
        $model->spp = (empty($params['formpo']['listspp']['no_spp'])) ? '-' : $params['formpo']['listspp']['no_spp'];
        //
        session_start();
        $id = $_SESSION['user']['id'];
        $mdl = \app\models\Pengguna::findOne(['id' => $id]);
        $model->pemberi_order = $mdl['nama'];

        if ($model->save()) {
            $details = $params['details'];
            foreach ($details as $val) {
                $det = new DetailPo();
                $det->attributes = $val;
                $det->kd_barang = $val['data_barang']['kd_barang'];
                if (!empty($det->tgl_pengiriman)) {
                    $det->tgl_pengiriman = date("Y-m-d", strtotime($val['data_barang']['tgl_pengiriman']));
                } else {
                    $det->tgl_pengiriman = NULL;
                }
                $det->nota = $model->nota;
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
        $model->attributes = $params['formpo'];
        $model->lock = 1;
        if (!empty($model->tanggal)) {
            $model->tanggal = date("Y-m-d", strtotime($params['formpo']['tanggal']));
        } else {
            $model->tanggal = NULL;
        }
        $model->spp = (empty($params['formpo']['listspp']['no_spp'])) ? '-' : $params['formpo']['listspp']['no_spp'];

        if ($model->save()) {
            $details = $params['details'];
            $del = DetailPo::deleteAll('nota = "' . $model->nota . '"');

            foreach ($details as $val) {
                if (isset($val['data_barang']['kd_barang'])) {
                    $det = new DetailPo();
                    $det->attributes = $val;
                    $det->kd_barang = $val['data_barang']['kd_barang'];
                    if (empty($det->tgl_pengiriman)) {
                        $det->tgl_pengiriman = NULL;
                    }
                    $det->nota = $model->nota;
                    $det->save();
                }
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
        $deleteDetail = DetailPo::deleteAll(['nota' => $id]);
        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransPo::findOne($id)) !== null) {
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

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_po')
                ->join('LEFT JOIN', 'supplier', 'trans_po.suplier=supplier.kd_supplier')
                ->orderBy('nota DESC')
                ->select("*")
                ->where(['like', 'nota', $params['nama']])
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        Yii::error($models);

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionSelect() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('det_spp')
                ->select("*")
                ->where('det_spp.no_spp =' . $param)
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        Yii::error($models);

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionBrgspp() {
        $param = $_REQUEST;
        $query = new Query;
        $models = array();
        if ((!isset($param['nospp']) || $param['nospp'] == '-' || $param['nospp'] == "") and ! empty($param['namabrg'])) {
            $query->from('barang')
                    ->select("*")
                    ->where(['like', 'nm_barang', $param['namabrg']])
                    ->orWhere(['like', 'kd_barang', $param['namabrg']])
                    ->andWhere("nm_barang != '-' && kd_barang != '-'");

            $command = $query->createCommand();
            $models = $command->queryAll();
        } else if (isset($param['nospp']) and $param['nospp'] != "") {
            $query->from("det_spp")
                    ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang ')
                    ->where('det_spp.no_spp =' . $param['nospp'])
                    ->andWhere(['LIKE', 'nm_barang', $param['namabrg']])
                    ->groupBy('barang.kd_barang')
                    ->select("det_spp.no_spp,sum(det_spp.qty) as jml,det_spp.kd_barang,barang.*");

            $command = $query->createCommand();
            $models = $command->queryAll();
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/po", ['models' => $models, 'filter' => $filter]);
    }

    public function actionExcelbeli() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/belitunaikredit", ['models' => $models, 'filter' => $filter, 'no_print' => $this->actionJmlprint()]);
    }

    public function actionExcelpantau() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekappantau", ['models' => $models, 'filter' => $filter]);
    }

    public function actionExcelfluktuasi() {
        session_start();
        $query = $_SESSION['queryfluktuasi'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekapfluktuasiharga", ['models' => $models]);
    }

}

?>
