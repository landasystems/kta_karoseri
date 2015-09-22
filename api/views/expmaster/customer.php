<?php
if (!isset($_GET['printlap'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-customer.xls");
}
?>
<?php
$data = array();
$i = 0;
foreach ($models as $val) {
    $data[$val['market']]['title']['market'] = $val['market'];
    $data[$val['market']]['body'][$i]['kd_cust'] = $val['kd_cust'];
    $data[$val['market']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $data[$val['market']]['body'][$i]['market'] = $val['market'];
    $data[$val['market']]['body'][$i]['alamat1'] = $val['alamat1'];
    $data[$val['market']]['body'][$i]['telp'] = $val['telp'];
    $data[$val['market']]['body'][$i]['hp'] = $val['hp'];
    $data[$val['market']]['body'][$i]['email'] = $val['email'];
    $data[$val['market']]['body'][$i]['cp'] = $val['cp'];
    $i++;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <center><h3>Data Master Customer</h3>
        Tanggal Cetak : <?= Yii::$app->landa->date2Ind(date('d-M-Y')) ?>
    </center>

    <br>
    <br>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%" border="1">
        <tr>
            <th style="text-align: center;">&nbsp;Kode Customer</th>
            <th>&nbsp;Nama Customer</th>
            <th>&nbsp;Market</th>
            <th>&nbsp;Alamat </th>
            <th>&nbsp;Telpon</th>
            <th>&nbsp;Hp</th>
            <th>&nbsp;Email</th>
            <th>&nbsp;Contact Person</th>
        </tr>
        <?php
        foreach ($data as $val) {
            ?>
            <tr><td class="border-all back-grey" colspan="8" style="background-color:rgb(226, 222, 222);"><b>&nbsp;<?= $val['title']['market'] ?></b></td></tr>
            <?php
            foreach ($val['body'] as $arr) {
                ?>
                <tr>
                    <td class="border-bottom border-right">&nbsp;<?= $arr['kd_cust'] ?></td>
                    <td class="border-bottom border-right"><?= $arr['nm_customer'] ?></td>
                    <td class="border-bottom border-right"><?= $arr['market'] ?></td>
                    <td class="border-bottom border-right" width="100px"><?= $arr['alamat1'] ?></td>
                    <td class="border-bottom border-right" width="90px">&nbsp;<?= $arr['telp'] ?></td>
                    <td class="border-bottom border-right">&nbsp;<?= $arr['hp'] ?></td>
                    <td class="border-bottom border-right"><?= $arr['email'] ?></td>
                    <td class="border-bottom border-right" width="90px">&nbsp;<?= $arr['cp'] ?></td>

                </tr>
                <?php
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