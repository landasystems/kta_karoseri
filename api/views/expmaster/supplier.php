<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-supplier.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <?php
    if (isset($_GET['print'])) {
        ?>
        <table width="100%">
            <tr>
                <td width="80"><img src="../../../img/logo.png"></td>
                <td valign="top">
                    <b style="font-size: 18px; margin:0px; padding:0px;">PT KARYA TUGAS ANDA</b>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Jl. Raya Sukorejo No. 1 Sukorejo 67161, Pasuruan Jawa Timur</p>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com</p>
                </td>
            </tr>
            <tr>
                <td colspan="2"><hr></td>
            </tr>
        </table>
        <?php
    }
    ?>

    <center><h3>Laporan Data Supplier</h3></center>
    <br>

    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 11px;" width="100%">
        <tr>
            <th class="border-bottom border-right" style="text-align: center;">Kode Supplier</th>
            <th class="border-bottom border-right">Nama Supplier</th>
            <th class="border-bottom border-right">Alamat</th>
            <th class="border-bottom border-right">Telp</th>
            <th class="border-bottom border-right">Contact Person</th>
            <th class="border-bottom border-right">Email</th>
            <th class="border-bottom border-right">Produk</th>

        </tr>
        <?php
        foreach ($models as $arr) {
            ?>
            <tr>
                <td class="border-bottom border-right">&nbsp;<?= $arr['kd_supplier'] ?></td>
                <td class="border-bottom border-right"><?= $arr['nama_supplier'] ?></td>
                <td class="border-bottom border-right"><?= $arr['alamat'] ?></td>
                <td class="border-bottom border-right">&nbsp;<?= $arr['telp'] ?></td>
                <td class="border-bottom border-right">&nbsp;<?= $arr['cp'] ?></td>
                <td class="border-bottom border-right"><?= $arr['email'] ?></td>
                <td class="border-bottom border-right"><?= $arr['ket'] ?></td>

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