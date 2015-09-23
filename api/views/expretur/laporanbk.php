<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-Barang_Keluar.xls");
}
?>

<?php
$data = array();
$i = 0;
foreach ($models as $val) {
    $data[$val['no_wo']]['title'] = (empty($val['no_wo']) || $val['no_wo'] == '-') ? 'Lain - Lain' : $val['no_wo'];
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['title'] = $val['jabatan'];

    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['no_bbk'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['no_bbk']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['no_bbk'] . $val['no_bbk'] . '<br>' : $val['no_bbk'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['tanggal'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['tanggal']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['tanggal'] . date('d/m/Y', strtotime($val['tanggal'])) . '<br>' : date('d/m/Y', strtotime($val['tanggal'])) . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['nama'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['nama']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['nama'] . $val['nama'] . '<br>' : $val['nama'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['petugas'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['petugas']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['petugas'] . $val['petugas'] . '<br>' : $val['petugas'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['kd_barang'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['kd_barang']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['kd_barang'] . $val['kd_barang'] . '<br>' : $val['kd_barang'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['nm_barang'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['nm_barang']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['nm_barang'] . $val['nm_barang'] . '<br>' : $val['nm_barang'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['satuan'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['satuan']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['satuan'] . $val['satuan'] . '<br>' : $val['satuan'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['jml'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['jml']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['jml'] . $val['jml'] . '<br>' : $val['jml'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['ket'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['ket']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['ket'] . $val['ket'] . '<br>' : $val['ket'] . '<br>';
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
    <br>
    <center><b>Rekap Bukti Barang Keluar</b></center>
    <br>
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">


        <tr>

            <th style="text-align: center;" width="40px">No WO</th>
            <th style="text-align: center;" >Bagian</th>
            <th style="text-align: center;" >No BBK</th>
            <th style="text-align: center;" >Tanggal</th>
            <th style="text-align: center;" >PLK Kerja</th>
            <th style="text-align: center;">Petugas</th>
            <th style="text-align: center;">Kode Barang</th>
            <th>Nama Barang</th>
            <th style="text-align: center;">Sat</th>
            <th style="text-align: center;">Jumlah</th>
            <th>Keterangan</th>
        </tr>
        <?php
        foreach ($data as $key) {
            ?>
            <tr>
                <td style="background-color:rgb(226, 222, 222);" class="border-all back-grey" valign="top" colspan="11">
                    <b><?= $key['title'] ?></b>
                </td>
            </tr>
            <?php
            foreach ($key['jabatan'] as $val) {
                ?>
                <tr><td class="border-right"></td>
                    <td class="border-all" valign="top" colspan="10"><?= $val['title'] ?></td>
                </tr>
                <?php
                foreach ($val['body'] as $keys) {
                    ?>
                    <tr>
                        <td class="border-right"></td>
                        <td class="border-right"></td>
                        <td style="text-align: center;" class="border-right" valign="top">&nbsp;<?php echo $keys['no_bbk'] ?></td>
                        <td style="text-align: center;" class="border-right" valign="top"><?php echo $keys['tanggal'] ?></td>
                        <td style="text-align: center;" class="border-right" valign="top"><?php echo $keys['nama'] ?></td>
                        <td style="text-align: center;" class="border-right" valign="top" style="text-align: center;">&nbsp;<?php echo $keys['petugas'] ?></td>
                        <td style="text-align: center;" class="border-right" valign="top" style="text-align: center;">&nbsp;<?php echo $keys['kd_barang'] ?></td>
                        <td class="border-right" valign="top"><?php echo $keys['nm_barang'] ?></td>
                        <td style="text-align: center;" class="border-right" valign="top"><?php echo $keys['satuan'] ?></td>
                        <td style="text-align: center;" class="border-right" valign="top">&nbsp;<?php echo $keys['jml'] ?></td>
                        <td class="border-right" valign="top"><?php echo $keys['ket'] ?></td>
                    </tr>
                    <?php
                }
            }
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
