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
//    
//    
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
    }
    ?>
</table>
