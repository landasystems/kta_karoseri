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
    if(!empty($val['p'])){
        $p = date('d/m/Y', strtotime($val['p']));
    }else{
        $p = '';
    }
    if(!empty($val['a'])){
        $a = date('d/m/Y', strtotime($val['a']));
    }else{
        $a = '';
    }
    $data[$val['no_spp']]['title']['no_spp'] = $val['no_spp'];
    $data[$val['no_spp']]['title']['tgl_trans'] = date('d-m-Y', strtotime($val['tgl_trans']));
    $data[$val['no_spp']]['body'][$i]['nota'] = $val['nota'];
    $data[$val['no_spp']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['no_spp']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['no_spp']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['no_spp']]['body'][$i]['qty'] = $val['qty'];
    $data[$val['no_spp']]['body'][$i]['p'] = $p;
    $data[$val['no_spp']]['body'][$i]['a'] = $a;
    $data[$val['no_spp']]['body'][$i]['ket'] = $val['ket'];
    $i++;
}
?>

<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:24cm; font-size:12px;">
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
    <center><b>LAPORAN SURAT PERINTAH PEMBELIAN</b></center>
    <br>
    <?php
    if ($filter['kategori'] == 'rutin') {
        $jns_spp = "Rutin";
    } else if ($filter['kategori'] == 'nonrutin') {
        $jns_spp = "Non Rutin";
    } else {
        $jns_spp = "Rutin & Non Rutin";
    }
    ?>

    <b>Jenis SPP : <?= $jns_spp ?></b>
    <br><br>
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
                <td class="border-top border-right" valign="top"><?= date("dm/Y",  strtotime($key['title']['tgl_trans'])) ?></td>
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
                    <td class="border-right border-bottom"valign="top">&nbsp;<?= $keys['p']; ?></td>
                    <td class="border-right border-bottom" valign="top">&nbsp;<?= $keys['a']; ?></td>
                    <td class="border-right border-bottom" valign="top"><?= $keys['ket']; ?></td>
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