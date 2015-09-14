<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-barang.xls");
}
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['kat']]['title']['kategory'] = $val['kat'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['title'] = $val['jenis_brg'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['jenis_brg'] = $val['jenis_brg'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['max'] = $val['max'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['min'] = $val['min'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['saldo'] = $val['saldo'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['qty'] = $val['qty'];
    $i++;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
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
    <center>
        <b style="margin:0px; padding:0px; font-size:16px;">LAPORAN DATA BARANG</b>
        <p style="margin:0px; padding:0px; font-size:12px;">Tanggal Cetak: <?php echo date("d M Y") ?></p><br>
    </center>

    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th align="center" style="font-size: 14px;">Kode Barang</th>
            <th align="center" style="font-size: 14px;">Nama barang</th>
            <th align="center" style="font-size: 14px;">Satuan</th>
            <th align="center" style="font-size: 14px;">Min Stok</th>
            <th align="center" style="font-size: 14px;">Maks Stok</th>
            <th align="center" style="font-size: 14px;">Stok</th>
            <th align="center" style="font-size: 14px;">Qty</th>
        </tr>
        <?php
        foreach ($data as $arr) {
            ?>
            <tr>
                <td  class="border-all back-grey">&nbsp;<b><?= $arr['title']['kategory'] ?></b></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
                <td  class="border-all back-grey"></td>
            <tr>
                <?php
                foreach ($arr['jenis_brg'] as $keys) {
                    ?>
                <tr>
                    <td accesskey=""class="border-right"></td>
                    <td class="border-right"><b><?= $keys['title'] ?></b></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>
                    <td class="border-right"></td>

                </tr>
                <?php
                foreach ($keys['body'] as $value) {
                    ?>
                    <tr>
                        <td class="border-right">&nbsp;<?= $value['kd_barang'] ?></td>
                        <td class="border-right"><?= $value['nm_barang'] ?></td>
                        <td class="border-right" align="center" >&nbsp;<?= $value['satuan'] ?></td>
                        <td class="border-right" align="center" >&nbsp;<?= $value['min'] ?></td>
                        <td class="border-right" align="center" >&nbsp;<?= $value['max'] ?></td>
                        <td class="border-right" align="center" >&nbsp;<?= $value['qty'] ?></td>
                        <td class="border-right" align="center" >&nbsp;<?= $value['saldo'] ?></td>

                    </tr>
                    <?php
                }
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