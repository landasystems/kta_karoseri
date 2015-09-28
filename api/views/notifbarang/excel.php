<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename='excel-notif-barang.xls'");
}
?>

<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:24cm">
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
    <center><h4>Notifikasi Barang</h4></center>
    <br>
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th class="border-bottom border-right">Tanggal Notifikasi</th>
            <th class="border-bottom border-right">Kode Barang</th>
            <th style="text-align: left;" class="border-bottom border-right">Nama Barang</th>
            <th class="border-bottom border-right">Satuan</th>
            <th class="border-bottom border-right">Keterangan</th>

        </tr>
        <?php
        foreach ($data as $key => $val) {
            ?>
            <tr>
                <td style='text-align: center;' class="border-bottom border-right"><?= date('d/m/Y',  strtotime($val['tgl_beli'])) ?></td>
                <td style='text-align: center;' class="border-bottom border-right"><?= $val['kd_barang'] ?></td>
                <td class="border-bottom border-right"><?= $val['nm_barang'] ?></td>
                <td style='text-align: center;' class="border-bottom border-right"><?= $val['satuan'] ?></td>
                <td style='text-align: center;' class="border-bottom border-right"><?= $val['status'] ?></td>
            </tr>
        <?php } ?>
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
