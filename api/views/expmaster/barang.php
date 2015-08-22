<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-master-barang.xls");
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['kat']['jenisbarang']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['kat']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['kat']]['body'][$i]['jenis_brg'] = $val['jenis_brg'];
    $data[$val['kat']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['kat']]['body'][$i]['min'] = $val['min'];
    $data[$val['kat']]['body'][$i]['saldo'] = $val['saldo'];
    $data[$val['kat']]['body'][$i]['qty'] = $val['qty'];
    $i++;
}
$data2 = array();
$i2 = 0;
foreach ($data as $keys => $vals) {
    $data2[$vals['jenis_brg']]['title']['jenis_brg'] = $vals['jenis_brg'];
    $data2[$vals['jenis_brg']]['body'][$i2]['kd_barang'] = $vals['kd_barang'];
    $data2[$vals['jenis_brg']]['body'][$i2]['nm_barang'] = $vals['nm_barang'];
    $data2[$vals['jenis_brg']]['body'][$i2]['jenis_brg'] = $vals['jenis_brg'];
    $data2[$vals['jenis_brg']]['body'][$i2]['satuan'] = $vals['satuan'];
    $data2[$vals['jenis_brg']]['body'][$i2]['min'] = $vals['min'];
    $data2[$vals['jenis_brg']]['body'][$i2]['saldo'] = $vals['saldo'];
    $data2[$vals['jenis_brg']]['body'][$i2]['qty'] = $vals['qty'];
    $i++;
}
?>
<h3>Data Master Barang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode Barang</th>
        <th>Nama barang</th>
        <th>Satuan</th>
        <th>Min Stok</th>
        <th>Maks Stok</th>
        <th>Stok</th>
        <th>Qty</th>
    </tr>
    <?php
    $data2 = array();
    $i2 = 0;
    foreach ($data as $key => $arr) {
        ?>
    <tr>
        <td colspan="7">&nbsp;<?= $key['title']['kat'] ?></td><tr>
    <?php
        $data2[$vals['jenis_brg']]['title']['jenis_brg'] = $vals['jenis_brg'];
        $data2[$vals['jenis_brg']]['body'][$i2]['kd_barang'] = $vals['kd_barang'];
        $data2[$vals['jenis_brg']]['body'][$i2]['nm_barang'] = $vals['nm_barang'];
        $data2[$vals['jenis_brg']]['body'][$i2]['jenis_brg'] = $vals['jenis_brg'];
        $data2[$vals['jenis_brg']]['body'][$i2]['satuan'] = $vals['satuan'];
        $data2[$vals['jenis_brg']]['body'][$i2]['min'] = $vals['min'];
        $data2[$vals['jenis_brg']]['body'][$i2]['saldo'] = $vals['saldo'];
        $data2[$vals['jenis_brg']]['body'][$i2]['qty'] = $vals['qty'];
        ?>
        <tr>
            <td>&nbsp;<?= $key['title']['kat'] ?></td>
            <td><?= $arr['nm_barang'] ?></td>
            <td><?= $arr['jenis_brg'] ?></td>
            <td><?= $arr['kat'] ?></td>
            <td>&nbsp;<?= $arr['harga'] ?></td>
            <td>&nbsp;<?= $arr['satuan'] ?></td>
            <td>&nbsp;<?= $arr['max'] ?></td>
            <td>&nbsp;<?= $arr['min'] ?></td>
            <td>&nbsp;<?= $arr['saldo'] ?></td>
            <td>&nbsp;<?= $arr['qty'] ?></td>

        </tr>
    <?php } ?>
</table>

