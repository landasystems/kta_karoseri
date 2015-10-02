<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-lapwomasuk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; font-size: 10px;" width="100%"  border="1">
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
            <br>
    <center><b>WORK IN PROCESS</b></center>
    <br>
    <center>Kode Dokumen : FR-PPC-012 Rev.00</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table style="border-collapse: collapse; font-size: 10px; margin-left: -1px" width="100%">

            <tr>
                <td style="border-bottom: 1px solid #000">Periode</td>
                <?php
                if (!empty($filter['tgl'])) {
                    $value = explode(' - ', $filter['tgl']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td style="border-bottom: 1px solid #000"> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000">Cetak</td>
                <td style="border-bottom: 1px solid #000"> : <?php echo date('d F Y') ?></td>
            </tr>
           
        </table>
    </td>
    <td  style="border: 1px solid #000000;width: 15%" valign="top">
    <center><b>Dibuat oleh</b></center>
    <hr style="border: 1px solid #000000">
    
</td>
<td  style="border: 1px solid #000000;width: 15%" valign="top">
<center><b>Diperiksa oleh</b></center>
 <hr style="border: 1px solid #000000">
  

</tr>
</table>

<table style="border-collapse: collapse; font-size: 9px;" width="100%"  border="1">
    <tr>
        <th class="border-all" style="font-size:8px">NO</th>
        <th class="border-all">NO WO</th>
        <th class="border-all">MERK/TYPE</th>
        <th class="border-all">IN CHASSIS</th>
        <th class="border-all">IN SPK</th>
        <th class="border-all">IN PRODUCTION</th>
        <th class="border-all">AGE</th>
        <th class="border-all">KONTRAK</th>
        <th class="border-all">CUSTOMER</th>
        <th class="border-all">CHASSIS</th>
        <th class="border-all">S/O</th>
        <th class="border-all">LANTAI</th>
        <th class="border-all">KOMP</th>
        <th class="border-all">HK</th>
        <th class="border-all">BODY</th>
        <th class="border-all">HK</th>
        <th class="border-all">PRIMER</th>
        <th class="border-all">HK</th>
        <th class="border-all">DEMPUL</th>
        <th class="border-all">HK</th>
        <th class="border-all">PRA. PAINT</th>
        <th class="border-all">HK</th>
        <th class="border-all">PAINTING</th>
        <th class="border-all">TRIM</th>
        <th class="border-all">HK</th>
        <th class="border-all">PDC</th>
        <th class="border-all">KET</th>
    </tr>
    <?php
    print_r($model);
//         foreach ($model as $data){
//             echo'
//                 <td>'.$data['no_wo'].'</td>
//                 ';
//         }
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