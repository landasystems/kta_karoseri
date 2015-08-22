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
                    'excel' => ['get'],
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
                ],
            ]
        ];
    }

    public function actionCariall() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_standar_bahan')
                ->select("*")
                ->where(['like', 'kd_bom', $params['nama']])
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
                ->join('JOIN','chassis','trans_standar_bahan.kd_chassis = chassis.kd_chassis')
                ->select("trans_standar_bahan.*, chassis.merk")
                ->where(['like', 'kd_bom', $params['nama']])
                ->andWhere('status = 1')
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
            $kode = 'BOM' . date("y") . '00001';
        } else {
            $lastKode = substr($models['kd_bom'], -5) + 1;
            $kode = 'BOM' . date("y") . substr('0000' . $lastKode, -5);
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_standar_bahan.kd_bom ASC";
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
        $filter = array();
        $sort = "dts.kd_bom ASC";
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
                ->from('det_standar_bahan as dts')
                ->join('JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
                ->join('JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                ->join('JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
                ->join('JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
                ->join('JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
                ->orderBy($sort)
                ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tsb.tgl_buat') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'tsb.tgl_buat', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
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
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $query->limit(null);
        $query->offset(null);

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rekapbom", ['models' => $models, 'filter' => $filter]);
    }

    public function actionExceltrans() {
        session_start();
        $query = $_SESSION['bom'];
        $kd_bom = $_SESSION['kd_bom'];

        $query->limit(null);
        $query->offset(null);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $det = BomDet::find()
                ->with(['jabatan', 'barang'])
                ->where(['kd_bom' => $kd_bom])
                ->all();

        $detail = array();
        $i = 0;
        foreach ($det as $val) {
            $detail[$val->jabatan->id_jabatan]['nama_jabatan'] = isset($val->jabatan->jabatan) ? $val->jabatan->jabatan : '-';
            $detail[$val->jabatan->id_jabatan]['body'][$i]['nama_barang'] = isset($val->barang->nm_barang) ? $val->barang->nm_barang : '-';
            $detail[$val->jabatan->id_jabatan]['body'][$i]['satuan'] = isset($val->barang->satuan) ? $val->barang->satuan : '-';
            $detail[$val->jabatan->id_jabatan]['body'][$i]['jumlah'] = isset($val->qty) ? $val->qty : '-';
            $detail[$val->jabatan->id_jabatan]['body'][$i]['ket'] = isset($val->ket) ? $val->ket : '-';
            $i++;
        }
//        echo json_encode($models);
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

        $det = BomDet::find()
                ->with(['jabatan', 'barang'])
                ->where(['kd_bom' => $models['kd_bom']])
                ->all();

        session_start();
        $_SESSION['bom'] = $query;
        $_SESSION['kd_bom'] = $models['kd_bom'];

        $detail = array();
        foreach ($det as $key => $val) {
            $detail[$key] = $val->attributes;
            $detail[$key]['bagian'] = (isset($val->jabatan)) ? $val->jabatan->attributes : [];
            $detail[$key]['barang'] = (isset($val->barang)) ? $val->barang->attributes : [];
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Bom();
        $model->attributes = $params['bom'];
        $model->kd_model = $params['bom']['kd_model']['kd_model'];
        $model->status = 0;

        if ($model->save()) {
            $detailBom = $params['detailBom'];
            foreach ($detailBom as $val) {
                $det = new BomDet();
                $det->attributes = $val;
                $det->kd_jab = $val['bagian']['id_jabatan'];
                $det->kd_barang = $val['barang']['kd_barang'];
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
        $model->kd_model = $params['bom']['kd_model']['kd_model'];

        if ($model->save()) {
            $deleteDetail = BomDet::deleteAll(['kd_bom' => $model->kd_bom]);
            $detailBom = $params['detailBom'];
            foreach ($detailBom as $val) {
                $det = new BomDet();
                $det->attributes = $val;
                $det->kd_jab = $val['bagian']['id_jabatan'];
                $det->kd_barang = $val['barang']['kd_barang'];
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
