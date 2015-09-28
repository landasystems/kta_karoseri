<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-lap_womasuk.xls");
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
    <center>Applicable To Realisasi OI & Budget Opname</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table style=" font-size: 11px;" width="100%" >

            <tr>
                <td >Periode</td>
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
    <td  style="border: 1px solid #000000;height: 10%" valign="top">
    <center><b>Dibuat oleh</b></center>
    <hr style="border: 1px solid #000">

</td>
<td  style="border: 1px solid #000000" valign="top">
<center><b>Diperiksa oleh</b></center>
<hr style="border: 1px solid #000">

</td>

</tr>
</table >
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
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl'] = $val['tgl'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $i++;
}
?>
<table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
    <tr>
        <th class="border-all" style="font-size: 11px">UNIT</th>
        <th class="border-all" style="font-size: 11px">WO</th>
        <th class="border-all" style="font-size: 11px">CUSTOMER</th>
        <th class="border-all" style="font-size: 11px">MODEL</th>
        <th class="border-all" style="font-size: 11px">MERK/TYPE</th>
        <th class="border-all" style="font-size: 11px">MARKET</th>
        <th class="border-all" style="font-size: 11px">TGL PEMB WO</th>
        <th class="border-all" style="font-size: 11px">IN SPK</th>
        <th class="border-all" style="font-size: 11px">KETERANGAN</th>
    </tr>
    <?php
    $jml = 1;
    $grandtotal = 0;
    foreach ($data as $keys) {
        ?>
        <tr><td  colspan="10" class="back-grey" style="text-align: left;background-color: #a6a6a6;color:#000;border-bottom: 1px solid #000"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

        <?php
        $no = 0;
        $total = 0;
        foreach ($keys['customer'] as $val1) {
            echo'<tr><td class="back-grey" colspan="10" style="text-align: left;background-color: #a6a6a6;">' . $val1['customer'] . '</td></tr>';
            foreach ($val1['body'] as $val) {
                $total += $jml;
                ?>
                <tr>
                    <td style="font-size: 11px" class="border-all"></td>
                    <td class="border-all" style="font-size: 11px"><center><?= "1"; ?></center></td>
            <td class="border-all" style="font-size: 11px"><?= $val['no_wo']; ?></td>
            <td class="border-all" style="font-size: 11px"><?= $val['nm_customer']; ?></td>
            <td class="border-all" style="font-size: 11px"><?= $val['model']; ?></td>
            <td class="border-all" style="font-size: 11px"><?= $val['merk']; ?> <?= $val['tipe']; ?></td>
            <td class="border-all" style="font-size: 11px"><?= $val['market']; ?></td>
            <td class="border-all" style="font-size: 11px"></td>
            <td class="border-all" style="font-size: 11px"><?= date('d/m/Y', strtotime($val['tgl'])); ?>&nbsp;</td>
            <td class="border-all" style="font-size: 11px"></td>



            </tr>
            <?php
        }
        echo'<tr>
                <th> Total</th>
                <th>' . $total . ' </th>
                <th colspan="8"> </th>
                </tr>';
        $grandtotal += $total;
    }
}
echo'<tr>
                <th>Grand Total</th>
                <th>' . $grandtotal . '</th>
                    <th colspan="8"> </th>
                </tr>';
?>
</table>
<table>
    <tr>
        <td colspan="3">
            Berdasarkan "Market"
        </td>
        <td colspan="3">
            Berdasarkan "Merk"
        </td>
        <td colspan="4">
            Berdasarkan "Model"
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
                <tr>
                    <th colspan="2"></th>
                    <th></th>
                </tr>
                <?php
                $no = 0;
                $total = 0;
                foreach ($market as $mar) {
                    $no++;
                    $total += $mar['jumlah'];
                    ?>
                    <tr>
                        <td class="border-all"><?php echo $no ?></td>
                        <td class="border-all"><?php echo $mar['market'] ?></td>
                        <td class="border-all"><?php echo $mar['jumlah'] ?></td>
                    </tr>
                    <?php
                }
                echo'<tr>
                    <th colspan="2">Total</th>
                    <th>' . $total . '</th>
                </tr>';
                ?>
            </table>
        </td>
        <td colspan="3">
            <table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
                <tr>
                    <th colspan="2"><center>Merk</center></th>
                <th><center>Unit</center></th>
    </tr>
    <?php
    $no = 0;
    $total = 0;
    foreach ($merk as $mar) {
        $no++;
        $total += $mar['jumlah'];
        ?>
        <tr>
            <td class="border-all"><?php echo $no ?></td>
            <td class="border-all"><?php echo $mar['merk'] ?></td>
            <td class="border-all"><?php echo $mar['jumlah'] ?></td>
        </tr>
        <?php
    }
    echo'<tr>
                    <th colspan="2">Total</th>
                    <th>' . $total . '</th>
                </tr>';
    ?>
</table>
</td>
<td colspan="4">
    <table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
        <tr>
            <th colspan="2"><center>Model</center></th>
    <th><center>Unit</center></th>
</tr>
<?php
$no = 0;
$total = 0;
foreach ($model as $mar) {
    $no++;
    $total += $mar['jumlah'];
    ?>
    <tr>
        <td class="border-all"><?php echo $no ?></td>
        <td class="border-all"><?php echo $mar['model'] ?></td>
        <td class="border-all"><?php echo $mar['jumlah'] ?></td>
    </tr>
    <?php
}
echo'<tr>
                    <th colspan="2">Total</th>
                    <th>' . $total . '</th>
                </tr>';
?>
</table>
</td>
</tr>
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