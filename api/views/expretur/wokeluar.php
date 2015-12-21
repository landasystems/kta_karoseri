<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-wokeluar.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">



    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <td colspan="2" rowspan="4" style="border: 1px solid #000000; width:130px;">
                <br>
        <center><b>LAPORAN UNIT KELUAR</b></center>
        <br>
        <center>No Dokumen : FR-PPC-017 Rev 00</center>
        <center>Applicable To Realisasi OI & Budget Opname</center>
        <br>

        </td>
        <td colspan="4" rowspan="4" style="border: 1px solid #000000">
            <table style="font-size:12px;">
               
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
                    <td> : <?php echo date('d/m/Y') ?></td>
                </tr>
            </table>
        </td>
        <td class="border-bottom border-right" style="border: 1px solid #000000;width: 100px;" valign="top">
        <center><b>Dibuat oleh</b></center>

        </td>
        <td class="border-bottom border-right" style="border: 1px solid #000000;width: 100px;" valign="top">
        <center><b>Diperiksa oleh</b></center>

        </td>

        </tr>
        <tr>
            <td class="border-bottom border-right" rowspan="2"></td>
            <td class="border-bottom border-right" rowspan="2"></td>
        </tr>
        <tr></tr>
        <tr>
            <td class="border-right border-bottom">Tgl:</td>
            <td class="border-right border-bottom"></td>
        </tr>
    </table>
    <?php
    $data = array();
    $i = 0;
    foreach ($models as $key => $val) {
        $data[$val['nm_customer']]['title']['pro'] = $val['provinsi'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['customer'] = $val['nm_customer'];

        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
//        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['jml_unit'] = $val['jml_unit'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_wo'] = $val['no_wo'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['model'] = $val['model'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['merk'] = $val['merk'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tipe'] = $val['tipe'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nama'] = $val['nama'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl_keluar'] = $val['tgl_keluar'];
        $i++;
    }
    ?>
    <table style="margin-top: -2px;border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th class="border-bottom border-right">IN CHASIS</th>
            <th class="border-bottom border-right">UNIT</th>
            <th class="border-bottom border-right">NO WO</th>
            <th class="border-bottom border-right">MODEL</th>
            <th class="border-bottom border-right">MERK TYPE</th>
            <th class="border-bottom border-right">SALES</th>
            <th class="border-bottom border-right">OUT</th>
            <th class="border-bottom border-right">KETERANGAN</th>
        </tr>
        <?php
        $jml = 1;

        $grandtotal = 0;
        foreach ($data as $keys) {
            ?>
            <tr>
                <td colspan="8" class="border-all back-grey" style="text-align: left;background-color: bisque;color:#000;">
                    <?= $keys['title']['pro']; ?>&nbsp;
                </td
            <tr>

                <?php
                $total = 0;
                foreach ($keys['customer'] as $val1) {
                    echo'<tr>'
                    . '<td class="border-all" colspan="8" style="text-align: left;background-color: darkkhaki;">' . $val1['customer'] . '</td>'
                    . '</tr>';
                    foreach ($val1['body'] as $val) {
                        $total += $jml;
                        ?>
                    <tr>
                        <td class="border-all"><?= Yii::$app->landa->date2Ind($val['tgl_terima']); ?>&nbsp;</td>
                        <td class="border-all"><center>1</center></td>
                    <td class="border-all"><?= $val['no_wo']; ?></td>
                    <td class="border-all"><?= $val['model']; ?></td>
                    <td class="border-all"><?= $val['merk']; ?> <?= $val['tipe']; ?></td>
                    <td class="border-all"><?= $val['nama']; ?></td>
                    <td class="border-all"><?= Yii::$app->landa->date2Ind($val['tgl_keluar']); ?>&nbsp;</td>
                    <td class="border-all"></td>

                    </tr>
                    <?php
                }
                echo'<tr>
                <th class="border-bottom border-right"> Total</th>
                <th class="border-bottom border-right" style="text-align:center;">' . $total . ' </th>
                <th class="border-bottom border-right" colspan="6"> </th>
                </tr>';
                echo'<tr>
                <th class="border-bottom border-right" colspan="8">&nbsp;</th>
                </tr>';
                $grandtotal += $total;
            }
        }
        echo'<tr>
                <th class="border-bottom border-right">Grand Total</th>
                <th class="border-bottom border-right" style="text-align:center;">' . $grandtotal . '</th>
                    <th colspan="6" class="border-bottom border-right"> </th>
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