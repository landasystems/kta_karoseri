<?php
if (!isset($_GET['printlap'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-fluktuasi-harga.xls");
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
    $data[$val['jenis_brg']]['body'][$i]['saldo'] = $val['saldo'];
    $data[$val['jenis_brg']]['body'][$i]['qty'] = $val['qty'];
    $data[$val['jenis_brg']]['body'][$i]['ket'] = $val['ket'];
    $data[$val['jenis_brg']]['body'][$i]['p'] = $val['p'];
    $data[$val['jenis_brg']]['body'][$i]['a'] = $val['a'];
    $i++;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
    <tr>
        <td class="border-right border-bottom"  rowspan="4" colspan="2">
            <br>
    <center><b>SURAT PERINTAH PEMBELIAN</b></center>
    <br><center><h1>RUTIN</h1></center>
    <center>No Dokument :  FR-INV-013 Rev.03</center>
    <br><br>

    </td>
    <td class="border-right border-bottom"  rowspan="4" colspan="3" valign="top">
        <table>
            <tr>
                <td>No. SPP</td>
                <td> : <?= $id ?></td>

            </tr>
            <tr>
                <td>Periode</td>

                <td> : 
                    <?php
                    $periode = \app\models\TransSpp::findOne(['no_spp' => $id]);

                    echo $periode['tgl1'] . " - " . $periode['tgl2'];
                    ?>
                </td>
            </tr>
            <tr>
                <td>Cetak</td>

                <td> : <?= date('d-m-Y') ?></td>
            </tr>

        </table>
    </td>
    <td class="border-right border-bottom" style="text-align: center;">Dibuat Oleh</td>
    <td class="border-right border-bottom" style="text-align: center;">Diperiksa Oleh</td>
    <td class="border-right border-bottom" style="text-align: center;">Diketahui Oleh</td>
    <td class="border-right border-bottom" style="text-align: center;">Diterima Oleh</td>
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
    <td class="border-right border-bottom" style="text-align: center;">FInance</td>
    <td class="border-right border-bottom" style="text-align: center;">Purchasing</td>
</tr>
</table>
<table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width='100%'>
    <tr>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">No</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Kode Barang</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Uraian</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Sat</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Max.Stok</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Min.Stok</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Saldo</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Qty</th>
        <th class="border-right border-bottom" style="text-align: center;" rowspan="2">Keterangan</th>
        <th class="border-right border-bottom" style="text-align: center;" colspan="2">Schedulle</th>

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
                <td class="border-right border-bottom"><?= $p ?></td>
                <td class="border-right border-bottom"><?= $a ?></td>
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