<?php

namespace app\controllers;

use Yii;
use app\models\TransBbm;
use app\models\DetBbm;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BbmController extends Controller {

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
                    'excel' => ['get'],
                    'excelrekap' => ['get'],
                    'exceldet' => ['get'],
                    'rekap' => ['get'],
                    'petugas' => ['get'],
                    'listbbm' => ['get'],
                    'detailstok' => ['post'],
                    'excelserahterima' => ['get'],
                    'caribarang' => ['post'],
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
            $status = TransBbm::findOne($key);
            $status->lock = 1;
            $status->save();
        }
    }

    public function actionUnlock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = TransBbm::findOne($key);
            $status->lock = 0;
            $status->save();
        }
    }

    public function actionCaribarang() {
        $params = json_decode(file_get_contents("php://input"), true);
        $kdBrg = array();
        foreach ($params['listBarang'] as $val) {
            $kdBrg[] = isset($val['kd_barang']) ? $val['kd_barang'] : '';
        }
        $barang = isset($params['barang']) ? $params['barang'] : '';
        $po = isset($params['no_po']) ? $params['no_po'] : '';
        $query = new Query;
        $query->from('detail_po')
                ->join('JOIN', 'barang', 'barang.kd_barang = detail_po.kd_barang')
                ->select("barang.kd_barang, barang.nm_barang, detail_po.jml as jml_po, detail_po.nota")
                ->where(['like', 'barang.nm_barang', $barang])
                ->orWhere(['like', 'barang.kd_barang', $barang])
                ->andWhere(['like', 'detail_po.nota', $po])
                ->andWhere(['NOT IN', 'barang.kd_barang', $kdBrg])
                ->andWhere("barang.nm_barang != '-' && barang.kd_barang != '-'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $data = array();
        $i = 0;
        foreach ($models as $key => $val) {
            $query = new Query;
            $query->from('det_bbm')
                    ->join('JOIN', 'trans_bbm', 'det_bbm.no_bbm = trans_bbm.no_bbm')
                    ->select('sum(det_bbm.jumlah) as jml_masuk')
                    ->where('trans_bbm.no_po = "' . $val['nota'] . '" and det_bbm.kd_barang = "' . $val['kd_barang'] . '"');
            $command = $query->createCommand();
            $bbm = $command->query()->read();

            $models[$key] = $val;
            $models[$i]['jml_po'] = $val['jml_po'];
            $models[$i]['telah_diambil'] = $bbm['jml_masuk'];
            $models[$i]['sisa_ambil'] = $val['jml_po'] - $bbm['jml_masuk'];
            $i++;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListbbm() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('trans_bbm')
                ->select("no_bbm, surat_jalan")
                ->where('no_bbm like "%' . $param['nama'] . '%"')
                ->orderBy('no_bbm DESC')
                ->limit(15);

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

    public function actionKode() {
        $query = new Query;
        $query->from('trans_bbm')
                ->select('*')
                ->orderBy('no_bbm DESC')
                ->limit(1);

        $cek = TransBbm::findOne('no_bbm = "BM' . date("y") . '0001"');
        if (empty($cek)) {
            $command = $query->createCommand();
            $models = $command->query()->read();
            $urut = substr($models['no_bbm'], 4) + 1;
            $kode = substr('0000' . $urut, strlen($urut));
            $kode = "BM" . date("y") . $kode;
        } else {
            $kode = "BM" . date("y") . "0001";
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tb.no_bbm DESC";
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
                ->from('trans_bbm as tb')
                ->join('LEFT JOIN', 'supplier as su', 'tb.kd_suplier= su.kd_supplier')
//                ->join('JOIN', 'det_bbm', 'tb.no_bbm = det_bbm.no_bbm')
                ->orderBy($sort)
                ->select("tb.*,su.nama_supplier as nama_supplier");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $val) {
            $po = \app\models\TransPo::findOne($val['no_po']);
            $wo = \app\models\Womasuk::findOne($val['no_wo']);

            if (!empty($po))
                $supplier = \app\models\Supplier::findOne($po->suplier);

            $models[$key]['po'] = (!empty($po)) ? $po->attributes : array();
            $models[$key]['wo'] = (!empty($wo)) ? $wo->attributes : array();
            $models[$key]['supplier'] = (!empty($supplier)) ? $supplier->attributes : array();
        }
        $totalItems = $query->count();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "db.no_bbm DESC";
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
                ->from('det_bbm as db')
                ->join('JOIN', 'trans_bbm as tb', 'tb.no_bbm= db.no_bbm')
                ->join('LEFT JOIN', 'supplier as su', 'tb.kd_suplier= su.kd_supplier')
                ->join('LEFT JOIN', 'trans_po as po', 'tb.no_po= po.nota')
                ->join('LEFT JOIN', 'trans_spp as spp', 'spp.no_spp= po.spp')
                ->join('LEFT JOIN', 'barang', 'barang.kd_barang = db.kd_barang')
                ->orderBy($sort)
                ->select("po.bayar,spp.no_spp,po.nota,tb.tgl_nota as tanggal_nota,db.tgl_terima, db.no_bbm as no_bbm, barang.kd_barang as kd_barang, barang.nm_barang,
                    barang.satuan, db.jumlah as jumlah, tb.surat_jalan, db.no_po, su.nama_supplier, db.keterangan");
        //filter
//        print_r($params['limit']);
//        echo $limit;
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
//            echo 'asd';
            foreach ($filter as $key => $val) {
                if ($key == 'tgl_nota') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'tb.tgl_nota', $start, $end]);
                } elseif ($key == 'kat' && !empty($val)) {
                    $query->andFilterWhere(['=', 'barang.kat', $val]);
                } elseif ($key == 'bayar' && !empty($val)) {
                    $query->andFilterWhere(['=', 'po.bayar', $val]);
                } else {
                    $query->andFilterWhere(['LIKE', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;
        $_SESSION['periode'] = isset($filter['tgl_nota']) ? $filter['tgl_nota'] : '';

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $data = $model->attributes;
        $data['po'] = array('nota' => $model->no_po);
        $querySup = new Query;
        $querySup->select("*")
                ->from('supplier')
                ->where('kd_supplier="' . $model->kd_suplier . '"');
        $commandSup = $querySup->createCommand();
        $sup = $commandSup->query()->read();

        $det = DetBbm::find()
                ->with(['barang'])
                ->orderBy('id')
                ->where(['no_bbm' => $model->no_bbm])
                ->all();
        $detail = array();
        $i = 0;
        foreach ($det as $key => $val) {
            $query = new Query;
            $query->from('detail_po')
                    ->join('JOIN', 'trans_po', 'detail_po.nota = trans_po.nota')
                    ->join('JOIN', 'trans_bbm', 'trans_bbm.no_po = trans_po.nota')
                    ->select('sum(detail_po.jml) as jml_po')
                    ->where('trans_bbm.no_bbm = "' . $val->no_bbm . '" and detail_po.kd_barang = "' . $val->kd_barang . '"');
            $command = $query->createCommand();
            $po = $command->query()->read();

            $detail[$key] = $val->attributes;
            $namaBarang = (isset($val->barang->nm_barang)) ? $val->barang->nm_barang : '';
            $satuan = (isset($val->barang->satuan)) ? $val->barang->satuan : '';

            $detail[$key]['barang'] = [
                'kd_barang' => $val->kd_barang,
                'nm_barang' => $namaBarang,
                'satuan' => $satuan,
                'jml_po' => $po['jml_po'],
                'telah_diambil' => $po['jml_po'] - $val->jumlah,
                'sisa_ambil' => $po['jml_po'],
            ];
            $i++;
        }


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'sup' => $sup, 'details' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);

        $model = new TransBbm();
        $model->attributes = $params['form'];
        $findNumber = TransBbm::find()->orderBy('no_bbm DESC')->one();
        $lastNumber = (int) substr($findNumber->no_bbm, -5);
        $model->no_bbm = 'BM' . date('y', strtotime($model->tgl_nota)) . substr('00000' . ($lastNumber + 1), -5);
        $model->kd_suplier = $params['form']['kd_supplier'];
        $model->no_wo = (isset($params['form']['wo']['no_wo']) ? $params['form']['wo']['no_wo'] : '-');
        $model->no_po = (isset($params['form']['po']['nota'])) ? $params['form']['po']['nota'] : NULL;
        $model->lock = 1;
        if ($model->save()) {
            //ambil no spp
            $no_spp = \app\models\TransPo::find()->where('nota="' . $model->no_po . '"')->one();

            $detailBbm = $params['detBbm'];
            foreach ($detailBbm as $val) {
                if (isset($val['barang']['kd_barang'])) {
                    $det = new DetBbm();
                    $det->attributes = $val;
                    $det->kd_barang = $val['barang']['kd_barang'];
                    $det->no_bbm = $model->no_bbm;
                    $det->no_po = $model->no_po;
                    $det->save();

                    if (!empty($no_spp)) {
                        //update tanggal aktual spp
                        $detSpp = \app\models\DetSpp::find()->where('no_spp = "' . $no_spp->spp . '" and kd_barang="' . $det->kd_barang . '"')->one();
                        if (!empty($detSpp)) {
                            $detSpp->a = $model->tgl_nota;
                            $detSpp->save();
                        }
                    }

                    //update stok barang
                    $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                    $barang->saldo += $det->jumlah;
                    $barang->save();
                }
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
        $model->attributes = $params['form'];

        if (isset($params['form']['po']['kd_suplier'])) {
            $model->kd_suplier = $params['form']['kd_suplier'];
        } else if (isset($params['form']['kd_supplier'])) {
            $model->kd_suplier = $params['form']['kd_supplier'];
        } else {
            $model->kd_suplier = '-';
        }
        $model->no_wo = (isset($params['form']['wo']['no_wo'])) ? $params['form']['wo']['no_wo'] : '-';
        $model->no_po = (isset($params['form']['po']['nota'])) ? $params['form']['po']['nota'] : NULL;
        $model->lock = 1;
        if ($model->save()) {
            //ambil no spp
            $no_spp = \app\models\TransPo::find()->where('nota="' . $model->no_po . '"')->one();

            // mengembalikan stok barang
            $detail = DetBbm::find()->where('no_bbm = "' . $model->no_bbm . '"')->all();
            foreach ($detail as $detbbm) {
                $barang = Barang::find()->where('kd_barang="' . $detbbm->kd_barang . '"')->one();
                $barang->saldo -= $detbbm->jumlah;
                $barang->save();

                //hapus detail bbm
                $detbbm->delete();
            }

            $detailBbm = $params['detBbm'];
            foreach ($detailBbm as $val) {
                $det = new DetBbm();
                $det->attributes = $val;
                $det->kd_barang = $val['barang']['kd_barang'];
                $det->no_bbm = $model->no_bbm;
                $det->no_po = $model->no_po;
                $det->save();

                //update tanggal aktual spp
                if (!empty($no_spp)) {
                    $detSpp = \app\models\DetSpp::find()->where('no_spp = "' . $no_spp->spp . '" and kd_barang="' . $det->kd_barang . '"')->one();
                    if (!empty($detSpp)) {
                        $detSpp->a = $model->tgl_nota;
                        $detSpp->save();
                    }
                }

                //update stok barang
                $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                $barang->saldo += $det->jumlah;
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
        $model = $this->findModel($id);
        if ($model->delete()) {
            // mengembalikan stok barang
            $detail = DetBbm::find()->where('no_bbm = "' . $model->no_bbm . '"')->all();
            foreach ($detail as $detbbk) {
                $barang = Barang::find()->where('kd_barang="' . $detbbk->kd_barang . '"')->one();
                $barang->saldo -= $detbbk->jumlah;
                $barang->save();
            }

            //hapus detail bbk
            $delBbm = DetBbm::deleteAll('no_bbm = "' . $id . '"');

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransBbm::findOne($id)) !== null) {
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
        $filter = $_SESSION['filter'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/bbm", ['models' => $models, 'filter' => $filter]);
    }

    public function actionExcelrekap() {
        session_start();
        $query = $_SESSION['query'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekapbbm", ['models' => $models]);
    }

    public function actionExcelserahterima() {
        session_start();
        $query = $_SESSION['query'];
        $periode = $_SESSION['periode'];
        $command = $query->createCommand();
        $models = $command->queryAll();

        return $this->render("/expretur/serahterimabbm", ['models' => $models, 'periode' => $periode]);
    }

    public function actionExceldet($id) {
        $model = $this->findModel($id);
        $detail = DetBbm::findAll(['no_bbm' => $id]);
//        Yii::error($detail);
        return $this->render('reportExcel', ['model' => $model, 'detail' => $detail]);
    }

}

?>
