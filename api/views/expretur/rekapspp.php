<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-ujimutu.xls");
?>

<?php
$data = array();
$i = 0;
//print_r($models);
foreach ($models as $val) {
    $data[$val['no_spp']]['title']['no_spp'] = $val['no_spp'];
    $data[$val['no_spp']]['title']['tgl_trans'] = $val['tgl_trans'];
    $data[$val['no_spp']]['body'][$i]['nota'] = $val['nota'];
    $data[$val['no_spp']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['no_spp']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['no_spp']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['no_spp']]['body'][$i]['qty'] = $val['qty'];
    $data[$val['no_spp']]['body'][$i]['p'] = $val['p'];
    $data[$val['no_spp']]['body'][$i]['a'] = $val['a'];
    $data[$val['no_spp']]['body'][$i]['ket'] = $val['ket'];
    $i++;
}
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>LAPORAN SURAT PERINTAH PEMBELIAN</b></center>
<br><br>



<table border="1" width="100%">
    <tr>
        <th>No SPP</th>
        <th>Tanggal</th>
        <th>No PO</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Satuan</th>
        <th>Qty</th>
        <th>Plan</th>
        <th>Actual</th>
        <th>Keterangan</th>
    </tr>
    <?php
    foreach ($data as $key) {
        ?>
        <tr>
            <td valign="top">&nbsp;<?= $key['title']['no_spp']; ?></td>
            <td valign="top"><?= date('d m Y', strtotime($key['title']['tgl_trans'])) ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php
        foreach ($key['body'] as $keys) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td valign="top"><?= $keys['nota'] ?></td>
                <td valign="top"><?= $keys['kd_barang'] ?></td>
                <td valign="top"><?= $keys['nm_barang']; ?></td>
                <td valign="top"><?= $keys['satuan']; ?></td>
                <td valign="top">&nbsp;<?= $keys['qty']; ?></td>
                <td valign="top">&nbsp;<?= $keys['p']; ?></td>
                <td valign="top">&nbsp;<?= $keys['a']; ?></td>
                <td valign="top"><?= $keys['ket']; ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
