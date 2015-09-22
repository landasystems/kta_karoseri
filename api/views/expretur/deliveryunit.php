
<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Delivery_masuk.xls");
    
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />

<table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
    <tr>
        <td colspan="2" style="border: 1px solid #000000">
            <br>
    <center><b>LAPORAN <br> REKAP DELIVERY UNIT</b></center>


</td>
<td colspan="4" style="border: 1px solid #000000">
    <table>

        <tr>
            <td>Periode</td>
            <?php
            if (!empty($filter['tgl_delivery'])) {
                $value = explode(' - ', $filter['tgl_delivery']);
                $start = date("d-m-Y", strtotime($value[0]));
                $end = date("d-m-Y", strtotime($value[1]));
            } else {
                $start = '';
                $end = '';
            }
            ?>
            <td> : <?php echo $start . ' - ' . $end ?></td>
        </tr>
        <tr>
            <td>Cetak</td>
            <td> : <?php echo date('d-m-Y') ?></td>
        </tr>
        <tr>
            <td colspan='2'></td>
        </tr>
    </table>
</td>
<td colspan="1" style="border: 1px solid #000000" valign="top">
<center><b>DIBUAT</b></center>

</td>
<td colspan="1" style="border: 1px solid #000000" valign="top">
<center><b>DIKETAHUI</b></center>

</td>

</tr>
</table>
<?php
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['lokasi_kntr']]['title']['lokasi'] = $val['lokasi_kntr'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['sales'] = $val['nama'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['jenis'] = $val['jenis'];

    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['tgl_delivery'] = $val['tgl_delivery'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['no_delivery'] = $val['no_delivery'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['merk'] = $val['merk'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['tipe'] = $val['tipe'];
    $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['model'] = $val['model'];$i++;
}
?>

<table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
    <tr>
        <th   st-sort="tgl_nota">NO</th>
        <th   st-sort="tgl_nota">TGL DELV</th>
        <th   st-sort="spp">UNIT</th>
        <th   st-sort="spp">NO DELV</th>
        <th   st-sort="spp">NO WO</th>
        <th  st-sort="spp">CUSTOMER</th>
        <th  st-sort="spp">MERK / TYPE</th>
        <th st-sort="spp">MODEL</th>
    </tr>
    <?php
    $total=0;
    foreach ($data as $key) {
        echo '<tr><td class="border-all back-grey" colspan="8">' . $key['title']['lokasi'] . '</td></tr>';

        foreach ($key['sales'] as $val) {
            echo '<tr><td class="border-all back-grey" colspan="8">' . $val['sales'] . '</td></tr>';


            foreach ($val['jenis'] as $val2) {
                echo '<tr><td class="border-all back-grey" colspan="8">' . $val2['jenis'] . '</td></tr>';
                $no=0;
                $jml=0;
                foreach ($val2['body'] as $val3) {
                    $no++;
                    $jml++;
                    echo'<tr>
                        <td>'.$no.'</td>
                        <td>'.date('d/m/Y', strtotime($val3['tgl_delivery'])).'</td>
                        <td style="text-align:center">1</td>
                        <td>'.$val3['no_delivery'].'</td>
                        <td>'.$val3['no_wo'].'</td>
                        <td>'.$val3['nm_customer'].'</td>
                        <td>'.$val3['merk'].'/'.$val3['tipe'].'</td>
                        <td>'.$val3['model'].'</td>
                            </tr>';
                }
                $total += $jml;
                echo'<tr>
                    <th></th>
                    <th>Total</th>
                    <th></th>
                    <th>'.$jml.'</th>
                    <th colspan="4"></th>
                    </tr>';
            }
        }
         
    }
     echo'<tr>
         <th></th>
         <th style="text-align:center;">GRAND TOTAL</th>
         <th style="text-align:center;">'.$total.'</th>
         <th colspan="5"></th></tr>';
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
