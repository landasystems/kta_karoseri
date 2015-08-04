<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-retur-PO.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>REKAP PURCHASE ORDER</b></center>
<br><br>
<?php
//$data = array();
//$i = 0;
//
//foreach ($models as $key => $val) {
//    $data[$val['nota']]['body']['jml'] = isset($data[$val['nota']]['body']['jml']) ? $data[$val['nota']]['body']['jml'] . $val['jml'] . '<br>' : $val['jml'] . '<br>';
//    $data[$val['nota']]['body']['bayar'] = isset($data[$val['nota']]['body']['bayar']) ? $data[$val['nota']]['body']['bayar'] . ($val['bayar'] == '0') ? 'Tunai' : 'Kredit' . '<br>' : ($val['bayar'] == '0') ? 'Tunai' : 'Kredit' . '<br>';
//    $data[$val['nota']]['body']['total'] = isset($data[$val['nota']]['body']['total']) ? $data[$val['nota']]['body']['total'] . $val['jml'] * $val['harga'] . '<br>' : $val['jml'] * $val['harga'].'<br>';
//    $data[$val['nota']]['body']['ket'] = isset($data[$val['nota']]['body']['ket']) ? $data[$val['nota']]['body']['ket'] . $val['ket'] . '<br>' : $val['ket'] . '<br>';
//    $data[$val['nota']]['body']['tgl_pengiriman'] = isset($data[$val['nota']]['body']['tgl_pengiriman']) ? $data[$val['nota']]['body']['tgl_pengiriman'] . $val['tgl_pengiriman'] . '<br>' : $val['tgl_pengiriman'] . '<br>';
//    $data[$val['nota']]['body']['harga'] = isset($data[$val['nota']]['body']['harga']) ? $data[$val['nota']]['body']['harga'] . $val['harga'] . '<br>' : $val['harga'] . '<br>';
//    $data[$val['nota']]['body']['nama_supplier'] = isset($data[$val['nota']]['body']['nama_supplier']) ? $data[$val['nota']]['body']['nama_supplier'] . $val['nama_supplier'] . '<br>' : $val['nama_supplier'] . '<br>';
//    $data[$val['nota']]['body']['no_bbm'] = isset($data[$val['nota']]['body']['no_bbm']) ? $data[$val['nota']]['body']['no_bbm'] . $val['no_bbm'] . '<br>' : $val['no_bbm'] . '<br>';
//    $data[$val['nota']]['body']['kd_barang'] = isset($data[$val['nota']]['body']['kd_barang']) ? $data[$val['nota']]['body']['kd_barang'] . $val['kd_barang'] . '<br>' : $val['kd_barang'] . '<br>';
//    $data[$val['nota']]['body']['nm_barang'] = isset($data[$val['nota']]['body']['nm_barang']) ? $data[$val['nota']]['body']['nm_barang'] . $val['nm_barang'] . '<br>' : $val['nm_barang'] . '<br>';
//    $data[$val['nota']]['title']['nota'] = $val['nota'];
//    $data[$val['nota']]['title']['suplier'] = $val['nama_supplier'];
//    $data[$val['nota']]['title']['no_bbm'] = $val['no_bbm'];
//    $i++;
//}
?>

<table border="1">
    <tr>
        <td rowspan="4" colspan="3">
            <br>
    <center><b>LAPORAN PEMBELIAN</b></center>
    <br><br>
    <center>No Dok : FR-PCH-019Rev.0</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="5" valign="top">
        <table>
            <tr>
                <td>NOMOR</td>
                <td> : </td>
            </tr>
            <tr>
                <td>PERIODE</td>
                <td> : </td>
            </tr>
            <tr>
                <td>CETAK</td>
                <td> : <?php echo date('d/m/Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIBUAT</td>
    <td>DIPERIKSA</td>
    <td>DIKETAHUI</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>ADM PURC</td>
    <td>PURC HEAD</td>
    <td>FINANCE HEAD</td>
</tr>
</table>
<table border="1">
    <tr>
        <th valign="top">TANGGAL</th>
        <th valign="top">NO BBM</th>
        <th valign="top">KODE BARANG</th>
        <th>SAT</th>
        <th>JUMLAH</th>
        <th>HARGA SATUAN</th>
        <th>JENIS BARANG</th>
        <th>SURAT JALAN</th>
        <th>PO</th>
        <th>SUPPLIER</th>
    </tr>
    <?php
    foreach ($models as $key) {
        ?>
        <tr><td bgcolor="#00FF00" colspan="10">Kredit</td></tr>
        <tr><td>test</td></tr>
        <?php
        if ($key['bayar'] == 'Kredit') {
            ?>
            <tr>
                <td><?= $key['tgl_pengiriman'] ?></td>
                <td><?= $key['no_bbm'] ?></td>
                <td><?= $key['kd_barang'] ?></td>
                <td><?= $key['nm_barang'] ?></td>
                <td><?= $key['satuan'] ?></td>
                <td><?= $key['jml'] ?></td>
                <td><?= $key['harga'] ?></td>
                <td></td>
                <td><?= $key['jenis_brg'] ?></td>
                <td><?= $key['nota'] ?></td>
                <td><?= $key['nama_supplier'] ?></td>
            </tr>
        <?php }?>
        <tr><td bgcolor="#00FF00" colspan="10">Tunai</td></tr>
        <tr><td>test</td></tr>
       <?php if ($key['bayar'] == 'Tunai') { ?>
            <tr>
                <td><?= $key['tgl_pengiriman'] ?></td>
                <td><?= $key['no_bbm'] ?></td>
                <td><?= $key['kd_barang'] ?></td>
                <td><?= $key['nm_barang'] ?></td>
                <td><?= $key['satuan'] ?></td>
                <td><?= $key['jml'] ?></td>
                <td><?= $key['harga'] ?></td>
                <td></td>
                <td><?= $key['jenis_brg'] ?></td>
                <td><?= $key['nota'] ?></td>
                <td><?= $key['nama_supplier'] ?></td>
            </tr>
        <?php
    }
    ?>
</table>
