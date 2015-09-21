<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-bstk.xls");
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <td class="border-right" rowspan="4" colspan="2">
            <br>
    <center><b>REKAP BSTK</b></center>
    <br><br>
    <center></center>
    <br><br>

    </td>
    <td class="border-right" rowspan="4" colspan="2" valign="top">
        <table>
            <tr>
                <td>PERIODE</td>
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
    <td style="width:100px;text-align: center;" class="border-right border-bottom">DIBUAT</td>
    <td style="width:100px;text-align: center;" class="border-right border-bottom" >DIKETAHUI</td>
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
<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <th>NO</th>
        <th>NO WO</th>
        <th>CUSTOMER</th>
        <th>WARNA</th>
        <th colspan="2">CATATAN</th>
    </tr>
    <?php
    foreach ($data as $keys) {
        ?>
    <tr><td colspan="6" style="text-align: left;background-color: #008000;"><?= Yii::$app->landa->date2Ind($keys['title']['tgl']); ?>&nbsp;</td></tr>
        <?php
        $no = 1;
        foreach ($keys['body'] as $val) {
            $catatan=str_replace('\r\n','<br>',$val['catatan']);
            ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $val['no_wo'] ?></td>
                <td><?= $val['nm_customer'] ?></td>
                <td><?= $val['warna'] ?></td>
                
                <td colspan="2"><?= $catatan?></td>
            </tr>
            <?php
            $no++;
        }
    }
    ?>
</table>
</div>