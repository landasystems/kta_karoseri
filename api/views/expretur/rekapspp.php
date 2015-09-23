<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-spp.xls");
}
?>

<?php
$data = array();
$i = 0;
//print_r($models);
foreach ($models as $val) {
    $data[$val['no_spp']]['title']['no_spp'] = $val['no_spp'];
    $data[$val['no_spp']]['title']['tgl_trans'] = $val['tgl_trans'];
    $data[$val['no_spp']]['body'][$i]['nota'] = $val['nota'];
    $data[$val['no_spp']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['no_spp']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['no_spp']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['no_spp']]['body'][$i]['qty'] = $val['qty'];
    $data[$val['no_spp']]['body'][$i]['p'] = $val['p'];
    $data[$val['no_spp']]['body'][$i]['a'] = $val['a'];
    $data[$val['no_spp']]['body'][$i]['ket'] = $val['ket'];
    $i++;
}
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>LAPORAN SURAT PERINTAH PEMBELIAN</b></center>
<br><br>
<?php
if($filter['kategori'] == 'rutin'){
    $jns_spp = "Rutin";
}else if($filter['kategori'] == 'nonrutin'){
    $jns_spp = "Non Rutin";
}else{
    $jns_spp = "Rutin & Non Rutin";
}
?>
<b>Jenis SPP : <?=$jns_spp?></b>
<br><br>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
    <tr>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">No SPP</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">Tanggal</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">No PO</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">Kode Barang</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;" >Nama Barang</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">Satuan</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">Qty</th>
        <th colspan="2" class="border-right border-bottom" style="text-align: center;">Tanggal</th>
        <th rowspan="2" class="border-right border-bottom" style="text-align: center;">Keterangan</th>
    </tr>
    <tr>
        
        
        <th class="border-right border-bottom" style="text-align: center;">Plan</th>
        <th class="border-right border-bottom" style="text-align: center;">Actual</th>
    </tr>
    <?php
    foreach ($data as $key) {
        ?>
        <tr>
            <td class="border-top border-right" valign="top">&nbsp;<?= $key['title']['no_spp']; ?></td>
            <td class="border-top border-right" valign="top"><?= date('d-m-Y', strtotime($key['title']['tgl_trans'])) ?></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
            <td class="border-top border-right"></td>
        </tr>
        <?php
        foreach ($key['body'] as $keys) {
            ?>
            <tr>
                <td class="border-right border-bottom"></td>
                <td class="border-right border-bottom"></td>
                <td style="text-align: center;" class="border-right border-bottom" valign="top"><?= $keys['nota'] ?></td>
                <td style="text-align: center;" class="border-right border-bottom" valign="top"><?= $keys['kd_barang'] ?></td>
                <td class="border-right border-bottom" valign="top"><?= $keys['nm_barang']; ?></td>
                <td style="text-align: center;" class="border-right border-bottom" valign="top"><?= $keys['satuan']; ?></td>
                <td style="text-align: center;" class="border-right border-bottom" valign="top">&nbsp;<?= $keys['qty']; ?></td>
                <td class="border-right border-bottom"valign="top">&nbsp;<?= date('d-m-Y', strtotime($keys['p'])); ?></td>
                <td class="border-right border-bottom" valign="top">&nbsp;<?= date('d-m-Y', strtotime($keys['a'])); ?></td>
                <td class="border-right border-bottom" valign="top"><?= $keys['ket']; ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<?php
if (isset($_GET['print'])) {
    ?>
    <script type="text/javascript">
        window.print();
        setTimeout(function() {
            window.close();
        }, 1);
    </script>
    <?php
}
?>
