<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-bstk.xls");
}
?>


<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table border="1">
        <tr>
            <td colspan="5">
                <br>
        <center><b></b></center>
        <br><br>
        <center>DATA CHASSIS IN</center>
        <br><br>

        </td>
        <td  colspan="5" valign="top">
            <table>
                <tr>
                    <td>DEPARTEMENT</td>

                    <td> Sales Report</td>
                </tr>
                <tr>
                    <td>PERIODE</td>
                    <?php
                    if (!empty($filter['tgl_terima'])) {
                        $value = explode(' - ', $filter['tgl_terima']);
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
                    <td>CETAK</td>
                    <td> : <?php echo date('d/M/Y') ?></td>
                </tr>
            </table>
        </td>
        </tr>

    </table>
    <?php
    $data = array();
    $i = 0;
    foreach ($models as $key => $val) {
        $data[$val['provinsi']]['title']['pro'] = $val['provinsi'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['tipe'] = $val['jenis'];

        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['nama'] = $val['nama'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['nm_customer'] = $val['nm_customer'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['kd_titipan'] = $val['kd_titipan'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['no_wo'] = $val['no_wo'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['total_harga'] = $val['total_harga'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['merk'] = $val['merk'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['type'] = $val['tipe'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['no_chassis'] = $val['no_chassis'];
        $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['no_mesin'] = $val['no_mesin'];
        $i++;
    }
    ?>

    <table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
        <tr>
            <th class="border-bottom border-right" colspan="8" rowspan="3" style="text-align: center; font-size: 14px;">DATA CHASSIS IN</th>
            <td class="border-bottom border-right">Departemen</td>
            <td class="border-bottom border-right">: Selles Suport</td>
            <td class="border-bottom border-right"></td>
        </tr>
        <tr>
            <td>Tgl Cetak</td>
            <td>: <?=date('d/M/Y')?></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>NO</th>
            <th>IN CHASSIS</th>
            <th>SALES</th>
            <th>CUSTOMER</th>
            <th>KODE TITIPAN</th>
            <th>NO WO</th>
            <th>SPK KE PPC</th>
            <th>REVENUE</th>
            <th>MERK/TYPE</th>
            <th>NO RANGKA</th>
            <th>NO ENGINE</th>
        </tr>
        <?php
        foreach ($data as $keys) {
            ?>
            <tr>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right" style="text-align: left;"><?= $keys['title']['pro']; ?>&nbsp;</td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
                <td  class="border-bottom border-right"></td>
            </tr>

            <?php
            $no = 0;
            $total = 0;
            foreach ($keys['tipe'] as $val1) {
                echo'<tr>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right" style="text-align: left;background-color: chartreuse;">' . $val1['tipe'] . '</td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '<td  class="border-bottom border-right"></td>'
                . '</tr>';
                foreach ($val1['body'] as $val) {
                    $no++;
                    $total += $val['total_harga'];
                    ?>
                    <tr>
                        <td  class="border-bottom border-right"><?= $no ?></td>
                        <td  class="border-bottom border-right"><?= Yii::$app->landa->date2Ind($val['tgl_terima']); ?>&nbsp;</td>
                        <td  class="border-bottom border-right"><?= $val['nama']; ?></td>
                        <td  class="border-bottom border-right"><?= $val['nm_customer']; ?></td>
                        <td  class="border-bottom border-right"><?= $val['kd_titipan']; ?></td>
                        <td  class="border-bottom border-right"><?= $val['no_wo']; ?></td>
                        <td  class="border-bottom border-right"> </td>
                        <td  class="border-bottom border-right"><?= Yii::$app->landa->rp($val['total_harga']); ?></td>
                        <td  class="border-bottom border-right"><?= $val['merk']; ?> <?= $val['type']; ?></td>
                        <td  class="border-bottom border-right"><?= $val['no_chassis']; ?></td>
                        <td  class="border-bottom border-right"><?= $val['no_mesin']; ?></td>
                    </tr>
                    <?php
                }
                echo'<tr>
                <th  class="border-bottom border-right"></th>
                <th  class="border-bottom border-right"></th>
                <th  class="border-bottom border-right"></th>
                <th  class="border-bottom border-right"></th>
                <th  class="border-bottom border-right"></th>
                <th  class="border-bottom border-right"></th>
                <th  class="border-bottom border-right">TOTAL</th>
                <th  class="border-bottom border-right">' . Yii::$app->landa->rp($total) . '</th>
                    <th class="border-bottom border-right"></th>
                    <th  class="border-bottom border-right"></th>
                    <th  class="border-bottom border-right"></th>
                </tr>';
            }
        }
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