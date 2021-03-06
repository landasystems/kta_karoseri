<?php
if (!isset($_GET['printlap'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-laporan-spp-non-rutin.xls");
}else{
    echo '<link rel="stylesheet" href="../../../css/print.css" type="text/css" />';
}
$data = array();
$nowo = array();
$jml = 0;
foreach ($models as $key => $val) {

    $kd_barang = isset($models[$key + 1]['kd_barang']) ? $models[$key + 1]['kd_barang'] : 0;
    $jml_barang = isset($models[$key + 1]['jmlspp']) ? $models[$key + 1]['jmlspp'] : 0;

    if ($kd_barang == $val['kd_barang'] and $jml_barang == $val['jmlspp']) {
        $nowo[] = $val['no_wo'];
        $jml += $val['jmlspp'];
    } else {
        $nowo[] = $val['no_wo'];
        $jml += $val['jmlspp'];
        $wo = join(", ", $nowo);

        $data[$val['jenis_brg']]['title'] = $val['jenis_brg'];
        $data[$val['jenis_brg']]['body'][$key]['kd_barang'] = $val['kd_barang'];
        $data[$val['jenis_brg']]['body'][$key]['nm_barang'] = $val['nm_barang'];
        $data[$val['jenis_brg']]['body'][$key]['satuan'] = $val['satuan'];
        $data[$val['jenis_brg']]['body'][$key]['min'] = $val['min'];
        $data[$val['jenis_brg']]['body'][$key]['max'] = $val['max'];
        $data[$val['jenis_brg']]['body'][$key]['saldo'] = $val['sld'];
        $data[$val['jenis_brg']]['body'][$key]['qty'] = $jml;
        $data[$val['jenis_brg']]['body'][$key]['ket'] = ((empty($val['ket']) || $val['ket'] == '-') ? '' : $val['ket'] . ', ') . $wo;
//        $data[$val['jenis_brg']]['body'][$key]['keterangan'] = $val['ket'];
        $data[$val['jenis_brg']]['body'][$key]['p'] = $val['p'];
        $data[$val['jenis_brg']]['body'][$key]['a'] = $val['a'];
        $nowo = array();
        $jml = 0;
    }
}
?>
<!--<style>
    @media print{
        @page {
            size: portrait;
            margin: 25px;
        }
    }
</style>-->
<div style="width:100%">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 13px;" width="100%">
        <tr>
            <td class="border-right border-bottom"  rowspan="4" colspan="2" align="center" width="30%">
                <br>
                <br>
                <p><b style="margin: 2px;font-size:14px;">SURAT PERINTAH PEMBELIAN</b></p>
                <h3 style="margin: 2px;" style="font-size:15px;">NON RUTIN</h3>
                <p style="font-size:11px;">No Dokument :  FR-INV-014 Rev.01</p>
                <br><br>
            </td>
            <td class="border-right border-bottom"  rowspan="4" colspan="3" valign="top">
                <table style="font-size:11px;">
                    <tr>
                        <td>No. SPP</td>
                        <td> : <?= $id ?></td>

                    </tr>
                    <tr>
                        <td>Periode</td>

                        <td> : 
                            <?php
                            $periode = \app\models\TransSpp::findOne(['no_spp' => $id]);
                            $tg1 = explode("/", $periode['tgl1']);
                            $tg2 = explode("/", $periode['tgl2']);
                            $tgl1 = $tg1[2] . '-' . $tg1[1] . '-' . $tg1[0];
                            $tgl2 = $tg2[2] . '-' . $tg2[1] . '-' . $tg2[0];
                            echo date("d/m/Y", strtotime($tgl1)) . " - " . date("d/m/Y", strtotime($tgl2));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Cetak</td>

                        <td> : <?= date('d-M-Y') ?></td>
                    </tr>

                </table>
            </td>
            <td class="border-right border-bottom" style="text-align: center;width:10%;">Dibuat Oleh</td>
            <td class="border-right border-bottom" style="text-align: center;width:10%;">Diperiksa Oleh</td>
            <td class="border-right border-bottom" style="text-align: center;width:10%;">Diketahui Oleh</td>
            <td class="border-right border-bottom" style="text-align: center;width:10%;">Diterima Oleh</td>
        </tr>
        <tr>
            <td class="border-right border-bottom" style="text-align: center;" rowspan="2"></td>
            <td class="border-right border-bottom" style="text-align: center;" rowspan="2"></td>
            <td class="border-right border-bottom" style="text-align: center;"rowspan="2"></td>
            <td class="border-right border-bottom" style="text-align: center;"rowspan="2"></td>
        </tr>
        <tr></tr>
        <tr>
            <td class="border-right border-bottom" style="text-align: center;">Inventory</td>
            <td class="border-right border-bottom" style="text-align: center;">ADH</td>
            <td class="border-right border-bottom" style="text-align: center;">Finance</td>
            <td class="border-right border-bottom" style="text-align: center;">Purchasing</td>
        </tr>
    </table>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 10px; margin-top:-2px;" width='100%'>
        <tr>
            <th class="border-right border-bottom" style="text-align: center; width: 15px;" rowspan="2">NO</th>
            <th class="border-right border-bottom" style="text-align: center; width: 50px;" rowspan="2">KODE</th>
            <th class="border-right border-bottom" style="text-align: center;" rowspan="2">URAIAN</th>
            <th class="border-right border-bottom" style="text-align: center;" rowspan="2" width="25">SAT</th>
            <th class="border-right border-bottom" style="text-align: center;" rowspan="2" width="35">SALDO</th>
            <th class="border-right border-bottom" style="text-align: center;" rowspan="2" width="25">QTY</th>
            <th class="border-right border-bottom" style="text-align: center;" rowspan="2">KETERANGAN</th>
            <th class="border-right border-bottom" style="text-align: center;" colspan="2">SCHEDULLE</th>
        </tr>
        <tr>
            <th class="border-right border-bottom" style="text-align: center;">P</th>
            <th class="border-right border-bottom" style="text-align: center;">A</th>
        </tr>

        <?php
        foreach ($data as $val) {
            ?>
            <tr><td class="border-all back-grey" colspan="11" style="background-color:rgb(226, 222, 222);"><b><?= $val['title'] ?></b></td></tr>
            <?php
            $no = 1;
            foreach ($val['body'] as $vals) {
                $p = (!empty($vals['p'])) ? date("d/m/y", strtotime($vals['p'])) : '';
                $a = (!empty($vals['a']) and $vals['a'] != null and $vals['a'] != "0000-00-00") ? date("d/m/y", strtotime($vals['a'])) : '';
                ?>
                <tr>
                    <td class="border-right border-bottom" style="text-align:center"><?= $no ?></td>
                    <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['kd_barang'] ?></td>
                    <td class="border-right border-bottom" style="width: 250px"><?= $vals['nm_barang'] ?></td>
                    <td class="border-right border-bottom" style="text-align:center"><?= $vals['satuan'] ?></td>
                    <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['saldo'] ?></td>
                    <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['qty'] ?></td>
                    <td class="border-right border-bottom"><?= $vals['ket'] ?></td>
                    <td class="border-right border-bottom" style="width: 30px;"><?= $p; ?></td>
                    <td class="border-right border-bottom" style="width: 30px;"><?= $a; ?></td>
                </tr>
                <?php
                $no++;
            }
        }
        ?>

    </table>
</div>
<?php
if (isset($_GET['printlap'])) {
    ?>
    <script type="text/javascript">
        window.print();
        setTimeout(function () {
            window.close();
        }, 1);
    </script>
    <?php
}
?>
