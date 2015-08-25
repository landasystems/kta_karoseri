<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename='excel-notif-barang.xls'");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr/>
<center><h4>Notifikasi Barang</h4></center>
<br>
<table border="1">
    <tr>
        <th>Tanggal Notifikasi</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Satuan</th>
        <th>Keterangan</th>
        
    </tr>
    <?php
    foreach ($data as $key => $val) {
        
        ?>
        <tr>
            <td><?=$val['tgl_beli']?></td>
            <td><?=$val['kd_barang']?></td>
            <td><?=$val['nm_barang']?></td>
            <td><?=$val['satuan']?></td>
            <td><?=$val['status']?></td>
        </tr>
    <?php } ?>
</table>

