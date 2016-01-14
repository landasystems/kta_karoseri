<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-PO.xls");
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
        <br>
<center><b>REKAP PURCHASE ORDER</b></center>
<br>
<?php
$data = array();
$i = 0;

foreach ($models as $key => $val) {
    $data[$val['nota']]['body']['jml'] = isset($data[$val['nota']]['body']['jml']) ? $data[$val['nota']]['body']['jml'] . $val['jml'] . '<br>' : $val['jml'] . '<br>';
    $data[$val['nota']]['body']['harga'] = isset($data[$val['nota']]['body']['harga']) ? $data[$val['nota']]['body']['harga'] . $val['harga'] . '<br>' : $val['harga'] . '<br>';
    $data[$val['nota']]['body']['bayar'] = isset($data[$val['nota']]['body']['bayar']) ? $data[$val['nota']]['body']['bayar'] . ($val['bayar'] == '0') ? 'Tunai' : 'Kredit' . '<br>' : ($val['bayar'] == '0') ? 'Tunai' : 'Kredit' . '<br>';
    $data[$val['nota']]['body']['total'] = isset($data[$val['nota']]['body']['total']) ? $data[$val['nota']]['body']['total'] . $val['jml'] * $val['harga'] . '<br>' : $val['jml'] * $val['harga'].'<br>';
    $data[$val['nota']]['body']['ket'] = isset($data[$val['nota']]['body']['ket']) ? $data[$val['nota']]['body']['ket'] . $val['ket'] . '<br>' : $val['ket'] . '<br>';
    $data[$val['nota']]['body']['tgl_pengiriman'] = isset($data[$val['nota']]['body']['tgl_pengiriman']) ? $data[$val['nota']]['body']['tgl_pengiriman'] . date('d/m/Y',strtotime($val['tgl_pengiriman'])) . '<br>' : date('d/m/Y',strtotime($val['tgl_pengiriman'])) . '<br>';
    $data[$val['nota']]['body']['nama_supplier'] = isset($data[$val['nota']]['body']['nama_supplier']) ? $data[$val['nota']]['body']['nama_supplier'] . $val['nama_supplier'] . '<br>' : $val['nama_supplier'] . '<br>';
    $data[$val['nota']]['body']['no_bbm'] = isset($data[$val['nota']]['body']['no_bbm']) ? $data[$val['nota']]['body']['no_bbm'] . $val['no_bbm'] . '<br>' : $val['no_bbm'] . '<br>';
    $data[$val['nota']]['body']['kode_barang'] = isset($data[$val['nota']]['body']['kode_barang']) ? $data[$val['nota']]['body']['kode_barang'] . $val['kode_barang'] . '<br>' : $val['kode_barang'] . '<br>';
    $data[$val['nota']]['body']['nm_barang'] = isset($data[$val['nota']]['body']['nm_barang']) ? $data[$val['nota']]['body']['nm_barang'] . $val['nm_barang'] . '<br>' : $val['nm_barang'] . '<br>';
    $data[$val['nota']]['title']['nota'] = $val['nota'];
    $data[$val['nota']]['title']['suplier'] = $val['nama_supplier'];
    $data[$val['nota']]['title']['no_bbm'] = $val['no_bbm'];
    $i++;
}
?>

<table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
    <tr>
        <th valign="top" class="border-all">No PO</th>
        <th valign="top" class="border-all">Supplier</th>
        <th valign="top" class="border-all">NO BBM</th>
        <th valign="top" class="border-all" style="text-align: center;">Kode Barang</th>
        <th valign="top" class="border-all">Nama Barang</th>
        <th valign="top" class="border-all">Qty</th>
        <th valign="top" class="border-all">Harga</th>
        <th valign="top" class="border-all" style="text-align: center;">Total</th>
        <th valign="top" class="border-all" style="text-align: center;">Tgl Kirim</th>
        <th valign="top" class="border-all">Jenis Bayar</th>
        <th valign="top" class="border-all">Keterangan</th>
    </tr>
    <?php
    foreach ($data as $key) {
        ?>
        <tr>
            <td class="border-bottom border-right" valign="top"><?php echo $key['title']['nota'] ?></td>
            <td class="border-bottom border-right" valign="top"><?php echo $key['title']['suplier'] ?></td>
            <td class="border-bottom border-right" valign="top"><?php echo $key['title']['no_bbm'] ?></td>
            <td class="border-bottom border-right" style="text-align: center">&nbsp;<?php echo $key['body']['kode_barang'] ?></td>
            <td class="border-bottom border-right"><?php echo $key['body']['nm_barang'] ?></td>
            <td class="border-bottom border-right" style="text-align: center"><?php echo $key['body']['jml'] ?>&nbsp;</td>
            <td class="border-bottom border-right" style="text-align: right"><?php echo $key['body']['harga']?>&nbsp;</td>
            <td class="border-bottom border-right" style="text-align: right"><?php echo  $key['body']['total'] ?>&nbsp;</td>
            <td class="border-bottom border-right" style="width: 60px;text-align: right"><?php echo $key['body']['tgl_pengiriman']?><?php //echo date("d/m/y",  strtotime($key['body']['tgl_pengiriman'])) ?></td>
            <td class="border-bottom border-right" align="center"><?php echo $key['body']['bayar'] ?></td>
            <td class="border-bottom border-right"><?php echo $key['body']['ket'] ?></td>
        </tr>
        <?php
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
