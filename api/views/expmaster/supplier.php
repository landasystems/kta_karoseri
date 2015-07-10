<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-supplier.xls");
?>
<h3>Data Master Supplier</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama Supplier</th>
        <th>Alamat</th>
        <th>Telphone</th>
        <th>Contact Person</th>
        <th>Email</th>
        <th>Fax</th>
        <th>Hp</th>
        <th>Keterangan</th>
        
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_supplier']?></td>
            <td><?=$arr['nama_supplier']?></td>
            <td><?=$arr['alamat']?></td>
            <td>&nbsp;<?=$arr['telp']?></td>
            <td>&nbsp;<?=$arr['cp']?></td>
            <td><?=$arr['email']?></td>
            <td>&nbsp;<?=$arr['fax']?></td>
            <td>&nbsp;<?=$arr['hp']?></td>
            <td><?=$arr['ket']?></td>
            
        </tr>
    <?php } ?>
</table>

