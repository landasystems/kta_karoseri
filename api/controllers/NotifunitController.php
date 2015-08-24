<?php

namespace app\controllers;

use Yii;
use app\models\Spkaroseri;
use app\models\Spk;
use app\models\Womasuk;
use app\models\Serahterimain;
use app\models\TransBbm;
use app\models\DetSpp;
use app\models\DetBbm;
use app\models\DetailPo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class NotifunitController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
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

    public function actionIndex($id) {
        //init variable
        $data = [];
        $no = 0;
        $a = date('Y-m-d', strtotime('-32 month', strtotime(date('Y-m-d'))));
        $woMasuk = WoMasuk::find()
                ->with(['spkaroseri', 'spkaroseri.chassis'])
                ->where('tgl_kontrak >="' . $a . '"')
//                ->limit(40)
                ->orderBy('tgl_kontrak DESC')
                ->all();
        if (!empty($woMasuk)) {
            //cari yang belum ada spek WO
            foreach ($woMasuk as $key => $val) {
                $chassis = (!empty($val->spkaroseri->chassis)) ? $val->spkaroseri->chassis->attributes : array();

                if (!empty($chassis['jenis']) and strtolower($chassis['jenis']) == 'mini bus') {
                    $miniInt = \app\models\Miniint::find()
                            ->where('no_wo="' . $val->no_wo . '"')
                            ->one();
                    $miniEks = \app\models\Minieks::find()
                            ->where('no_wo="' . $val->no_wo . '"')
                            ->one();
                    if ((empty($miniInt) or $miniInt->plavon = "-" or $miniInt->plavon = NULL ) AND ( empty($miniEks) or $miniEks->plat_body = "-" or $miniEks->plat_body = NULL )) {
                        $data[$no] = $val->attributes;
                        $data[$no]['nm_customer'] = (!empty($val->spkaroseri->customer->nm_customer)) ? $val->spkaroseri->customer->nm_customer : '-';
                        $data[$no]['status'] = 'Belum Ada Spesifikasi WO';
                    }
                } elseif (!empty($chassis['jenis']) and strtolower($chassis['jenis']) == 'small bus') {
                    $smallInt = \app\models\Smallint::find()
                            ->where('no_wo="' . $val->no_wo . '"')
                            ->one();
                    $smallEks = \app\models\Smalleks::find()
                            ->where('no_wo="' . $val->no_wo . '"')
                            ->one();
                    if ((empty($smallInt) or $smallInt->plavon = "-" or $smallInt->plavon = NULL ) AND ( empty($smallEks) or $smallEks->plat_body = "-" or $smallEks->plat_body = NULL )) {
                        $data[$no] = $val->attributes;
                        $data[$no]['nm_customer'] = (!empty($val->spkaroseri->customer->nm_customer)) ? $val->spkaroseri->customer->nm_customer : '-';
                        $data[$no]['status'] = 'Belum Ada Spesifikasi WO';
                    }
                }
                $no++;
            }

            //cari yang belum ada WIP
            foreach ($woMasuk as $key => $val) {
                $findWip = \app\models\Wip::find()
                        ->where('no_wo="' . $val->no_wo . '"')
                        ->one();
                if (empty($findWip)) {
                    $data[$no] = $val->attributes;
                    $data[$no]['nm_customer'] = (!empty($val->spkaroseri->customer->nm_customer)) ? $val->spkaroseri->customer->nm_customer : '-';
                    $data[$no]['status'] = 'Belum Ada WIP';
                }
                $no++;
            }

            //cari yang belum ada STI
            foreach ($woMasuk as $key => $val) {
                $findSti = Serahterimain::find()
                        ->where('kd_titipan="' . $val->kd_titipan . '"')
                        ->one();
                if (empty($findSti)) {
                    $data[$no] = $val->attributes;
                    $data[$no]['nm_customer'] = (!empty($val->spkaroseri->customer->nm_customer)) ? $val->spkaroseri->customer->nm_customer : '-';
                    $data[$no]['status'] = 'Belum Ada Serah Terima Internal';
                }
                $no++;
            }
        }
        //cari yg belum ada WO
        $query = new Query();
        $query->from('wo_masuk')
                ->join('RIGHT JOIN', 'spk', '`spk`.`no_spk` = `wo_masuk`.`no_spk`')
                ->join('lEFT JOIN', 'customer', '`spk`.`kd_customer` = `customer`.`kd_cust`')
//                ->where("no_proyek='Non Rutin'")
                ->orderBy('wo_masuk.no_spk ASC')
                ->limit(10)
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryAll();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $data[$no] = $val;
                $data[$no]['nm_customer'] = (!empty($val['nm_customer'])) ? $val['nm_customer'] : '-';
                $data[$no]['status'] = 'Belum Ada WO';
                $no++;
            }
        }
        $this->setHeader(200);
        if ($id) {
            return $this->render('excel', ['data' => $data]);
        } else {
            echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
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
