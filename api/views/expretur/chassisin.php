<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-bstk.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>LAPORAN CHASSIS IN</b></center>
<br><br>


<table border="1">
    <tr>
        <td rowspan="4" colspan="2">
            <br>
    <center><b></b></center>
    <br><br>
    <center>No Dok : FR-SS-014</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="2" valign="top">
        <table>
            <tr>
                <td>PERIODE</td>
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
                <td>CETAK</td>
                <td> : <?php echo date('d M Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIBUAT</td>
    <td>DIPERIKSA</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>Tgl :</td>
    <td></td>
</tr>
</table>
<?php
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['tgl']]['title']['province'] = $val['province'];
    $data[$val['tgl']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['tgl']]['body'][$i]['catatan'] = $val['catatan'];
    $data[$val['tgl']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $data[$val['tgl']]['body'][$i]['warna'] = $val['warna'];
    $i++;
}
?>
<table border="1">
    <tr>
        <th>NO</th>
        <th>IN CHASSIS</th>
        <th>SALES</th>
        <th>CUSTOMER</th>
        <th >KODE TITIPAN</th>
        <th>NO WO</th>
        <th>REVENUE</th>
        <th>MERK/TYPE</th>
        <th>NO RANGKA</th>
        <th>NO ENGINE</th>
    </tr>
    <?php
    foreach ($data as $keys) {
        ?>
        <tr><td colspan="6" style="text-align: left;background-color: #008000;"><?= $keys['title']['province'] ?></td></tr>
        <?php
        $no = 1;
        foreach ($keys['body'] as $val) {
            $catatan = str_replace('\r\n', '<br>', $val['catatan']);
            ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $val['no_wo'] ?></td>
            </tr>
            <?php
            $no++;
        }
    }
    ?>
</table>