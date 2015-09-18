<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename='excel-notif-unit.xls'");
}
?>

<?php
$data = array();
$i = 0;
foreach ($model as $val) {
    $data[$val['tgl_kontrak']]['title'] = $val['tgl_kontrak'];
    $data[$val['tgl_kontrak']]['body'][$i]['tgl_kontrak'] = $val['tgl_kontrak'];
    $data[$val['tgl_kontrak']]['body'][$i]['no_spk'] = $val['no_spk'];
    $data[$val['tgl_kontrak']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['tgl_kontrak']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $data[$val['tgl_kontrak']]['body'][$i]['status'] = $val['status'];
    $i++;
}
?>

<link rel="stylesheet" href="../../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <?php
    if (isset($_GET['print'])) {
        ?>
        <table>
            <tr>
                <td width="80"><img src="../../../img/logo.png"></td>
                <td valign="top">
                    <b style="font-size: 18px; margin:0px; padding:0px;">PT KARYA TUGAS ANDA</b>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Jl. Raya Sukorejo No. 1 Sukorejo 67161, Pasuruan Jawa Timur</p>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com</p>
                </td>
            </tr>
        </table>
        <hr>
        <?php
    }
    ?>
    <center><h4>Laporan Notifikasi Unit</h4></center>
    <br>

    <table border="1" style="border-collapse: collapse; font-size: 12px;" width="100%">
        <tr>

            <th style="text-align: center;">Tanggal Notif</th>
            <th style="text-align: center;">No SPK</th>
            <th style="text-align: center;">No WO</th>
            <th>Customer</th>
            <th>Status</th>

        </tr>
        <?php
        foreach ($data as $value) {
            ?>
            <tr>
                <td class="border-top border-right" style="text-align: center;"><?= $value['title'] ?></td>
                <td class="border-top border-right"></td>
                <td class="border-top border-right"></td>
                <td class="border-top border-right"></td>
                <td class="border-top border-right"></td>
            </tr>
            <?php
            foreach ($value['body'] as $val) {
                ?>
                <tr>
                    <td class="border-right border-bottom"></td>
                    <td class="border-right border-bottom" style="text-align: center;"><?= $val['no_spk'] ?></td>
                    <td class="border-right border-bottom" style="text-align: center;"><?= $val['no_wo'] ?></td>
                    <td class="border-right border-bottom" ><?= $val['nm_customer'] ?></td>
                    <td class="border-right border-bottom"><?= $val['status'] ?></td>
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