<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-bstk.xls");
}
?>

<table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
            <br>
    <center><b>LAPORAN UNIT MASUK</b></center>
    <br>
    <center>No Dokumen : FR-PPC-004 Rev 00</center>
    <center>Applicable To Realisasi OI & Budget Opname</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table>

            <tr>
                <td>Periode</td>
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
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td>Cetak</td>
                <td> : <?php echo date('d F Y') ?></td>
            </tr>
        </table>
    </td>
    <td  style="border: 1px solid #000000" valign="top">
    <center><b>Dibuat oleh</b></center>

</td>
<td  style="border: 1px solid #000000" valign="top">
<center><b>Diperiksa oleh</b></center>

</td>

</tr>
</table>
<?php
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['nm_customer']]['title']['pro'] = $val['provinsi'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['customer'] = $val['nm_customer'];

    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['kd_titipan'] = $val['kd_titipan'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['jml_unit'] = $val['jml_unit'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['model'] = $val['model'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['merk'] = $val['merk'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tipe'] = $val['tipe'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nama'] = $val['nama'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_chassis'] = $val['no_chassis'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_mesin'] = $val['no_mesin'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['market'] = $val['market'];
    $i++;
}
?>
<table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
    <tr>
        <th>IN CHASIS</th>
        <th>UNIT</th>
        <th>WO</th>
        <th>NO ENGINE</th>
        <th>NO CHASSIS</th>
        <th>SALES</th>
        <th>MARKET</th>
        <th>MODEL</th>
        <th>MERK/TYPE</th>
    </tr>
    <?php
       $jml = 1;
         $grandtotal = 0;
    foreach ($data as $keys) {
     
         
        ?>
        <tr><td colspan="9" style="text-align: left;background-color: bisque;color:#000;"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

        <?php
        $no = 0;
        $total = 0;
       
        
        foreach ($keys['customer'] as $val1) {
            echo'<tr><td colspan="9" style="text-align: left;background-color: darkkhaki;">' . $val1['customer'] . '</td></tr>';
            foreach ($val1['body'] as $val) {
                $total += $jml;
                ?>
                <tr>
                    <td><?= $val['kd_titipan']; ?>&nbsp;</td>
                    <td><center>1</center></td>
                    <td><?= $val['no_wo']; ?></td>
                    <td><?= $val['no_chassis']; ?></td>
                    <td><?= $val['no_mesin']; ?></td>
                    <td><?= $val['nama']; ?></td>
                    <td><?= $val['market']; ?></td>
                    <td><?= $val['model']; ?></td>
                    <td><?= $val['merk']; ?> <?= $val['tipe']; ?></td>


                </tr>
                <?php
            }
            echo'<tr>
                <th> Total</th>
                <th>'.$total.' </th>
                <th colspan="7"> </th>
                </tr>';
            $grandtotal += $total;
        }
      
    }
       echo'<tr>
                <th>Grand Total</th>
                <th>'.$grandtotal.'</th>
                    <th colspan="7"> </th>
                </tr>';
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