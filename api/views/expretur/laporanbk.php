<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-Barang_Keluar.xls");
?>

<?php
$data = array();
$i = 0;
foreach ($models as $val) {
    $data[$val['no_wo']]['title'] = (empty($val['no_wo']) || $val['no_wo'] == '-') ? 'Lain - Lain' : $val['no_wo'];
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['title'] = $val['jabatan'];

    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['no_bbk'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['no_bbk']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['no_bbk'] . $val['no_bbk'] . '<br>' : $val['no_bbk'] . '<br>';
    $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body'][$i]['tanggal'] = isset($data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['tanggal']) ? $data[$val['no_wo']]['jabatan'][$val['jabatan']]['body']['tanggal'] . date('d/m/y', strtotime($val['tanggal'])) . '<br>' : date('d/m/y', strtotime($val['tanggal'])) . '<br>';
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
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com


<hr>
<center><b>Rekap Bukti Barang Keluar</b></center>

<table width="100%" border="1" style="border-collapse: collapse;">


    <tr>

        <th>NO WO</th>
        <th>BAGIAN</th>
        <th>NO BBK</th>
        <th>TANGGAL</th>
        <th>PLK KERJA</th>
        <th>PETUGAS</th>
        <th>KODE BARANG</th>
        <th>NAMA BARANG</th>
        <th>SAT</th>
        <th>JML</th>
        <th>KETERANGAN</th>
    </tr>
    <?php
    foreach ($data as $key) {
        ?>
    <tr><td style="background-color:#a6a6a6; " valign="top" colspan="11"><b><?= $key['title'] ?></b></td></tr>
        <?php
        foreach ($key['jabatan'] as $val) {
            ?>
            <tr>&nbsp;<td></td><td valign="top" colspan="10"><?= $val['title'] ?></td></tr>
            <?php
            foreach ($val['body'] as $keys) {
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td valign="top">&nbsp;<?php echo $keys['no_bbk'] ?></td>
                    <td valign="top"><?php echo $keys['tanggal'] ?></td>
                    <td valign="top"><?php echo $keys['nama'] ?></td>
                    <td valign="top">&nbsp;<?php echo $keys['petugas'] ?></td>
                    <td valign="top">&nbsp;<?php echo $keys['kd_barang'] ?></td>
                    <td valign="top"><?php echo $keys['nm_barang'] ?></td>
                    <td valign="top"><?php echo $keys['satuan'] ?></td>
                    <td valign="top">&nbsp;<?php echo $keys['jml'] ?></td>
                    <td valign="top"><?php echo $keys['ket'] ?></td>
                </tr>
                <?php
            }
        }
    }
    ?>
</table>
