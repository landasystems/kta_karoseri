<?php
use yii\db\Query;
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=LAPORAN-BARANG-PER-31DESEMBER2015.xls");
}
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['kat']]['title']['kategory'] = $val['kat'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['title'] = $val['jenis_brg'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['jenis_brg'] = $val['jenis_brg'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['harga'] = $val['harga'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['max'] = $val['max'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['min'] = $val['min'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['saldo'] = $val['saldo'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['qty'] = $val['qty'];
    $i++;
}

$tanggal = "2015-12-31";

$jmlbbk = new Query;
$jmlbbk->from('trans_bbk')
        ->join('JOIN', 'det_bbk', 'det_bbk.no_bbk = trans_bbk.no_bbk')
        ->select('sum(det_bbk.jml) as jml_bbk, det_bbk.kd_barang as kd_barang')
        ->groupBy('det_bbk.kd_barang')
        ->where('trans_bbk.tanggal <= "' . $tanggal . '"');
$cJmlBbk = $jmlbbk->createCommand();
$mJmlBbk = $cJmlBbk->queryAll();

$bk = array();
foreach ($mJmlBbk as $val) {
    $bk[$val['kd_barang']] = $val['jml_bbk'];
}

$jmlbbm = new Query;
$jmlbbm->from('trans_bbm')
        ->join('JOIN', 'det_bbm', 'det_bbm.no_bbm = trans_bbm.no_bbm')
        ->select('sum(det_bbm.jumlah) as jml_bbm, det_bbm.kd_barang as kd_barang')
        ->groupBy('det_bbm.kd_barang')
        ->where('trans_bbm.tgl_nota <= "' . $tanggal . '"');
$cJmlBbm = $jmlbbm->createCommand();
$mJmlBbm = $cJmlBbm->queryAll();

$bm = array();
foreach ($mJmlBbm as $val) {
    $bm[$val['kd_barang']] = $val['jml_bbm'];
}

//print_r($bm);

$rbbk = new Query;
$rbbk->from('retur_bbk')
        ->groupBy('kd_barang')
        ->select('sum(jml) as jml_retur_bbk, kd_barang')
        ->where('tgl <= "' . $tanggal . '"');
$crbbk = $rbbk->createCommand();
$mrbbk = $crbbk->queryAll();

$rbk = array();
foreach ($mrbbk as $val) {
    $rbk[$val['kd_barang']] = $val['jml_retur_bbk'];
}

$rbbm = new Query;
$rbbm->from('retur_bbm')
        ->groupBy('kd_barang')
        ->select('sum(jml) as jml_retur_bbm, kd_barang')
        ->where('tgl <= "' . $tanggal . '"');
$crbbm = $rbbm->createCommand();
$mrbbm = $crbbm->queryAll();

$rbm = array();
foreach ($mrbbm as $val) {
    $rbm[$val['kd_barang']] = $val['jml_retur_bbm'];
}
?>
<div style="width:26cm">

    <center>
        <b style="margin:0px; padding:0px; font-size:16px;">LAPORAN DATA BARANG</b>
        <p style="margin:0px; padding:0px; font-size:12px;">Tanggal Cetak: <?php echo date("d M Y") ?></p><br>
    </center>

    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th align="center" style="font-size: 14px;">Kode Barang</th>
            <th align="center" style="font-size: 14px;">Nama barang</th>
            <th align="center" style="font-size: 14px;">Satuan</th>
            <th align="center" style="font-size: 14px;">Harga</th>
            <th align="center" style="font-size: 14px;">Min Stok</th>
            <th align="center" style="font-size: 14px;">Maks Stok</th>
            <th align="center" style="font-size: 14px;">Stok</th>
        </tr>
        <?php
        foreach ($data as $arr) {
            ?>
            <tr>
                <td  class="border-all back-grey">&nbsp;<b><?= $arr['title']['kategory'] ?></b></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
            <tr>
                <?php
                foreach ($arr['jenis_brg'] as $keys) {
                    ?>
                <tr>
                    <td accesskey=""class="border-right"></td>
                    <td class="border-right"><b><?= $keys['title'] ?></b></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>

                </tr>
                <?php
                foreach ($keys['body'] as $value) {
                    $bbk = isset($bk[$value['kd_barang']]) ? $bk[$value['kd_barang']] : 0;
                    $bbm = isset($bm[$value['kd_barang']]) ? $bm[$value['kd_barang']] : 0;
                    $rbbk = isset($rbk[$value['kd_barang']]) ? $rbk[$value['kd_barang']] : 0;
                    $rbbm = isset($rbm[$value['kd_barang']]) ? $rbm[$value['kd_barang']] : 0;
                    ?>
                    <tr>
                        <td class="border-right"><?= $value['kd_barang'] ?></td>
                        <td class="border-right"><?= $value['nm_barang'] ?></td>
                        <td class="border-right" align="center" ><?= $value['satuan'] ?>&nbsp;</td>
                        <td class="border-right" align="center" ><?= $value['harga'] ?></td>
                        <td class="border-right" align="center" ><?= $value['min'] ?></td>
                        <td class="border-right" align="center" ><?= $value['max'] ?></td>
                        <!--<td class="border-right" align="center" >&nbsp;</td>-->
                        <td class="border-right" align="center" ><?= (0 - $bbk + $bbm - $rbbm + $rbbk) ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>
</div>