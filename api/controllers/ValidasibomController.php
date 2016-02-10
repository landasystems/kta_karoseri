<?php

namespace app\controllers;

use Yii;
use app\models\Validasibom;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ValidasibomController extends Controller {

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

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "trans_standar_bahan.tgl_buat DESC";
        $offset = 0;
        $limit = 20;
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
                ->from('trans_standar_bahan')
                ->join('LEFT JOIN', 'chassis', 'trans_standar_bahan.kd_chassis = chassis.kd_chassis')
                ->join('LEFT JOIN', 'model', 'trans_standar_bahan.kd_model=model.kd_model')
                ->orderBy($sort)
                ->where('trans_standar_bahan.status = 0')
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'tipe') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

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

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        Yii::error($params);
//        if (isset($params['bom'])) {
//            $status = Validasibom::findOne($params['bom']);
//            $status->status = 1;
//            $status->save();
//        } else {
            $centang = $params['kd_bom'];
            foreach ($centang as $key => $val) {
                $status = \app\models\Bom::find()->where('kd_bom="' . $key . '"')->one();
                $status->status = 1;
                $status->save();
            }
//        }
    }

    protected function findModel($id) {
        if (($model = Validasibom::findOne($id)) !== null) {
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

}

?>
