<?php

namespace app\controllers;

use Yii;
use app\models\Jabatan;
use app\models\AbsensiEmp;
use app\models\AbsensiEttLog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class JabatanController extends Controller {

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
                    'kode' => ['get'],
                    'listkaryawan' => ['get'],
                    'listkaryawanabsent' => ['get'],
                    'listkaryawanabsentjabatan' => ['get'],
                    'listkaryawansales' => ['get'],
                    'cari' => ['get'],
                    'cari2' => ['get'],
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

    public function actionCari2() {
        $params = $_REQUEST;
        $models = array();

        if (isset($params['no_wo']) and ! empty($params['no_wo']) and $params['kat_bbk'] == 'produksi') {

            $optional = \app\models\TransAdditionalBomWo::find()
                    ->joinWith('transadditionalbom')
                    ->where(['trans_additional_bom_wo.no_wo' => $params['no_wo']])
                    ->andWhere(['trans_additional_bom.status' => 1])
                    ->all();

            //jika tidak ada optional ambil dari trans_standar_bahan
            if (empty($optional) or count($optional) == 0) {
                //create query
                $query = new Query;

                $query->from('det_standar_bahan as dts')
                        ->join('JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                        ->join('JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
                        ->join('JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
                        ->join('JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
                        ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC')
                        ->groupBy('tjb.id_jabatan')
                        ->where('wm.no_wo = "' . $params['no_wo'] . '"')
                        ->andWhere('tjb.jabatan like "%' . $params['nama'] . '%"')
                        ->select("tjb.*");

                $command = $query->createCommand();
                $models = $command->queryAll();
            } else {
                //create query
                $query = new Query;

                $query->from('det_additional_bom as dts')
                        ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
                        ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dts.tran_additional_bom_id')
                        ->join('LEFT JOIN', 'trans_additional_bom_wo as tsbw', ' tsb.id = tsbw.tran_additional_bom_id')
                        ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC')
                        ->groupBy('tjb.id_jabatan')
                        ->where('tsbw.no_wo = "' . $params['no_wo'] . '"')
                        ->andWhere('tjb.jabatan like "%' . $params['nama'] . '%"')
                        ->select("tjb.*");

                $command = $query->createCommand();
                $models = $command->queryAll();
            }
        } else if ($params['kat_bbk'] == 'umum') {
            $query = new Query;

            $query->from('tbl_jabatan')
                    ->select("*")
                    ->orderBy('jabatan ASC')
                    ->where('jabatan like "%' . $params['nama'] . '%"')
                    ->orWhere(['like', 'id_jabatan', $params['nama']]);

            $command = $query->createCommand();
            $models = $command->queryAll();
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListkaryawanabsent() {
        $param = $_REQUEST;

        $abs = AbsensiEttLog::absen(date("Y-m-d"), date("Y-m-d"));

        $query = new Query;
        $query->select("tk.nik, tk.nama, tjb.jabatan")
                ->from("tbl_karyawan as tk")
                ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = tk.jabatan')
                ->where('nama like "%' . $param['nama'] . '%"')
                ->limit(20);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $data = array();
        foreach ($models as $val) {
            if (isset($abs[$val['nik']])) {
                $data[] = $val;
            }
        }

//        $absen = AbsensiEttLog::find()
//                ->joinWith('emp')
//                ->join('RIGHT JOIN', 'purchassing.tbl_karyawan', 'purchassing.tbl_karyawan.nik = emp.nik')
//                ->select("emp.first_name, emp.pin, date(scan_date) as scan_date")
//                ->groupBy("emp.pin") // nonaktifkan jika filter absen ingin diaktifkan
////                ->where('date(scan_date) = "' . date("Y-m-d") . '"')
//                ->andWhere('emp.first_name like "%' . $param['nama'] . '%" or emp.last_name like "%' . $param['nama'] . '%"')
//                ->andWhere('emp.nik != "-"')
//                ->limit(100)
//                ->all();
//        $query = new Query;
//        $query->select("tk.nik, tk.nama, tjb.jabatana")
//                ->from('purchassing.tbl_karyawan as tk')
//                ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = tk.jabatan')
//                ->join('JOIN', 'ftm.emp as emp', 'emp.nik = tk.nik')
//                ->join('JOIN', 'ftm.att_log as att_log', 'att_log.pin = emp.pin')
//                ->where('date(att_log.scan_date) = "' . date("Y-m-d") . '"')
//                ->andWhere('tk.nama like "%' . $param['nama'] . '%"')
//                ->groupBy('tk.nik')
//                ->limit(20);
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//        $data = array();
//        foreach ($absen as $key => $val) {
//            $data[$key]['nik'] = $val->emp->nik;
//            $data[$key]['nama'] = $val->emp->first_name . ' ' . $val->emp->last_name;
//        }
//        }

        echo json_encode(array('status' => 1, 'data' => $data));
    }

    public function actionListkaryawanabsentjabatan() {
        $param = $_REQUEST;

        if (isset($param['jabatan'])) {
//            $absen = AbsensiEttLog::find()
//                    ->joinWith('emp')
//                    ->join('LEFT JOIN', 'purchassing.tbl_karyawan', 'purchassing.tbl_karyawan.nik = emp.nik')
//                    ->select("emp.first_name, emp.pin, date(scan_date) as scan_date")
//                    ->where('date(scan_date) = "' . date("Y-m-d") . '"')
//                    ->andWhere('purchassing.tbl_karyawan.jabatan = "' . $param['jabatan'] . '"')
//                    ->limit(100)
//                    ->all();
//            $data = array();
//            foreach ($absen as $key => $val) {
//                $data[$key]['nik'] = $val->emp->nik;
//                $data[$key]['nama'] = $val->emp->first_name . ' ' . $val->emp->last_name;
//            }

            $query = new Query;
            $query->select("tk.nik, tk.nama, tjb.jabatan")
                    ->from('purchassing.tbl_karyawan as tk')
                    ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = tk.jabatan')
                    ->join('JOIN', 'ftm.emp as emp', 'emp.nik = tk.nik')
                    ->join('JOIN', 'ftm.att_log as att_log', 'att_log.pin = emp.pin')
                    ->where('date(att_log.scan_date) = "' . date("Y-m-d") . '"')
                    ->andWhere('tk.jabatan = "' . $param['jabatan'] . '"')
                    ->andWhere('tk.nama like "%' . $param['nama'] . '%"')
                    ->groupBy('tk.nik')
                    ->limit(20);

            $command = $query->createCommand();
            $models = $command->queryAll();

            echo json_encode(array('status' => 1, 'data' => $models));
        }
    }

    public function actionListkaryawan() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select("nik, nama")
                ->where('nama like "%' . $param['nama'] . '%"');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListkaryawansales() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select("nik, nama")
                ->where('nama like "%' . $param['nama'] . '%"')
                ->andWhere(['department' => 'DPRT014']);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCari() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jabatan')
                ->select("*")
                ->orderBy('jabatan ASC')
                ->where('jabatan like "%' . $param['nama'] . '%"')
                ->orWhere(['like', 'id_jabatan', $param['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('tbl_jabatan')
                ->select('*')
                ->orderBy('id_jabatan DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = (substr($models['id_jabatan'], -3) + 1);
        $kode = substr('000' . $kode_mdl, strlen($kode_mdl));
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => 'JBTN' . $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tbl_jabatan.id_jabatan DESC";
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
                ->limit($limit)
                ->from('tbl_jabatan')
                ->join('JOIN', 'pekerjaan', 'tbl_jabatan.krj = pekerjaan.kd_kerja')
                ->orderBy($sort)
                ->select("tbl_jabatan.*,pekerjaan.kerja");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $data = $model->attributes;
        $subsec = \app\models\SubSection::find()
                ->where(['kd_kerja' => $model['krj']])
                ->One();
        $kd_kerja = (isset($subsec->kd_kerja)) ? $subsec->kd_kerja : '';
        $kerja = (isset($subsec->kerja)) ? $subsec->kerja : '';
        $data['subSection'] = ['kd_kerja' => $kd_kerja, 'kerja' => $kerja];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Jabatan();
        $model->attributes = $params;
        $model->krj = $params['subSection']['kd_kerja'];


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
        $model->krj = $params['subSection']['kd_kerja'];

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

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Jabatan::findOne($id)) !== null) {
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
        return $this->render("/expmaster/jabatan", ['models' => $models]);
    }

}

?>
