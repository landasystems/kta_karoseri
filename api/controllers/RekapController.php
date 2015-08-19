<?php

namespace app\controllers;

use Yii;
use app\models\ReturBbk;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class RekapController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'rekapchassisin' => ['get'],
                    'excelchassisin' => ['get'],
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

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    
    public function actionRekapchassisin() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "sti.tgl_terima ASC";
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
                ->from('view_wo_spk as vws')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = vws.kd_chassis')
                ->join('JOIN', 'customer', 'chassis.kd_chassis = vws.kd_chassis')
                ->join('JOIN', 'serah_terima_in as sti', 'sti.no_spk = vws.no_spk')
                ->join('JOIN', 'spk', 'vws.no_spk = spk.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->where('tk.department="DPRT005"')
                
                ->orderBy($sort)
                ->select("sti.tgl_terima, tk.nama, customer.nm_customer, sti.kd_titipan, vws.no_wo, chassis.merk, chassis.tipe, sti.no_chassis,sti.no_mesin, spk.total_harga, spk.no_spk, chassis.jenis");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tanggal') 
                    {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'rb.tgl', $start, $end]);
                }elseif($key == 'no_retur_bbk'){
                    $query->andFilterWhere(['like', 'rb.'.$key, $val]);
                } elseif($key == 'no_bbk'){
                    $query->andFilterWhere(['like', 'tb.'.$key, $val]);
                } elseif($key == 'nm_barang'){
                    $query->andFilterWhere(['like', 'barang.'.$key, $val]);
                }
            }
        }
        Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

//        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }
     public function actionExcelchassisin() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/chassisin", ['models' => $models,'filter'=>$filter]);
    }


}

?>
