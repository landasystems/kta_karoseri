<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-schedule.xls");
}
$data = array();
$i = 0;
foreach ($models as $val) {
    $data[$val['no_wo']]['in_chasis'] = '<p align="left">' . $val['nm_customer'] . '</p><p align="center">' . $val['no_wo'] . '</p>';
    $data[$val['no_wo']]['model'] = $val['model'];
    $data[$val['no_wo']]['merk_tipe'] = $val['merk'] . ' / ' . $val['tipe'];
    $data[$val['no_wo']]['body'][$i]['proses'] = $val['bagian'];
    $data[$val['no_wo']]['body'][$i]['ps'] = $val['plan_start'];
    $data[$val['no_wo']]['body'][$i]['pf'] = $val['plan_finish'];
    $data[$val['no_wo']]['body'][$i]['as'] = $val['act_start'];
    $data[$val['no_wo']]['body'][$i]['af'] = $val['act_finish'];
    $data[$val['no_wo']]['body'][$i]['keterangan'] = $val['keterangan'];
    $i++;
}
?>
<link href="../../../css/print.css" rel="stylesheet" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <thead>
            <tr>
                <td rowspan="3" width="75" class="border-all" style="text-align:center;" align="center">
                    <img src="../../../img/logo.png" alt=".">
                </td>
                <td rowspan="3" valign="top" class="border-all" width="338" style="padding:5px;">
                    <b style="font-size:18px;">MASTER SCHEDULLE</b>
                    <br><br>
                    Kode Dokumen :
                </td>
                <td rowspan="3" valign="top" class="border-all">
                    Cetak : <?php echo date("d-M-Y") ?>
                </td>
                <td class="border-all" height="10" align="center" width="120" style="vertical-align:middle">
                    Disetujui
                </td>
                <td class="border-all" align="center" width="120" style="vertical-align:middle">
                    Dibuat
                </td>
            </tr>
            <tr>
                <td class="border-all" style="height:60px;"></td>
                <td class="border-all"></td>
            </tr>
            <tr>
                <td class="border-all" height="10" style="vertical-align:middle">Tgl :</td>
                <td class="border-all" style="vertical-align:middle">Tgl : </td>
            </tr>
    </table>
    <table style="border-collapse: collapse; font-size: 12px; margin-top:-2px;" width="100%"  border="1">
        <tr>
            <td rowspan="2" align="left" style="width:120px; vertical-align:middle" class="border-all">In Chasis</td>
            <td rowspan="2" align="center" style="width:100px; vertical-align:middle" class="border-all">Model</td>
            <td rowspan="2" align="center" style="width:100px; vertical-align:middle" class="border-all">Merk / Tipe</td>
            <td rowspan="2" align="center" style="vertical-align:middle" class="border-all" width="124">Proses</td>
            <td align="center" align="center" style="vertical-align:middle" class="border-all" colspan="2">Plan</td>
            <td align="center" align="center" style="vertical-align:middle" class="border-all" colspan="2">Actual</td>
            <td rowspan="2" align="center" style="vertical-align:middle" class="border-all" width="150">Keterangan</td>
        </tr>
        <tr>
            <td align="center" class="border-all" width="65" style="vertical-align:middle">Start</td>
            <td align="center" class="border-all" width="65" style="vertical-align:middle">Finish</td>
            <td align="center" class="border-all" width="65" style="vertical-align:middle">Start</td>
            <td align="center" class="border-all" width="65" style="vertical-align:middle">Finish</td>
        </tr>
        <?php
        foreach ($data as $value) {
            ?>
            <tr>
                <td class="border-all" valign="top"  style="border-bottom: none;"><?php echo $value['in_chasis'] ?></td>
                <td class="border-all" valign="top"  style="border-bottom: none;"><?php echo $value['model'] ?></td>
                <td class="border-all" valign="top"  style="border-bottom: none;"><?php echo $value['merk_tipe'] ?></td>
                <td class="border-all" valign="top"  style="border-bottom: none;"></td>
                <td class="border-all" style="border-bottom: none;"></td>
                <td class="border-all" style="border-bottom: none;"></td>
                <td class="border-all" style="border-bottom: none;"></td>
                <td class="border-all" style="border-bottom: none;"></td>
                <td class="border-all" style="border-bottom: none;" colspan="2"></td>
            </tr>
            <?php
            foreach ($value['body'] as $scedule) {
                ?>
                <tr>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;"></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;"></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;"></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;" align="left"><?php echo $scedule['proses'] ?></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['ps'] ?></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['pf'] ?></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['as'] ?></td>
                    <td class="border-all" valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['af'] ?></td>
                    <td class="border-all" colspan="2" valign="top" style="border-top: none; border-bottom: none;"><?php echo $scedule['keterangan'] ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>
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