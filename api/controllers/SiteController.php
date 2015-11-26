<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\AbsensiEmp;
use app\models\AbsensiEttLog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    'logout' => ['get'],
                    'session' => ['get'],
                    'coba' => ['get'],
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

    public function actionSession() {
        session_start();
        echo json_encode(array('status' => 1, 'data' => array_filter($_SESSION)), JSON_PRETTY_PRINT);
    }

    public function actionLogout() {

        session_start();
        session_destroy();
    }

    public function actionCoba() {
        header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
//        $query = new Query;
//        $query->select("tk.nik, tk.nama, tjb.jabatan")
//                ->from('purchassing.tbl_karyawan as tk')
//                ->join('LEFT JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = tk.jabatan')
//                ->join('JOIN', 'ftm.emp as emp', 'emp.nik = tk.nik')
//                ->join('JOIN', 'ftm.att_log as att_log', 'att_log.pin = emp.pin')
////                    ->where('date(att_log.scan_date) = "' . date("Y-m-d") . '"')
//                ->andWhere('tk.jabatan = "JBTN002"')
//                ->groupBy('tk.nik')
//                ->limit(20);
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//
//        echo json_encode(array('status' => 1, 'data' => $models));
//        $query = new Query;
//        $query->select("tk.nik, tk.nama, tjb.jabatan")
//                ->from('purchassing.tbl_karyawan as tk')
//                ->join('LEFT JOIN','tbl_jabatan as tjb','tjb.id_jabatan = tk.jabatan')
//                ->join('JOIN', 'ftm.emp as emp', 'emp.nik = tk.nik')
//                ->join('JOIN', 'ftm.att_log as att_log', 'att_log.pin = emp.pin')
//                ->where('date(att_log.scan_date) = "' . date("Y-m-d") . '"');
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//        echo json_encode($models);
//        $query = new Query;
//        $query->from('det_standar_bahan as dts')
//                ->join('JOIN', 'barang as brg', 'dts.kd_barang = brg.kd_barang')
//                ->join('JOIN', 'tbl_jabatan as tjb', 'tjb.id_jabatan = dts.kd_jab')
//                ->join('JOIN', 'spk', 'spk.kd_bom = dts.kd_bom')
//                ->join('JOIN', 'wo_masuk as wm', 'wm.no_spk  = spk.no_spk')
//                ->join('JOIN', 'trans_standar_bahan as tsb', 'tsb.kd_bom  = spk.kd_bom')
//                ->orderBy('tjb.urutan_produksi ASC, tjb.jabatan ASC, brg.nm_barang ASC')
//                ->where('wm.no_wo = "NDP-215133" ')
//                ->select("brg.kd_barang, brg.nm_barang, brg.satuan, dts.ket, dts.qty, brg.harga, tjb.id_jabatan, tjb.jabatan, wm.no_wo");
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//
//        $data = array();
//        foreach ($models as $key => $val) {
//            $query2 = new Query;
//            $query2->from('tbl_karyawan')
//                    ->select('nama, nik')
//                    ->where('tbl_karyawan.jabatan = "' . $val['id_jabatan'] . '" and status = "Kerja"');
//            $command2 = $query2->createCommand();
//            $models2 = $command2->queryAll();
//
//            $arr = array();
//            foreach ($models2 as $vv) {
//                $arr[] = " (<b>" . $vv['nik'] . "</b>) " . $vv['nama'];
//            }
//
//            $karyawan[$val['id_jabatan']] = join($arr, ",");
//
//            $data[$val['id_jabatan']]['id_jabatan'] = $val['id_jabatan'];
//            $data[$val['id_jabatan']]['bagian'] = $val['jabatan'];
//            $data[$val['id_jabatan']]['karyawan'] = $karyawan;
//            $data[$val['id_jabatan']]['body'][$key]['barang'] = $val['nm_barang'];
//        }
//
//        foreach ($data as $val) {
//            echo '<b>' . $val['bagian'] . '</b><br>';
//            echo '<table>';
//            echo '<tr>';
//            echo '<td style="width: 100px;vertical-align:top"><b>KARYAWAN</b></td>';
//            echo '<td width=1 style="vertical-align:top">:</td>';
//            echo '<td>' . $karyawan[$val['id_jabatan']] . '</td>';
//            echo '</tr>';
//            echo '</table>';
//            echo '<b>BARANG</b><br>';
//            foreach ($val['body'] as $val2) {
//                echo ' -| ' . $val2['barang'] . '</br>';
//            }
//            echo '<hr>';
//        }
    }

    public function actionLogin() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = User::find()->where(['username' => $params['username'], 'password' => sha1($params['password'])])->one();

        if (!empty($model)) {
            session_start();
            $_SESSION['user']['id'] = $model->id;
            $_SESSION['user']['roles_id'] = $model->roles_id;
            $_SESSION['user']['username'] = $model->username;
            $_SESSION['user']['nama'] = $model->nama;
            $akses = (isset($model->roles->akses)) ? $model->roles->akses : '[]';
            $_SESSION['user']['akses'] = json_decode($akses);
            $_SESSION['user']['settings'] = json_decode($model->settings);

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($_SESSION)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => "Authentication Systems gagal, Username atau password Anda salah."), JSON_PRETTY_PRINT);
        }
    }

    private function setHeader($status) {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);

        header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
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
