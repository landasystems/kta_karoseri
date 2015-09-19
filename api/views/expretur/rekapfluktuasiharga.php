<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-Laporan-spp-rutin.xls");

?>


<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <td class="border-right" rowspan="4" colspan="2">
            <br>
    <center><b>FLUKTUASI HARGA</b></center>
    <br><br>
    <center>NO DOKUMEN FR-PCH-004 Rev.03</center>
    <br><br>

    </td>
    <td class="border-right" rowspan="4" colspan="3" valign="top">
        <table>
            <tr>
                <td>Departemen Purchassing</td>

            </tr>
            <tr>
                <td>Periode</td>
                <?php
                if (!empty($filter['tanggal'])) {
                    $value = explode(' - ', $filter['tanggal']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>

        </table>
    </td>
    <td class="border-bottom border-right">Dibuat</td>
    <td class="border-bottom border-right">>Diketahui</td>
</tr>
<tr>
    <td class="border-bottom border-right" rowspan="2"></td>
    <td class="border-bottom border-right" rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td style="height: 20px"class="border-bottom border-right"></td>
    <td class="border-bottom border-right"></td>
</tr>
</table>
<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <th>No</th>
        <th>Supplier</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Sat</th>
        <th>Tanggal</th>
        <th>Harga Barang</th>
    </tr>
    <?php
    $no=1;
    foreach ($models as $key) {
        ?>
        <tr>
            <td class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
            <td class="border-bottom border-right" valign="top"><?=$key['nama_supplier']?></td>
            <td class="border-bottom border-right" valign="top">&nbsp;<?=$key['kd_barang']?></td>
            <td class="border-bottom border-right" valign="top"><?= $key['nm_barang']; ?></td>
            <td class="border-bottom border-right" valign="top"><?= $key['satuan'];?></td>
            <td class="border-bottom border-right" valign="top"><?= $key['tgl_pengiriman']; ?></td>
            <td class="border-bottom border-right" valign="top" style="text-align: right;">&nbsp;<?= $key['hrg_barang']; ?></td>
        </tr>
        <?php
        $no++;
    }
    ?>
</table>
</div>