<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-bom.xls");
}

$i = 0;

$detBbk = array();
foreach ($modelbbk as $valBbk) {
    $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] = isset($detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
    if ($valBbk['ket'] != "-") {
        $detBbk[$valBbk['id_jabatan']][$valBbk['kd_barang']]['ket'][] = $valBbk['ket'];
    }
}

$data = array();
foreach ($models as $val) {
    $jKeluar = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]['jml']) ? $detBbk[$val['id_jabatan']][$val['kd_barang']]['jml'] : 0;
    $ket = isset($detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) ? join(',', $detBbk[$val['id_jabatan']][$val['kd_barang']]['ket']) : '-';

    $data[$val['no_wo']]['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['jabatan'] = $val['jabatan'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['kode_barang'] = $val['kd_barang'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['nama_barang'] = $val['nm_barang'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['satuan'] = $val['satuan'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['qty'] = $val['qty'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['ket'] = $ket;
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['harga'] = $val['harga'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['jml_keluar'] = $jKeluar;
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
    <center>
        <b style="margin:0px; padding:0px; font-size:16px;">REALISASI BILL OF MATERIAL</b>
    </center>  
    <?php
    foreach ($data as $value) {
        ?>
        <br>
        <b style="font-size:13px;">NO WO : <?php echo isset($value['no_wo']) ? $value['no_wo'] : '-' ?></b>
        <br><br>
        <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
            <tr height="25">
                <th class="border-all" align="center">Kode Barang</th>
                <th class="border-all" align="center">Nama Barang</th>
                <th class="border-all" align="center">Satuan</th>
                <th class="border-all" align="center">Harga</th>
                <th class="border-all" align="center">Standar</th>
                <th class="border-all" align="center">Realisasi</th>
                <th class="border-all" align="center">Keterangan</th>
            </tr>
            <?php
            foreach ($value['jab'] as $key => $bag) {
                echo '<tr>
                        <td colspan="7" class="back-grey border-all"><b>' . $bag['jabatan'] . '</b></td>
                    </tr>';
                foreach ($bag['body'] as $det) {
                    echo '<tr height="25">
                        <td align="center" class="border-all">' . $det['kode_barang'] . '</td>
                        <td class="border-all">' . $det['nama_barang'] . '</td>
                        <td class="border-all" align="center">' . $det['satuan'] . '</td>
                        <td class="border-all" align="right">' . $det['harga'] . '</td>
                        <td class="border-all" align="center">' . $det['qty'] . '</td>
                        <td class="border-all" align="center">' . $det['jml_keluar'] . '</td>
                        <td class="border-all">' . $det['ket'] . '</td>
                    </tr>';
                }
            }
            ?>
        </table>
        <?php
    }
    ?>
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