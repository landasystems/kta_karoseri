<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class MonitoringController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'excel' => ['get'],
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

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_spp.no_spp DESC";
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
                ->from('trans_spp')
                ->join('JOIN', 'det_spp', 'trans_spp.no_spp = det_spp.no_spp')
                ->join('JOIN', 'barang', 'barang.kd_barang = det_spp.kd_barang')
                ->join('LEFT JOIN', 'view_wo_spk', 'view_wo_spk.no_wo = det_spp.no_wo')
                ->orderBy($sort)
                ->select("trans_spp.*, det_spp.*, barang.nm_barang, view_wo_spk.nm_customer");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            if (isset($filter['tanggal'])) {
                $value = explode(' - ', $filter['tanggal']);
                $start = date("Y-m-d", strtotime($value[0]));
                $end = date("Y-m-d", strtotime($value[1]));
                $query->andFilterWhere(['between', 'trans_spp.tgl_trans', $start, $end]);
            }
            if (isset($filter['kategori'])) {
                $query->andFilterWhere(['=', 'trans_spp.no_proyek', $filter['kategori']]);
            }
        }

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['periode'] = isset($filter['tanggal']) ? $filter['tanggal'] : '-';

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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