<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename='excel-notif-unit.xls'");
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
        <th>No SPK</th>
        <th>No WO</th>
        <th>Customer</th>
        <th>Tanggal Notif</th>
        <th>Status</th>
        
    </tr>
    <?php
    foreach ($data as $key => $val) {
        
        ?>
        <tr>
            <td><?=$val['no_spk']?></td>
            <td><?=$val['no_wo']?></td>
            <td><?=$val['nm_customer']?></td>
            <td><?=$val['tgl_kontrak']?></td>
            <td><?=$val['status']?></td>
        </tr>
    <?php } ?>
</table>

