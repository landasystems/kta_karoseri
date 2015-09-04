<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-Claim.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>REKAP CLAIM UNIT</b></center>
<br><br>

<style>
    .no_border{
        border-top: none;
        border-bottom: none;
    }
</style>

<?php
$data = array();
$i = 0;

foreach ($models as $key => $val) {
    $data[$val['no_wo']]['title']['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['title']['model'] = $val['model'];
    $data[$val['no_wo']]['title']['nm_customer'] = $val['nm_customer'];
    $data[$val['no_wo']]['title']['lokasi_kntr'] = $val['lokasi_kntr'];
    $data[$val['no_wo']]['title']['nama'] = $val['nama'];
    //
    $data[$val['no_wo']]['stat'][$val['stat']]['title'] = $val['stat'];
//
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl'] . $val['tgl'] . '<br>' : $val['tgl'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl_pelaksanaan'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl_pelaksanaan']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl_pelaksanaan'] . $val['tgl_pelaksanaan'] . '<br>' : $val['tgl_pelaksanaan'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['pelaksana'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['pelaksana']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['pelaksana'] . $val['pelaksana'] . '<br>' : $val['pelaksana'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['jns_komplain'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['jns_komplain']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['jns_komplain'] . '- ' . $val['jns_komplain'] . '<br>' : '- ' . $val['jns_komplain'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['problem'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['problem']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['problem'] . '- ' . $val['problem'] . '<br>' : '- ' . $val['problem'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['solusi'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['solusi']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['solusi'] . '- ' . $val['solusi'] . '<br>' : '- ' . $val['solusi'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_mat'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_mat']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_mat'] . $val['biaya_mat'] . '<br>' : $val['biaya_mat'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_tk'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_tk']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_tk'] . $val['biaya_tk'] . '<br>' : $val['biaya_tk'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_spd'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_spd']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_spd'] . $val['biaya_spd'] . '<br>' : $val['biaya_spd'] . '<br>';
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['total'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['total']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['total'] . ($val['biaya_mat'] + $val['biaya_tk'] + $val['biaya_spd']) . '<br>' : ($val['biaya_mat'] + $val['biaya_tk'] + $val['biaya_spd']) . '<br>';


    $i++;
}
?>
<table border="1">
    <tr>
        <td rowspan="4" colspan="4">
            <br>
    <center><b>REKAP DATA CLAIM</b></center>
    <br><br>
    <center>NO DOKUMEN : FR-SS-018</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="6" valign="top">
        <table>
            <tr>
                <td>Departemen Purchassing</td>

            </tr>
            <tr>
                <td>Periode</td>
                <?php
                if (!empty($filter['tanggal'])) {
                    $value = explode(' - ', $filter['tanggal']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>

        </table>
    </td>
    <th colspan="3" style="text-align: ceter">Dibuat</th>
    <th colspan="3" style="text-align: ceter">Diketahui</th>
</tr>
<tr>
    <td colspan="3" rowspan="3"></td>
    <td colspan="3" rowspan="3"></td>
</tr>

</table>
<table border="1">
    <tr>
        <th style="text-align: center;">No</th>
        <th>No WO</th>
        <th>Model</th>
        <th>Customer</th>
        <th>Area</th>
        <th>Sales</th>
        <th>Tgl Claim</th>
        <th>Tgl Penanganan</th>
        <th>Pelakasana</th>
        <th>Jenis Komplain</th>
        <th>Problem</th>
        <th>Perubahan yg dilakukan</th>
        <th>Biaya Material</th>
        <th>Biaya TK</th>
        <th>Biaya SPD</th>
        <th>Biaya Total</th>
    </tr>
    <?php
    $n = 1;
    foreach ($data as $key) {
        ?>
        <tr>
            <td style="border-bottom: none;" valign="top"><?= $n ?></td>
            <td style="border-bottom: none;" valign="top"><?php echo $key['title']['no_wo'] ?></td>
            <td style="border-bottom: none;" valign="top"><?php echo $key['title']['model'] ?></td>
            <td style="border-bottom: none;" valign="top"><?php echo $key['title']['nm_customer'] ?></td>
            <td style="border-bottom: none;" valign="top"><?php echo $key['title']['lokasi_kntr'] ?></td>
            <td style="border-bottom: none;" valign="top"><?php echo $key['title']['nama'] ?></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
            <td style="border-bottom: none;"></td>
        </tr>
        <?php
        foreach ($key['stat'] as $val) {
            ?>
            <tr>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border" valign="top"><b><?= $val['title'] ?></b></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
            </tr>
            <?php
//            foreach ($val['body'] as $vals) {
            ?>
            <tr>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"></td>
                <td class="no_border"><?= $val['body']['tgl'] ?></td>
                <td class="no_border"><?= $val['body']['tgl_pelaksanaan'] ?></td>
                <td class="no_border"><?= $val['body']['pelaksana'] ?></td>
                <td class="no_border"><?= $val['body']['jns_komplain'] ?></td>
                <td class="no_border"><?= $val['body']['problem'] ?></td>
                <td class="no_border"><?= $val['body']['solusi'] ?></td>
                <td class="no_border" style="text-align: right">&nbsp;<?= $val['body']['biaya_mat'] ?></td>
                <td  class="no_border"style="text-align: right">&nbsp;<?= $val['body']['biaya_tk'] ?></td>
                <td  class="no_border"style="text-align: right">&nbsp;<?= $val['body']['biaya_spd'] ?></td>
                <td  class="no_border"style="text-align: right">&nbsp;<?= $val['body']['total'] ?></td>
            </tr>
            <?php
//            }
        }
        $n++;
    }
    ?>
</table>
