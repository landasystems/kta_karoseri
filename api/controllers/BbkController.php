<?php

namespace app\controllers;

use Yii;
use app\models\TransBbk;
use app\models\DetBbk;
use app\models\Barang;
use app\models\AutentikasiBbk;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BbkController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'excelbk' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['get'],
                    'listbbk' => ['get'],
                    'detailstok' => ['post'],
                    'rekap' => ['get'],
                    'listbarang' => ['post'],
                    'print' => ['get'],
                    'pengecualian' => ['post'],
                    'bukaprint' => ['post'],
                    'riwayatambil' => ['post'],
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
            $status = TransBbk::findOne($key);
            $status->lock = 1;
            $status->save();
        }
    }

    public function actionUnlock() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['id'];

        foreach ($centang as $key => $val) {
            $status = TransBbk::findOne($key);
            $status->lock = 0;
            $status->save();
        }
    }

    public function actionRiwayatambil() {
        $params = json_decode(file_get_contents("php://input"), true);
        if (!empty($params['no_wo']) and ! empty($params['kd_jab']) and $params['no_wo']['no_wo'] != '-') {
            $query = new Query;
            $query->from('trans_bbk')
                    ->join('LEFT JOIN', 'det_bbk', 'det_bbk.no_bbk = trans_bbk.no_bbk')
                    ->join('LEFT JOIN', 'barang', 'barang.kd_barang = det_bbk.kd_barang')
                    ->join('LEFT JOIN', 'tbl_jabatan', 'tbl_jabatan.id_jabatan = trans_bbk.kd_jab')
                    ->join('LEFT JOIN', 'tbl_karyawan', 'tbl_karyawan.nik = trans_bbk.penerima')
                    ->select('tbl_karyawan.nama as karyawan, trans_bbk.tanggal,tbl_jabatan.jabatan,barang.nm_barang, det_bbk.jml, barang.satuan')
                    ->orderBy('trans_bbk.tanggal DESC')
                    ->where('trans_bbk.no_wo = "' . $params['no_wo']['no_wo'] . '" and trans_bbk.kd_jab = "' . $params['kd_jab']['id_jabatan'] . '"');
            $command = $query->createCommand();
            $models = $command->queryAll();

            echo json_encode(array('status' => 1, 'data' => $models));
        }
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

    public function actionBukaprint() {
        $params = json_decode(file_get_contents("php://input"), true);
        $centang = $params['no_bbk'];

        foreach ($centang as $key => $val) {
            $status = TransBbk::findOne($key);
            $status->status = 0;
            $status->save();
        }
    }

    public function actionPengecualian() {
        $params = json_decode(file_get_contents("php://input"), true);
        if (isset($params['no_wo'])) {
            $optional = \app\models\TransAdditionalBomWo::find()
                    ->joinWith('transadditionalbom')
                    ->where(['trans_additional_bom_wo.no_wo' => $params['no_wo']['no_wo']])
                    ->andWhere(['trans_additional_bom.status' => 1])
                    ->all();

            //jika tidak ada optional
            if (empty($optional) or count($optional) == 0) {
                $query = new Query;
                $query->from('det_standar_bahan as dsb')
//                        ->join('LEFT JOIN', 'barang as b', 'b.kd_barang = dsb.kd_barang')
                        ->join('LEFT JOIN', 'tbl_jabatan as tj', 'tj.id_jabatan = dsb.kd_jab')
                        ->join('LEFT JOIN', 'spk', 'spk.kd_bom = dsb.kd_bom')
                        ->join('LEFT JOIN', 'wo_masuk as wm', 'spk.no_spk = wm.no_spk')
                        ->select('tj.id_jabatan as kd_jabatan, '
                                . 'tj.jabatan as bagian, dsb.qty as jml, dsb.ket as ket')
                        ->where('dsb.kd_barang = "' . $params['kd_barang']['kd_barang'] . '" and wm.no_wo = "' . $params['no_wo']['no_wo'] . '" and tj.id_jabatan = "' . $params['kd_kerja']['id_jabatan'] . '"');
            } else {
                $query = new Query;
                $query->from('det_additional_bom as dsb')
//                        ->join('LEFT JOIN', 'barang as b', 'dsb.kd_barang = b.kd_barang')
                        ->join('LEFT JOIN', 'tbl_jabatan as tj', 'tj.id_jabatan = dsb.kd_jab')
                        ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dsb.tran_additional_bom_id')
                        ->join('LEFT JOIN', 'trans_additional_bom_wo as tsbw', ' tsb.id = tsbw.tran_additional_bom_id')
                        ->select('tj.id_jabatan as kd_jabatan, '
                                . 'tj.jabatan as bagian, dsb.qty as jml, dsb.ket as ket')
                        ->where('dsb.kd_barang = "' . $params['kd_barang']['kd_barang'] . '" and tsbw.no_wo = "' . $params['no_wo']['no_wo'] . '" and tj.id_jabatan = "' . $params['kd_kerja']['id_jabatan'] . '"');
            }
            $command = $query->createCommand();
            $models = $command->query()->read();
            $jml = $models['jml'];

            $persen = ($params['jml'] / $jml) * 100;

            $roles_id = 1;
            if ($persen <= 5) {
                $roles_id = 2;
            } else if ($persen > 5 && $persen <= 10) {
                $roles_id = 3;
            } else if ($persen > 10) {
                $roles_id = 4;
            }

            $model = new AutentikasiBbk;
            $model->attributes = $params;
            $model->no_wo = $params['no_wo']['no_wo'];
            $model->kd_barang = $params['kd_barang']['kd_barang'];
            $model->kd_kerja = $params['kd_kerja']['id_jabatan'];
            $model->standard = $jml;
            $model->status = 0;
            $model->m_roles_id = $roles_id;

            if ($model->save()) {
                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        }
    }

    public function actionPrint() {
        $params = json_decode(file_get_contents("php://input"), true);
        $update = TransBbk::findOne($_GET['no_bbk']);
        $update->status = 1;
        $update->save();
    }

    public function actionListbarang() {
        $params = json_decode(file_get_contents("php://input"), true);

        if (!empty($params['no_wo']) and ! empty($params['kd_jab'])) {
            //cek optional bom            

            $kdBrg = array();
            if (isset($params['listBarang'])) {
                foreach ($params['listBarang'] as $val) {
                    $kdBrg[] = isset($val['kd_barang']['kd_barang']) ? $val['kd_barang']['kd_barang'] : '';
                }
            }

            $optional = \app\models\TransAdditionalBomWo::find()
                    ->joinWith('transadditionalbom')
                    ->where(['trans_additional_bom_wo.no_wo' => $params['no_wo']['no_wo']])
                    ->andWhere(['trans_additional_bom.status' => 1])
                    ->all();

            //jika tidak ada optional
            if (empty($optional) or count($optional) == 0) {
                $query = new Query;
                $query->from('det_standar_bahan as dsb')
                        ->join('LEFT JOIN', 'barang as b', 'b.kd_barang = dsb.kd_barang')
                        ->join('LEFT JOIN', 'tbl_jabatan as tj', 'tj.id_jabatan = dsb.kd_jab')
                        ->join('LEFT JOIN', 'spk', 'spk.kd_bom = dsb.kd_bom')
                        ->join('LEFT JOIN', 'wo_masuk as wm', 'spk.no_spk = wm.no_spk')
                        ->select('b.saldo as stok, wm.no_wo as no_wo, b.kd_barang as kd_barang, '
                                . 'b.nm_barang as nm_barang, b.satuan, tj.id_jabatan as kd_jabatan, '
                                . 'tj.jabatan as bagian, dsb.qty as jml, dsb.ket as ket')
                        ->where('(b.nm_barang like "%' . $params['nama'] . '%" or b.kd_barang like "%' . $params['nama'] . '%" ) and wm.no_wo = "' . $params['no_wo']['no_wo'] . '" and tj.id_jabatan = "' . $params['kd_jab']['id_jabatan'] . '"');
            } else {
                $query = new Query;
                $query->from('det_additional_bom as dsb')
                        ->join('LEFT JOIN', 'barang as b', 'dsb.kd_barang = b.kd_barang')
                        ->join('LEFT JOIN', 'tbl_jabatan as tj', 'tj.id_jabatan = dsb.kd_jab')
                        ->join('LEFT JOIN', 'trans_additional_bom as tsb', 'tsb.id  = dsb.tran_additional_bom_id')
                        ->join('LEFT JOIN', 'trans_additional_bom_wo as tsbw', ' tsb.id = tsbw.tran_additional_bom_id')
                        ->select('b.saldo as stok, tsbw.no_wo as no_wo, b.kd_barang as kd_barang, '
                                . 'b.nm_barang as nm_barang, b.satuan, tj.id_jabatan as kd_jabatan, '
                                . 'tj.jabatan as bagian, dsb.qty as jml, dsb.ket as ket')
                        ->where('(b.nm_barang like "%' . $params['nama'] . '%" or b.kd_barang like "%' . $params['nama'] . '%" ) and tsbw.no_wo = "' . $params['no_wo']['no_wo'] . '" and tj.id_jabatan = "' . $params['kd_jab']['id_jabatan'] . '"');
            }

            $query->andWhere(['NOT IN', 'b.kd_barang', $kdBrg]);

            $command = $query->createCommand();
            $models = $command->queryAll();

            $queryBbk = new Query;
            $queryBbk->from('trans_bbk as tb')
                    ->join('JOIN', 'det_bbk as db', 'tb.no_bbk = db.no_bbk')
                    ->select('db.kd_barang, db.jml')
                    ->where('tb.no_wo = "' . $params['no_wo']['no_wo'] . '" and tb.kd_jab = "' . $params['kd_jab']['id_jabatan'] . '"');

            $commandBbk = $queryBbk->createCommand();
            $modelsBbk = $commandBbk->queryAll();

            $detBbk = array();
            foreach ($modelsBbk as $valBbk) {
                $detBbk[$valBbk['kd_barang']]['jml_keluar'] = isset($detBbk[$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
            }

            $queryPengecualian = new Query;
            $queryPengecualian->from('autentikasi_bbk as ab')
                    ->select('ab.jml, ab.kd_barang')
                    ->where('ab.no_wo = "' . $params['no_wo']['no_wo'] . '" '
                            . 'and ab.kd_kerja = "' . $params['kd_jab']['id_jabatan'] . '"'
                            . 'and ab.status = 1');

            $commandPengecualian = $queryPengecualian->createCommand();
            $modelsPengecualian = $commandPengecualian->queryAll();

            $detPengecualian = array();
            foreach ($modelsPengecualian as $valPengecualian) {
                $detPengecualian[$valPengecualian['kd_barang']]['jml'] = $valPengecualian['jml'];
            }

            $det = array();
            $i = 0;
            foreach ($models as $val) {
                $det[$i]['kd_barang'] = $val['kd_barang'];
                $det[$i]['satuan'] = $val['satuan'];
                $det[$i]['nm_barang'] = $val['nm_barang'];
                if (isset($detPengecualian[$val['kd_barang']])) {
                    $val['jml'] += $detPengecualian[$val['kd_barang']]['jml'];
                }
                $det[$i]['stok_sekarang'] = $val['stok'];
                $det[$i]['sisa_pengambilan'] = isset($detBbk[$val['kd_barang']]['jml_keluar']) ? $val['jml'] - $detBbk[$val['kd_barang']]['jml_keluar'] : $val['jml'];
                $i++;
            }

            echo json_encode(array('status' => 1, 'data' => $det));
//            echo '1';
        } else {
//            echo '2';
            $query = new Query;
            $query->from('barang')
                    ->select("*")
                    ->orderBy('nm_barang ASC')
                    ->where('nm_barang like "%' . $params['nama'] . '%" or kd_barang like "%' . $params['nama'] . '%" ');

            $command = $query->createCommand();
            $models = $command->queryAll();

            $det = array();
            foreach ($models as $key => $val) {
                $det[$key]['kd_barang'] = $val['kd_barang'];
                $det[$key]['satuan'] = $val['satuan'];
                $det[$key]['nm_barang'] = $val['nm_barang'];
                $det[$key]['stok_sekarang'] = $val['saldo'];
                $det[$key]['sisa_pengambilan'] = 0;
            }

            $this->setHeader(200);

            echo json_encode(array('status' => 1, 'data' => $det));
        }
    }

    public function actionKode() {
        $query = new Query;
        $query->from('trans_bbk')
                ->select('*')
                ->orderBy('no_bbk DESC')
                ->limit(1);
        $command = $query->createCommand();
        $models = $command->query()->read();

        $cek = TransBbk::findOne('no_bbk = "BK' . date("y") . '00001"');
        if (empty($cek)) {
            $urut = substr($models['no_bbk'], -5) + 1;
            $kode = substr('00000' . $urut, strlen($urut));
            $kode = "BK" . date("y") . $kode;
        } else {
            $kode = "BK" . date("y") . "00001";
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionDetailstok() {
        $params = json_decode(file_get_contents("php://input"), true);
        $sisa_pengambilan = 0;
        $stok_sekarang = 0;
        Yii::error($params);

        if (!empty($params['kd_barang'])) {
            $stok = Barang::find()->where('kd_barang="' . $params['kd_barang'] . '"')->one();
            $stok_sekarang = $stok->saldo;

            if (!empty($params['no_wo'])) {
                // mencari jumlah barang dari bom
                $query = new Query;
                $query->from('view_bom_wo as vbw, det_standar_bahan as dsb')
                        ->select("sum(dsb.qty) as jml")
                        ->where('vbw.kd_bom = dsb.kd_bom and dsb.kd_barang = "' . $params['kd_barang'] . '" and vbw.no_wo = "' . $params['no_wo']['no_wo'] . '"');
                $command = $query->createCommand();
                $stokBom = $command->query()->read();

                //mencari jumlah barang yang telah diambil
                $query = new Query;
                $query->from('det_bbk as db, trans_bbk as tb')
                        ->select("sum(db.jml) as jml_keluar")
                        ->where('db.no_bbk = tb.no_bbk and db.kd_barang = "' . $params['kd_barang'] . '" and tb.no_wo = "' . $params['no_wo']['no_wo'] . '"');
                $command = $query->createCommand();
                $stokKeluar = $command->query()->read();

                $sisa_pengambilan = $stokBom['jml'] - $stokKeluar['jml_keluar'];
            }
        }
        $data['sisa_pengambilan'] = $sisa_pengambilan;
        $data['stok_sekarang'] = $stok_sekarang;
        echo json_encode(array('data' => $data));
    }

    public function actionListbbk() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('trans_bbk')
                ->select("no_bbk")
                ->where('no_bbk like "%' . $param['nama'] . '%"')
                ->orderBy('no_bbk DESC')
                ->limit(15);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_bbk DESC";
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
                ->from('trans_bbk as tb')
                ->leftJoin('tbl_karyawan as tk', 'tb.penerima = tk.nik')
                ->leftJoin('tbl_jabatan as tj', 'tj.id_jabatan  = tb.kd_jab')
                ->orderBy($sort)
                ->where('tb.kd_jab != "-" and (tb.penerima is not null or tb.penerima != "-")')
                ->select("tb.*, tk.nama as penerima, tj.jabatan as bagian");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'barang') {
                    $brg = $this->searchBrg($val);
                    foreach ($brg as $brg_val) {
                        $query->orFilterWhere(['=', 'tb.no_bbk', $brg_val]);
                    }
                } else if ($key == 'keterangan') {
                    $brg = $this->searchKet($val);
                    foreach ($brg as $brg_val) {
                        $query->orFilterWhere(['=', 'tb.no_bbk', $brg_val]);
                    }
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function searchKet($id) {
        $patern = $id;
        $query = new Query;
        $query->from('det_bbk')
                ->select("*")
                ->andFilterWhere(['like', 'det_bbk.ket', $patern]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $data = array();
        foreach ($models as $key) {
            $data[] = $key['no_bbk'];
        }

        $this->setHeader(200);
        return $data;
    }

    public function searchBrg($id) {
        $patern = $id;
        $query = new Query;
        $query->from('det_bbk')
                ->join('JOIN', 'barang', 'barang.kd_barang = det_bbk.kd_barang')
                ->select('det_bbk.no_bbk, barang.nm_barang');
        $query->andFilterWhere(['like', 'barang.nm_barang', $patern]);
        $query->orFilterWhere(['=', 'barang.kd_barang', $patern]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $data = array();
        foreach ($models as $key) {
            $data[] = $key['no_bbk'];
        }

        $this->setHeader(200);
        return $data;
    }

    public function actionView($id) {
        $model = TransBbk::find()
                        ->leftJoin('tbl_karyawan as tk', 'trans_bbk.penerima = tk.nik')
                        ->leftJoin('tbl_jabatan as tj', 'tj.id_jabatan  = trans_bbk.kd_jab')
                        ->select("trans_bbk.*, tk.nik, tk.nama")
//                        ->with(['penerima', 'bagian'])
                        ->where('no_bbk="' . $id . '"')->one();

        $query = new Query;
        $query->from('tbl_karyawan')
                ->select('nik, nama')
                ->where('nik = "' . $model->penerima . '"')
                ->limit(1);

        $command = $query->createCommand();
        $penerima = $command->query()->read();

        $model->kd_jab = array('id_jabatan' => isset($model->bagian->id_jabatan) ? $model->bagian->id_jabatan : '-', 'jabatan' => isset($model->bagian->jabatan) ? $model->bagian->jabatan : '-');
//        $model->penerima = array('nik' => isset($model->nik) ? $model->nik : '-', 'nama' => isset($model->nama) ? $model->nama : '-');
        $model->penerima = $penerima;
        $model->no_wo = array('no_wo' => $model->no_wo);

        $detail = DetBbk::find()
                        ->joinWith('barang')
                        ->where('no_bbk="' . $model->no_bbk . '"')->all();

        $i = 0;
        $det = array();
        foreach ($detail as $val) {
            $kd_barang = '';
            $nm_barang = '';
            $satuan = '';

            if (isset($val->barang->kd_barang)) {
                $kd_barang = $val->barang->kd_barang;
                $nm_barang = $val->barang->nm_barang;
                $satuan = $val->barang->satuan;
            }

            $det[$i]['jml'] = $val['jml'];
            $det[$i]['satuan'] = $satuan;
            $det[$i]['ket'] = $val['ket'];
            $det[$i]['kd_barang'] = array('kd_barang' => $kd_barang, 'nm_barang' => $nm_barang, 'satuan' => $satuan);
            $i++;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $det), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TransBbk();
        $model->attributes = $params['bbk'];
        if ($model->validate()) {
            $model->tanggal = date("Y-m-d", strtotime($params['bbk']['tanggal']));
            $model->status = 0;
            $model->no_wo = isset($params['bbk']['no_wo']['no_wo']) ? $params['bbk']['no_wo']['no_wo'] : '-';
            $model->kd_jab = isset($params['bbk']['kd_jab']['id_jabatan']) ? $params['bbk']['kd_jab']['id_jabatan'] : '-';
            $model->penerima = isset($params['bbk']['penerima']['nik']) ? $params['bbk']['penerima']['nik'] : '-';
            $model->lock = 1;
            if ($model->save()) {
                $detailBbk = $params['detailBbk'];
                foreach ($detailBbk as $val) {
                    if (isset($val['kd_barang']['kd_barang']) and ( $val['jml'] > 0 or ! empty($val['jml']))) {
                        $det = new DetBbk();
                        $det->attributes = $val;
                        $det->kd_barang = $val['kd_barang']['kd_barang'];
                        $det->no_bbk = $model->no_bbk;
                        $det->save();

                        //update stok barang
                        $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                        $barang->saldo -= $det->jml;
                        $barang->save();
                    }
                }

                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        } else {
//          $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = TransBbk::find()->where('no_bbk="' . $id . '"')->one();
        $model->attributes = $params['bbk'];
        $model->tanggal = date("Y-m-d", strtotime($params['bbk']['tanggal']));
        $model->status = 0;
        $model->no_wo = isset($params['bbk']['no_wo']['no_wo']) ? $params['bbk']['no_wo']['no_wo'] : '-';
        $model->kd_jab = isset($params['bbk']['kd_jab']['id_jabatan']) ? $params['bbk']['kd_jab']['id_jabatan'] : '-';
        $model->penerima = isset($params['bbk']['penerima']['nik']) ? $params['bbk']['penerima']['nik'] : '-';
        $model->lock = 1;
        if ($model->save()) {
            // mengembalikan stok barang
            $detail = DetBbk::find()->where('no_bbk = "' . $model->no_bbk . '"')->all();
            foreach ($detail as $detbbk) {
                $barang = Barang::find()->where('kd_barang="' . $detbbk->kd_barang . '"')->one();
                $barang->saldo += $detbbk->jml;
                $barang->save();

                //hapus detail bbk
                $detbbk->delete();
            }
//            $del = DetBbk::deleteAll('no_bbk = "' . $model->no_bbk . '"');
            //isi detail dengan yang baru
            $detailBbk = $params['detailBbk'];
            foreach ($detailBbk as $val) {
                if (isset($val['kd_barang']['kd_barang'])) {
                    $det = new DetBbk();
                    $det->attributes = $val;
                    $det->kd_barang = $val['kd_barang']['kd_barang'];
                    $det->no_bbk = $model->no_bbk;
                    $det->save();

                    //update stok barang
                    $barang = Barang::find()->where('kd_barang="' . $det->kd_barang . '"')->one();
                    $barang->saldo -= $det->jml;
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

    public function actionDelete($id) {
        $model = TransBbk::find()->where('no_bbk="' . $id . '"')->one();
        if ($model->delete()) {
            // mengembalikan stok barang
            $detail = DetBbk::find()->where('no_bbk = "' . $model->no_bbk . '"')->all();
            foreach ($detail as $detbbk) {
                $barang = Barang::find()->where('kd_barang="' . $detbbk->kd_barang . '"')->one();
                $barang->saldo += $detbbk->jml;
                $barang->save();
            }

            //hapus detail bbk
            $delBbk = DetBbk::deleteAll('no_bbk = "' . $id . '"');

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TransBbk::findOne($id)) !== null) {
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

    public function actionRekap() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "rvb.tanggal DESC";
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
                ->from('view_bbk_rekap as rvb')
                ->join('LEFT JOIN', 'tbl_karyawan as tbk', 'tbk.nik = rvb.penerima')
                ->join('LEFT JOIN', 'trans_bbk as trbk', 'trbk.no_bbk = rvb.no_bbk')
                ->join('LEFT JOIN', 'tbl_jabatan as tbj', 'tbj.id_jabatan = trbk.kd_jab')
                ->orderBy($sort)
                ->select("rvb.*,tbk.nama,tbj.jabatan");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tgl_periode') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'rvb.tanggal', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
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

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }


    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/rbbk", ['models' => $models, 'filter' => $filter]);
    }

    public function actionExcelbk() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/laporanbk", ['models' => $models, 'filter' => $filter]);
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
