<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-lapwomasuk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
            <br>
    <center><b>LAPORAN UNIT MASUK</b></center>
    <br>
    <center>No Dokumen : FR-PPC-004 Rev 00</center>
    <center>Applicable To Budgets OI & Budget Opname</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table style="border-collapse: collapse; font-size: 11px; margin-left: -1px" width="100%">

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
            <tr>
                <td style="border-bottom: 1px solid #000" colspan="2"> &nbsp;</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000" colspan="2">&nbsp;</td>
            </tr>
        </table>
    </td>
    <td  style="border: 1px solid #000000;width: 15%" valign="top">
    <center><b>Dibuat oleh</b></center>
    <hr style="border: 1px solid #000000">
    <br><br><br>
    <hr style="border: 1px solid #000000">
    Tgl:
</td>
<td  style="border: 1px solid #000000;width: 15%" valign="top">
<center><b>Diperiksa oleh</b></center>
 <hr style="border: 1px solid #000000">
    <br><br><br>
    <hr style="border: 1px solid #000000">
    Tgl:
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
<table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
    <tr>
        <th class="border-all">IN CHASIS</th>
        <th class="border-all">UNIT</th>
        <th class="border-all">WO</th>
        <th class="border-all">NO ENGINE</th>
        <th class="border-all">NO CHASSIS</th>
        <th class="border-all">SALES</th>
        <th class="border-all">MARKET</th>
        <th class="border-all">MODEL</th>
        <th class="border-all">MERK/TYPE</th>
    </tr>
    <?php
       $jml = 1;
         $grandtotal = 0;
    foreach ($data as $keys) {
     
         
        ?>
    <tr><td colspan="9" class="back-grey" style="text-align: left;background-color: bisque;color:#000;"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

        <?php
        $no = 0;
        $total = 0;
       
        
        foreach ($keys['customer'] as $val1) {
            echo'<tr><td class="border-all back-grey" colspan="9" style="text-align: left;background-color: darkkhaki;">' . $val1['customer'] . '</td></tr>';
            foreach ($val1['body'] as $val) {
                $total += $jml;
                ?>
                <tr>
                    <td class="border-all"><?= $val['kd_titipan']; ?>&nbsp;</td>
                    <td class="border-all"><center>1</center></td>
                    <td class="border-all"><?= $val['no_wo']; ?></td>
                    <td class="border-all"><?= $val['no_mesin']; ?></td>
                    <td class="border-all"><?= $val['no_chassis']; ?></td>
                    
                    <td class="border-all"><?= $val['nama']; ?></td>
                    <td class="border-all"><?= $val['market']; ?></td>
                    <td class="border-all"><?= $val['model']; ?></td>
                    <td class="border-all"><?= $val['merk']; ?> <?= $val['tipe']; ?></td>


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