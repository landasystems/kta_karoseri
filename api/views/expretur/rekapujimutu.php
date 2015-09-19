<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-ujimutu.xls");
}
?>

<table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
    <tr>
        <td rowspan="4" colspan="2">
            <br>
    <center><b>LAPORAN PENERIAMAAN UJI MUTU</b></center>
    <br><br>
    <center>No Dok : FR-SS-014</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="3" valign="top">
        <table style="font-size: 12px;">
            <tr>
                <td>PERIODE</td>
                <td> : <?php echo isset($periode) ? $periode : '-' ?></td>
            </tr>
            <tr>
                <td>CETAK</td>
                <td> : <?php echo date('d M Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIBUAT</td>
    <td>DIPERIKSA</td>
</tr>
<tr>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>Tgl :</td>
    <td>Tgl :</td>
</tr>
</table>
<table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
    <tr>
        <th>WO</th>
        <th>Pembuatan RB</th>
        <th>Pengajuan UM</th>
        <th>No. Register UM</th>
        <th>Merk/Type</th>
        <th>Chassis</th>
        <th>Customer</th>
    </tr>
    <?php
    $jml = 1;
    $total = 0;
    foreach ($models as $key) {
        $total += $jml;
        ?>
        <tr>
            <td valign="top">&nbsp;<?= $key['no_wo']; ?></td>
            <td valign="top"><?= date('d/m/Y', strtotime($key['tanggal_rubah'])) ?></td>
            <td valign="top"><?= date('d/m/Y', strtotime($key['tgl'])) ?></td>
            <td valign="top">&nbsp;<?= $key['kd_uji']; ?></td>
            <td valign="top">&nbsp;<?= $key['merk']; ?>/<?= $key['tipe']; ?></td>
            <td valign="top">&nbsp;<?= $key['no_chassis']; ?></td>
            <td valign="top">&nbsp;<?= $key['nm_customer']; ?></td>
        </tr>
        <?php
    }
    echo'<tr><th>Total</th>
        <th colspan="6" textalign="left" style="text-align: left;">&nbsp;&nbsp;&nbsp;' . $total . '</th>';
    ?>
</table>
<?php
if (isset($_GET['print'])) {
    ?>
    <script type="text/javascript">
        window.print();
        setTimeout(function () {
            window.close();
        }, 1);
    </script>
    <?php
}
?>