<?php

namespace app\controllers;

use Yii;
use app\models\TransSpp;
use app\models\DetSpp;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SppnonrutinController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'cari' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'print' => ['get'],
                    'detail' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'listbarang' => ['get'],
                    'kode' => ['get'],
                ],
            ]
        ];
    }

    public function actionCari() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('trans_spp')
                ->select("no_spp,no_proyek")
                ->andWhere(['like', 'no_spp', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_spp')
                ->select("*")
                ->orderBy('no_spp DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = ($models['no_spp'] + 1);
        $kode = substr('00000' . $kode_mdl, strlen($kode_mdl));

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
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

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tgl_trans DESC";
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
                ->where("no_proyek='Non Rutin'")
                ->limit($limit)
                ->from('trans_spp')
                ->orderBy($sort)
                ->select("*");

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                $query->andFilterWhere(['like', 'trans_spp.' . $key, $val]);
            }
        }

        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $data = array();
        foreach ($models as $key => $val) {
            $tg1 = explode("/", $val['tgl1']);
            $tg2 = explode("/", $val['tgl2']);
            $tgl1 = $tg1[2] . '-' . $tg1[1] . '-' . $tg1[0];
            $tgl2 = $tg2[2] . '-' . $tg2[1] . '-' . $tg2[0];
            $data[$key] = $val;
            $data[$key]['tgl1'] = $tgl1;
            $data[$key]['tgl2'] = $tgl2;
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $query = new Query;
        $query->select("trans_spp.*, det_spp.*, barang.*, jenis_brg.*, det_spp.saldo as sld, det_spp.qty as jmlspp")
                ->from('trans_spp')
                ->join('JOIN', 'det_spp', 'det_spp.no_spp = trans_spp.no_spp')
                ->join('LEFT JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('LEFT JOIN', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
                ->where('trans_spp.no_spp = "' . $id . '"');

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['nospp'] = $id;
    }

//    public function actionCreate() {
//        $params = json_decode(file_get_contents("php://input"), true);
//        $model = new TransSpp();
//        $tgl_trans = date('Y-m-d', strtotime($params['form']['tgl_trans']));
//        $lastNumber = TransSpp::find()
//                ->where('year(tgl_trans)="' . $tgl_trans . '"')
//                ->orderBy('no_spp DESC')
//                ->one();
//        $number = (empty($lastNumber)) ? 1 : (int) substr($lastNumber->no_spp, 3) + 1;
//        $model->no_spp = date('y', $tgl_trans) . substr("000" . $number, -3);
//        $model->tgl_trans = $tgl_trans;
//        $model->tgl1 = date('d/m/Y', strtotime($params['form']['periode']['startDate']));
//        $model->tgl2 = date('d/m/Y', strtotime($params['form']['periode']['endDate']));
//        $model->no_proyek = 'Non Rutin';
//
//        if ($model->save()) {
//            foreach ($params['details'] as $val) {
//                foreach ($val['no_wo'] as $valWo) {
//                    $det = new DetSpp();
//                    $det->attributes = $val;
//                    $det->no_spp = $model->no_spp;
//                    $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
//                    $det->saldo = $val['barang']['saldo'];
//                    $det->p = date('Y-m-d', strtotime($det->p));
//                    $det->no_wo = (empty($valWo['no_wo'])) ? '-' : $valWo['no_wo'];
//                    $det->save();
//                    echo 'a';
//                    echo $valWo['no_wo'];
//                }
//                echo 'bb';
//            }
////            $this->setHeader(200);
////            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
//        } else {
////            $this->setHeader(400);
////            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
//        }
//    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = TransSpp::findOne($id);
        if (empty($model)) {
            $model = new TransSpp();
            $model->no_spp = $params['form']['no_spp'];
        }
        $model->attributes = $params;
        $tgl_trans = date('Y-m-d', strtotime($params['form']['tgl_trans']));
        $model->tgl_trans = $tgl_trans;
        $model->tgl1 = date('d/m/Y', strtotime($params['form']['periode']['startDate']));
        $model->tgl2 = date('d/m/Y', strtotime($params['form']['periode']['endDate']));
        $model->no_proyek = 'Non Rutin';
        if ($model->save()) {
            $deleteAll = DetSpp::deleteAll('no_spp="' . $model->no_spp . '"');
            foreach ($params['details'] as $val) {
                foreach ($val['no_wo'] as $valWo) {
                    $det = new DetSpp();
                    $det->attributes = $val;
                    $det->no_spp = $model->no_spp;
                    $det->kd_barang = (empty($val['barang']['kd_barang'])) ? '-' : $val['barang']['kd_barang'];
                    $det->saldo = $val['saldo'];
                    $det->p = isset($val['p']) ? date('Y-m-d', strtotime($val['p'])) : null;
                    $det->no_wo = (empty($valWo['no_wo'])) ? '-' : $valWo['no_wo'];
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
        $deleteDetail = DetSpp::deleteAll('no_spp="' . $id . '"');

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransSpp::findOne($id)) !== null) {
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
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excel", ['models' => $models]);
    }

    public function actionListbarang() {
        $query = new Query();
        $query->from('barang')
                ->select("kd_barang,nm_barang");

        //filter
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionDetail($id) {
        $detSpp = DetSpp::find()
                ->joinWith(['wo', 'barang'])
                ->where(['no_spp' => $id])
                ->select('det_spp.*, barang.nm_barang, barang.kd_barang, barang.satuan')
                ->all();
        $detail = array();
        $nowo = array();
        $woArr = array();
        $i = 0;
        foreach ($detSpp as $key => $val) {
            $kd_barang = isset($detSpp[$key + 1]['kd_barang']) ? $detSpp[$key + 1]['kd_barang'] : 0;
            $jml_barang = isset($detSpp[$key + 1]['qty']) ? $detSpp[$key + 1]['qty'] : 0;
            if ($kd_barang == $val['kd_barang'] and $jml_barang == $val['qty']) {
                $woArr[] = array('no_wo' => $val['no_wo']);
            } else {
                $woArr[] = array('no_wo' => $val['no_wo']);
                $detail[$i]['id'] = $val->id;
                $detail[$i]['kd_barang'] = $val->kd_barang;
                $detail[$i]['qty'] = $val->qty;
                $detail[$i]['ket'] = $val->ket;
                $detail[$i]['saldo'] = $val->saldo;
                $detail[$i]['p'] = $val->p;
                $detail[$i]['no_wo'] = $woArr;
                $detail[$i]['barang'] = array('kd_barang' => $val->barang->kd_barang, 'saldo' => $val->barang->saldo, 'nm_barang' => $val->barang->nm_barang, 'satuan' => $val->barang->satuan);
                $woArr = array();
                $i++;
            }
        }
        $this->setHeader(200);
        echo json_encode(['status' => 1, 'details' => $detail]);
    }

    public function actionPrint() {
        session_start();
        $query = $_SESSION['query'];
        $nospp = $_SESSION['nospp'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/laporansppnonrutin", ['models' => $models, 'id' => $nospp]);
    }

//    public function actionPrint() {
//        session_start();
//        $nospp = $_SESSION['nospp'];
//
//        $query = new Query;
//        $query->where("det_spp.no_spp='" . $nospp . "'")
//                ->from('det_spp')
//                ->join('JOIN', 'trans_spp', 'trans_spp.no_spp = det_spp.no_spp')
//                ->join('LEFT JOIN', 'trans_po', 'trans_po.spp = trans_spp.no_spp')
//                ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
//                ->join('LEFT JOIN', 'jenis_brg', 'jenis_brg.kd_jenis = barang.jenis')
//                ->select("det_spp.*,trans_spp.*,jenis_brg.jenis_brg,barang.min,barang.max,barang.nm_barang,barang.satuan,trans_po.nota");
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//        return $this->render("/expretur/laporansppnonrutin", ['models' => $models, 'id' => $nospp]);
//    }
}
