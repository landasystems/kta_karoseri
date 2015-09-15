<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-Barang_Keluar.xls");
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


<table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
    <center><b>REKAP BARANG KELUAR</b></center>
    <br>
    <center>BBK</center>
    <br>
    No Dok : FR-WHS-015-REV.00
    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table>
            <tr>
                <td>Nomer</td>
                <td> : </td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td> : </td>
            </tr>
            <tr>
                <td>Periode</td>
                <?php
                if (!empty($filter['tgl_periode'])) {
                    $value = explode(' - ', $filter['tgl_periode']);
                    $start = date("d-m-Y", strtotime($value[0]));
                    $end = date("d-m-Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td>Cetak</td>
                <td> : <?php echo date('d/m/Y') ?></td>
            </tr>
        </table>
    </td>
    <td colspan="2" style="border: 1px solid #000000" valign="top">
    <center><b>Dibuat oleh</b></center>

</td>
<td colspan="0" style="border: 1px solid #000000" valign="top">
<center><b>Diperiksa oleh</b></center>

</td>
<td colspan="0" style="border: 1px solid #000000" valign="top">
<center><b>Diketahui oleh</b></center>

</td>
</tr>
</table>

<table width="100%" border="1" style="border-collapse: collapse;">
    <tr>
        <th>#</th>
        <th>TANGGAL</th>
        <th>NO WO</th>
        <th>NO BBK</th>
        <th>BAGIAN</th>
        <th>PLK KERJA</th>
        <th>KODE BARANG</th>
        <th>NAMA BARANG</th>
        <th>SAT</th>
        <th>JML</th>
        <th>KET</th>
    </tr>
    <?php
    $no = 0;
    foreach ($models as $key) {
        ?>
        <tr>
            <td valign="top">&nbsp;<?php echo $no ?></td>
            <td valign="top"><?php echo date('d/m/y', strtotime($key['tanggal'])) ?></td>
            <td valign="top">&nbsp;<?php echo $key['no_wo'] ?></td>
            <td valign="top">&nbsp;<?php echo $key['no_bbk'] ?></td>
            <td valign="top"><?php echo $key['jabatan'] ?></td>
            <td valign="top"><?php echo $key['nama'] ?></td>
            <td valign="top">&nbsp;<?php echo $key['kd_barang'] ?></td>
            <td valign="top"><?php echo $key['nm_barang'] ?></td>
            <td valign="top"><?php echo $key['satuan'] ?></td>
            <td valign="top">&nbsp;<?php echo $key['jml'] ?></td>
            <td valign="top"><?php echo $key['ket'] ?></td>
        </tr>
        <?php
        $no++;
    }
    ?>
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
