<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-customer.xls");
?>
<h3>Data Master Customer</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama Customer</th>
        <th>Kategori</th>
        <th>Nama Pemilik</th>
        <th>Market</th>
        <th>Alamat 1</th>
        <th>Alamat 2</th>
        <th>Provinsi</th>
        <th>Pulau</th>
        <th>Telphone</th>
        <th>Fax</th>
        <th>No. Hp</th>
        <th>Email</th>
        <th>Website</th>
        <th>Contact Person</th>
        <th>NPWP</th>
        <th>NPPKP</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_cust']?></td>
            <td><?=$arr['nm_customer']?></td>
            <td><?=$arr['kategori']?></td>
            <td><?=$arr['nm_pemilik']?></td>
            <td><?=$arr['market']?></td>
            <td><?=$arr['alamat1']?></td>
            <td><?=$arr['alamat2']?></td>
            <td><?=$arr['provinsi']?></td>
            <td><?=$arr['pulau']?></td>
            <td>&nbsp;<?=$arr['telp']?></td>
            <td>&nbsp;<?=$arr['fax']?></td>
            <td>&nbsp;<?=$arr['hp']?></td>
            <td><?=$arr['email']?></td>
            <td><?=$arr['web']?></td>
            <td>&nbsp;<?=$arr['cp']?></td>
            <td>&nbsp;<?=$arr['npwp']?></td>
            <td>&nbsp;<?=$arr['nppkp']?></td>
            
        </tr>
    <?php } ?>
</table>

