<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-pemantauan-penerimaan-barang.xls");
}
?>

<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">

<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <td class="border-right" rowspan="4" colspan="3">
            <br>
    <center><b>LAPORAN PEMANTAUAN PENERIMAAN BARANG</b></center>
    <br><br>
    <center>No Dokumen FR-PCH-002REV.02</center>
    <br><br>

    </td>
    <td class="border-right" rowspan="4" colspan="5" valign="top">
        <table>
            <tr>
                <td>DEPARTEMENT</td>
                <td> : PURCHASSING</td>
            </tr>
            <tr>

                <td>PERIODE</td>
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
    <td style="text-align: center;" class="border-right border-bottom">DIBUAT</td>
    <td style="text-align: center;" class="border-right border-bottom">DIKETAHUI</td>
</tr>
<tr>
    <td style="width: 90px;" class="border-right" rowspan="3"></td>
    <td rowspan="3"></td>
</tr>
<tr></tr>
<tr>
</tr>
</table>
<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <th style="text-align: center;" rowspan="2">No</th>
        <th style="text-align: center;" rowspan="2">No PO</th>
        <th rowspan="2">Kode Barang</th>
        <th rowspan="2">Nama Barang</th>
        <th style="text-align: center;" rowspan="2">Sat</th>
        <th style="text-align: center;" rowspan="2">QTY</th>
        <th style="text-align: center;" rowspan="2">Deadline</th>
        <th style="text-align: center; width: 60px;" colspan="2">Tanggal Kirim</th>
        <th style="text-align: center;" rowspan="2">Status</th>
    </tr>
    <tr>
        <th style="text-align: center; width: 40px">P</th>
        <th style="text-align: center; width: 40px">A</th>
    </tr>
    <?php
    $no = 1;
    foreach ($models as $key) {
        if ($key['p'] == '0000-00-00' || empty($key['p'])) {
            $key['p'] = '';
        }else{
            $key['p'] = date('d/m',strtotime($key['p']));
        }
        
        if($key['a'] == '0000-00-00' || empty($key['a'])){
            $key['a'] = '';
        }else{
           $key['a'] = date('d/m',strtotime($key['a']));
        }
        
        ?>
        <tr>
            <td style="text-align: center;" class="border-bottom border-right"><?= $no ?></td>
            <td style="text-align: center;" class="border-bottom border-right"><?= $key['nota'] ?></td>
            <td class="border-bottom border-right"><?= $key['kd_barang'] ?></td>
            <td class="border-bottom border-right"><?= $key['nm_barang'] ?></td>
            <td style="text-align: center;" class="border-bottom border-right"><?= $key['satuan'] ?></td>
            <td style="text-align: center;" class="border-bottom border-right"><?= $key['jml'] ?></td>
            <td style="text-align: center;" class="border-bottom border-right"><?= $key['jatuh_tempo'] ?></td>
            <td style="text-align: center;" style="" class="border-bottom border-right"><?= $key['p'] ?></td>
            <td style="text-align: center;" style="" class="border-bottom border-right"><?= $key['a'] ?></td>
            <td class="border-bottom border-right"></td>
        </tr>
        <?php
        $no++;
    }
    ?>
</table>
</div>
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
