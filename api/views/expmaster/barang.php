<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-barang.xls");
?>
<h3>Data Master Barang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama barang</th>
        <th>Jenis Barang</th>
        <th>Kategori</th>
        <th>Harga Barang</th>
        <th>Satuan</th>
        <th>Min</th>
        <th>Max</th>
        <th>Qty</th>
        <th>Saldo</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_barang']?></td>
            <td><?=$arr['nm_barang']?></td>
            <td><?=$arr['jenis_brg']?></td>
            <td><?=$arr['kat']?></td>
            <td>&nbsp;<?=$arr['harga']?></td>
            <td>&nbsp;<?=$arr['satuan']?></td>
            <td>&nbsp;<?=$arr['min']?></td>
            <td>&nbsp;<?=$arr['max']?></td>
            <td>&nbsp;<?=$arr['qty']?></td>
            <td>&nbsp;<?=$arr['saldo']?></td>
            
        </tr>
    <?php } ?>
</table>

