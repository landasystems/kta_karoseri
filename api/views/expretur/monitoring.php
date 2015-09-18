<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-monitoring.xls");
}
$data = array();
$i = 0;
foreach ($models as $val) {
    $data[$val['no_wo']]['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['body'][$i]['nm_customer'] = isset($val['nm_customer']) ? $val['nm_customer'] : '-';
    $data[$val['no_wo']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['no_wo']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['no_wo']]['body'][$i]['ket'] = $val['ket'];
    $data[$val['no_wo']]['body'][$i]['inv'] = isset($val['tgl_trans']) ? date("d/m/y", strtotime($val['tgl_trans'])) : '-';
    $data[$val['no_wo']]['body'][$i]['pch'] = isset($val['tgl_pch']) ? date("d/m/y", strtotime($val['tgl_pch'])) : '-';
    $data[$val['no_wo']]['body'][$i]['realisasi'] = isset($val['tgl_realisasi']) ? date("d/m/y", strtotime($val['tgl_realisasi'])) : '-';
    $i++;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:24cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 11px;" width="100%">
        <thead>
            <tr>
                <td rowspan="2" class="border-bottom">
                    <h4 align="center" style="margin-bottom: 0px; font-size: 14px;">MONITORING KELENGKAPAN UNIT</h4>
                    <p align="center" style="margin-bottom: 0px;">Periode <?php echo $periode ?></p>
                    <p align="right" style="margin-right: 20px; margin-bottom: 0px;">NO. DOKUMEN : FR-INV-025 REV 0</p>
                </td>
                <td height="20" class="border-left border-bottom" style="width: 17%" align="center">Dibuat</td>
                <td class="border-left border-bottom" style="width: 17%" align="center">Diperiksa</td>
                <td class="border-left border-bottom" style="width: 17%" align="center">Diterima</td>
            </tr>
            <tr>
                <td class="border-left border-bottom" ></td>
                <td class="border-left border-bottom" ></td>
                <td class="border-left border-bottom" ></td>
            </tr>
        </thead>
    </table>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 11px; margin-top: -2px;" width="100%">
        <thead>
            <tr>
                <td rowspan="2" align="center" class="border-right border-bottom">NO WO</td>
                <td rowspan="2" align="center" class="border-right border-bottom">CUSTOMER</td>
                <td rowspan="2" align="center" class="border-right border-bottom">KODE BRG</td>
                <td rowspan="2" align="center" class="border-right border-bottom">NAMA BARANG</td>
                <td rowspan="2" align="center" class="border-right border-bottom">KETERANGAN</td>
                <td colspan="2" align="center" class="border-right border-bottom">DUE DATE</td>
                <td rowspan="2" align="center">REALISASI</td>
            </tr>
            <tr>
                <td align="center" class="border-right border-bottom" width="80">INV</td>
                <td align="center" class="border-bottom" width="80">PCH</td>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($data as $val) {
                echo '<tr>';
                echo '<td colspan="8" class="border-all">' . $val['no_wo'] . '</td>';
                echo '</tr>';
                foreach ($val['body'] as $det) {
                    echo '<tr>';
                    echo '<td class="border-all"></td>';
                    echo '<td class="border-all">' . $det['nm_customer'] . '</td>';
                    echo '<td class="border-all" align="center">' . $det['kd_barang'] . '</td>';
                    echo '<td class="border-all">' . $det['nm_barang'] . '</td>';
                    echo '<td class="border-all">' . $det['ket'] . '</td>';
                    echo '<td class="border-all" align="center">' . $det['inv'] . '</td>';
                    echo '<td class="border-all" align="center">' . $det['pch'] . '</td>';
                    echo '<td class="border-all" align="center">' . $det['realisasi'] . '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
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
