<?php
if (!isset($_GET['printlap'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-laporan-spp-rutin.xls");
}
$data = array();
$i = 0;
foreach ($models as $val) {

    $data[$val['jenis_brg']]['title'] = $val['jenis_brg'];

    $data[$val['jenis_brg']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['jenis_brg']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['jenis_brg']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['jenis_brg']]['body'][$i]['min'] = $val['min'];
    $data[$val['jenis_brg']]['body'][$i]['max'] = $val['max'];
    $data[$val['jenis_brg']]['body'][$i]['saldo'] = $val['sld'];
    $data[$val['jenis_brg']]['body'][$i]['qty'] = $val['jmlspp'];
    $data[$val['jenis_brg']]['body'][$i]['ket'] = $val['ket'];
    $data[$val['jenis_brg']]['body'][$i]['p'] = $val['p'];
    $data[$val['jenis_brg']]['body'][$i]['a'] = $val['a'];
    $i++;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; border: 1px #000 solid; font-size: 9px;" width="100%">
    <tr>
        <td class="border-right border-bottom"  rowspan="4" colspan="2" align="center" width="30%">
            <br>
            <b style="margin: 2px;" style="font-size:14px;">SURAT PERINTAH PEMBELIAN</b>
            <h1 style="margin: 2px;" style="font-size:16px;">RUTIN</h1>
            <p style="font-size:11px;">No Dokument :  FR-INV-013 Rev.03</p>
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

                        echo date("d/m/Y", strtotime($periode['tgl1'])) . " - " . date("d/m/Y", strtotime($periode['tgl2']));
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
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">NO</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">KODE BARANG</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">URAIAN</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">SAT</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">MAX. STOK</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">MIN. STOK</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">SALDO</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">QTY</th>
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
            $p = (!empty($vals['p'])) ? date('d-m-Y', strtotime($vals['p'])) : '';
            $a = (!empty($vals['a'])) ? date('d-m-Y', strtotime($vals['a'])) : '';
            ?>
            <tr>
                <td class="border-right border-bottom" style="text-align:center"><?= $no ?></td>
                <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['kd_barang'] ?></td>
                <td class="border-right border-bottom"><?= $vals['nm_barang'] ?></td>
                <td class="border-right border-bottom" style="text-align:center"><?= $vals['satuan'] ?></td>
                <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['min'] ?></td>
                <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['max'] ?></td>
                <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['saldo'] ?></td>
                <td class="border-right border-bottom" style="text-align:center">&nbsp;<?= $vals['qty'] ?></td>
                <td class="border-right border-bottom"><?= $vals['ket'] ?></td>
                <td class="border-right border-bottom"><?= date("d/m/y", strtotime($p)); ?></td>
                <td class="border-right border-bottom"><?= date("d/m/y", strtotime($a)); ?></td>
            </tr>
            <?php
            $no++;
        }
    }
    ?>

</table>
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