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
                    'view' => ['get'],
                    'listsupplier' => ['get'],
                    'listspp' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['get'],
                    'cari' => ['get'],
                    'updtst' => ['get'],
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

    public function actionKode() {
        $query = new Query;
        $query->from('trans_po')
                ->select('*')
                ->orderBy('nota DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = (substr($models['nota'], -4) + 1);
        $kode = substr('0000' . $kode_mdl, strlen($kode_mdl));
        $kode_tahun = substr(date('Y'), -2);
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => 'PCH' . $kode_tahun . $kode));
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
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                $query->andFilterWhere(['like', 'trans_po.' . $key, $val]);
            }
        }


        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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

        $this->setHeader(200);
        echo json_encode(array('status' => 1,'data' => $data, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionUpdtst($id){
       $model = TransPo::findOne(['nota' => $id]);
       $model->status = 1;
       $model->save();
      
    }
    
    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TransPo();
        $model->attributes = $params['formpo'];
        $model->suplier = $params['formpo']['supplier']['kd_supplier'];
        $model->tanggal = date("Y-m-d", strtotime($params['formpo']['tanggal']));
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
                $det->tgl_pengiriman = date("Y-m-d", strtotime($val['data_barang']['tgl_pengiriman']));
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
        $model->attributes = $params['formPo'];
        $model->tanggal = date("Y-m-d", strtotime($params['formPo']['tanggal']));
        $model->spp = (empty($params['formpo']['listspp']['no_spp'])) ? '-' : $params['formpo']['listspp']['no_spp'];

        if ($model->save()) {
            $detailsr = $params['details'];
            foreach ($details as $val) {
                $det = new DetailPo();
                $det->attributes = $val;
                $det->kd_barang = $val['data_barang']['kd_barang'];
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
                ->select("*")
                ->where(['like', 'nota', $params['nama']])
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
