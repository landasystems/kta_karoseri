<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-laporan-rekap-bbm.xls");
}

$data = array();
foreach ($models as $val) {
    $data[$val['no_bbm']]['no_spp'] = $val['no_spp'];
    $data[$val['no_bbm']]['nota'] = $val['nota'];
    $data[$val['no_bbm']]['no_bbm'] = $val['no_bbm'];
    $data[$val['no_bbm']]['surat_jalan'] = $val['surat_jalan'];
    $data[$val['no_bbm']]['nama_supplier'] = $val['nama_supplier'];
    $data[$val['no_bbm']]['body'][$val['kd_barang']]['kd_barang'] = $val['kd_barang'];
    $data[$val['no_bbm']]['body'][$val['kd_barang']]['tanggal_nota'] = $val['tanggal_nota'];
    $data[$val['no_bbm']]['body'][$val['kd_barang']]['nm_barang'] = $val['nm_barang'];
    $data[$val['no_bbm']]['body'][$val['kd_barang']]['satuan'] = $val['satuan'];
    $data[$val['no_bbm']]['body'][$val['kd_barang']]['jumlah'] = $val['jumlah'];
    $data[$val['no_bbm']]['body'][$val['kd_barang']]['keterangan'] = $val['keterangan'];
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
        <b style="margin:0px; padding:0px; font-size:16px;">REKAP BUKTI BARANG MASUK</b>
        <br><br>
    </center>
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th class="border-all" align="center" width="55">No SPP</th>
            <th class="border-all" align="center" width="55">No PO</th>
            <th class="border-all" align="center" width="55">No BBM</th>
            <th class="border-all" align="center">Surat Jalan</th>
            <th class="border-all">Supplier</th>
            <th class="border-all" align="center" width="65">Tgl Terima</th>
            <th class="border-all" align="center" width="70">Kode Barang</th>
            <th class="border-all">Nama Barang</th>
            <th class="border-all" align="center">Sat</th>
            <th class="border-all" align="center">Qty</th>
            <th class="border-all">Keterangan</th>
        </tr>
        <?php
        $no = 0;
        foreach ($data as $key) {
            $no++;
            ?>
            <tr>
                <td valign="top" class="border-right" align="center"><?php echo $key['no_spp'] ?></td>
                <td valign="top" class="border-right" align="center"><?php echo $key['nota'] ?></td>
                <td valign="top" class="border-right" align="center"><?php echo $key['no_bbm'] ?></td>
                <td valign="top" class="border-right" align="center"><?php echo $key['surat_jalan'] ?></td>
                <td valign="top" class="border-right"><?php echo $key['nama_supplier'] ?></td>
                <td valign="top" class="border-right"></td>
                <td valign="top" class="border-right"></td>
                <td valign="top" class="border-right"></td>
                <td valign="top" class="border-right"></td>
                <td valign="top" class="border-right"></td>
                <td valign="top" class="border-right"></td>
            </tr>
            <?php
            foreach ($key['body'] as $val) {
                ?>
                <tr>
                    <td valign="top" class="border-right border-bottom"></td>
                    <td valign="top" class="border-right border-bottom"></td>
                    <td valign="top" class="border-right border-bottom"></td>
                    <td valign="top" class="border-right border-bottom"></td>
                    <td valign="top" class="border-right border-bottom"></td>
                    <td valign="top" class="border-right border-bottom" align="center"><?php echo date('d/m/y', strtotime($val['tanggal_nota'])) ?></td>
                    <td valign="top" class="border-right border-bottom" align="center"><?php echo $val['kd_barang'] ?></td>
                    <td valign="top" class="border-right border-bottom"><?php echo $val['nm_barang'] ?></td>
                    <td valign="top" class="border-right border-bottom" align="center"><?php echo $val['satuan'] ?></td>
                    <td valign="top" class="border-right border-bottom" align="center"><?php echo $val['jumlah'] ?></td>
                    <td valign="top" class="border-right border-bottom"><?php echo $val['keterangan'] ?></td>
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