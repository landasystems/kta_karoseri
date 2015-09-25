<?php
if (!isset($_GET['printlap'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-bstk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
        <tr>
            <td class="border-right" rowspan="4" colspan="2" style="font-size:18px;">
                <br>
        <center><h4><b>REKAP BSTK</b></h4></center>
        <br>

        </td>
        <td class="border-right" rowspan="4" colspan="2" valign="top">
            <table style="font-size:12px;">
                <tr>
                    <td width="100">Periode</td>
                    <?php
                    if (!empty($filter['tgl_periode'])) {
                        $value = explode(' - ', $filter['tgl_periode']);
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
                    <td>Tanggal Cetak</td>
                    <td> : <?= Yii::$app->landa->date2Ind(date('d-M-Y')) ?></td>
                </tr>
            </table>
        </td>
        <td style="width:15%;text-align: center;" class="border-right border-bottom" >DIBUAT</td>
        <td style="width:15%;text-align: center;" class="border-right border-bottom" >DIKETAHUI</td>
        </tr>
        <tr>
            <td class="border-right border-bottom" rowspan="2"></td>
            <td class="border-right border-bottom" rowspan="2"></td>
        </tr>
        <tr></tr>
    </table>
    <?php
    $data = array();
    $i = 0;
    foreach ($models as $key => $val) {
        $data[$val['tgl']]['title']['tgl'] = $val['tgl'];
        $data[$val['tgl']]['body'][$i]['no_wo'] = $val['no_wo'];
        $data[$val['tgl']]['body'][$i]['catatan'] = $val['catatan'];
        $data[$val['tgl']]['body'][$i]['nm_customer'] = $val['nm_customer'];
        $data[$val['tgl']]['body'][$i]['warna'] = $val['warna'];
        $i++;
    }
    ?>
    <table style="border-collapse: collapse; font-size: 12px; margin-top: -2px;" width="100%" border="1">
        <tr>
            <th style="text-align: center;">NO</th>
            <th style="text-align: center;">NO WO</th>
            <th>CUSTOMER</th>
            <th style="text-align: center;">WARNA</th>
            <th  colspan="2">CATATAN</th>
        </tr>
        <?php
        foreach ($data as $keys) {
            ?>
            <tr>
                <td colspan="6" style="text-align: left;" class="border-all back-grey">
                    <?= Yii::$app->landa->date2Ind($keys['title']['tgl']); ?>&nbsp;
                </td>
            </tr>
            <?php
            $no = 1;
            foreach ($keys['body'] as $val) {
                $catatan = str_replace('\r\n', '<br>', $val['catatan']);
                ?>
                <tr>
                    <td style="text-align: center;" class="border-bottom border-right"><?= $no ?></td>
                    <td style="text-align: center;" class="border-bottom border-right"><?= $val['no_wo'] ?></td>
                    <td class="border-bottom border-right"><?= $val['nm_customer'] ?></td>
                    <td style="text-align: center;" class="border-bottom border-right"><?= $val['warna'] ?></td>

                    <td class="border-bottom border-right" style="width:290px;" colspan="2"><?= $catatan ?></td>
                </tr>
                <?php
                $no++;
            }
        }
        ?>
    </table>
</div>
<?php
if (isset($_GET['printlap'])) {
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