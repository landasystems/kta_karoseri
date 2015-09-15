<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-ujimutu.xls");
}
?>


<table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
    <tr>
        <td colspan="9">
            <tr>
        <td rowspan="4" colspan="2">
            <br>
    <center><b> PENGAJUAN UJI MUTU</b></center>
    <br><br>
    <center>Kode Dokumen : FR-PPC-00-REV000</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="4" valign="top">
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
                <td>CETAK</td>
                <td> : <?php echo date('d M Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIajukan Oleh</td>
    <td>DIketahui Oleh</td>
    <td>DIsetujui Oleh</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>Customer Relation Officer</td>
    <td>Sales Suport Sect Head</td>
    <td>Finance Dept Head</td>
</tr>
        </td>
    </tr>
    <tr>
        <th rowspan="2">NO</th>
        <th rowspan="2">WO</th>
        <th rowspan="2">Merk / Type</th>
        <th rowspan="2">Bentuk Baru</th>
        <th rowspan="2">Chassis</th>
        <th rowspan="2">Customer</th>
        <th colspan="3">KELAS</th>
    </tr>
    <tr>
        <th>I</th>
        <th>II</th>
        <th>III</th>
    </tr>
    <?php
    $no=0;
    $kelas1=0;
    $kelas2=0;
    $kelas3=0;
    $td='';
    $total=0;
    foreach ($models as $key) {
        $no++;
        if($key['kelas']==1){
            $kelas1 += 148000;
            $td = '<td>'.Yii::$app->landa->rp(148000).'</td>
                <td>-</td>
                <td>-</td>';
        }
        if($key['kelas']==2){
            $kelas2 += 138000;
            $td = '<td>-</td>
                <td>'.Yii::$app->landa->rp(138000).'</td>
                <td>-</td>';
        }
        if($key['kelas']==3){
            $kelas3 += 138000;
             $td = '<td>-</td>
                <td>-</td>
                <td>'.Yii::$app->landa->rp(138000).'</td>';
        }
        ?>
        <tr>
        <td valign="top">&nbsp;<?=$no;?></td>
        <td valign="top">&nbsp;<?=$key['no_wo'];?></td>
        <td valign="top">&nbsp;<?=$key['merk'];?>/<?=$key['tipe'];?></td>
        <td valign="top">&nbsp;<?=$key['bentuk_baru'];?></td>
        <td valign="top">&nbsp;<?=$key['no_chassis'];?></td>
        <td valign="top">&nbsp;<?=$key['nm_customer'];?></td>
        <?php echo $td; ?>
        </tr>
        <?php
    }
    ?>
        <tr>
            <th colspan="6" style="text-align: left">Total</th>
            <td ><?php echo  Yii::$app->landa->rp($kelas1); ?></td>
            <td><?php echo  Yii::$app->landa->rp($kelas2); ?></td>
            <td><?php echo  Yii::$app->landa->rp($kelas3); ?></td>
        </tr>
        <tr>
            <th colspan="6" style="text-align: left">Administrasi</th>
            <td colspan="3"><?= Yii::$app->landa->rp(50000) ?></td>
        </tr>
        <tr>
            <th colspan="6" style="text-align: left">Grand Total</th>
            <td colspan="3"><?php 
            $total = ($kelas1+$kelas2+$kelas3)+50000;
            echo Yii::$app->landa->rp($total); 
            ?> </td>
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

