<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rubah-bentuk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:24cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
        <tr>
            <td colspan="4" rowspan="5" align="center" class="border-all" width="40%" style="vertical-align: middle; text-align: center">
                <h3>LAPORAN PEMBUATAN RUBAH BENTUK</h3>
                No. Dokumen : FR-SS-013
            </td>
            <td colspan="2" class="border-all">
                <table width="100%" style="font-size: 12px;">
                    <tr height="10">
                        <td width="100">PERIODE</td>
                        <td width="1" width="1">:</td>
                        <td align="left"><?php echo $periode ?></td>
                    </tr>
                </table>
            </td>
            <td class="border-all" heigh="15" width="15%" style="vertical-align: middle; text-align: center">DIBUAT</td>
            <td class="border-all" width="15%" style="vertical-align: middle; text-align: center">DIPERIKSA</td>
        </tr>
        <tr>
            <td colspan="2" class="border-all">
                <table width="100%" style="font-size: 12px;">
                    <tr height="10">
                        <td width="100">CETAK</td>
                        <td width="1" width="1">:</td>
                        <td align="left"><?php echo date("d-m-Y") ?></td>
                    </tr>
                </table>
            </td>
            <td rowspan="3" class="border-all"></td>
            <td rowspan="3" class="border-all"></td>
        </tr>
        <tr height="30">
            <td colspan="2" class="border-all" height="10"></td>
        </tr>
        <tr height="30">
            <td colspan="2" class="border-all" height="10"></td>
        </tr>
        <tr height="30">
            <td colspan="2" class="border-all" height="10"></td>
            <td class="border-all" height="10"></td>
            <td class="border-all" height="10"></td>
        </tr>
        <tr>
            <td align="center" class="border-all" width="50">PEMBUATAN WO</td>
            <td align="center" class="border-all">WO</td>
            <td align="center" class="border-all">PEMBUATAN</td>
            <td align="center" class="border-all">NO. REGISTER</td>
            <td align="center" class="border-all">MERK TYPE</td>
            <td align="center" class="border-all">BENTUK BARU</td>
            <td align="center" class="border-all">CHASSIS</td>
            <td align="center" class="border-all">CUSTOMER</td>
        </tr>
        <?php
        $jml = 0;
        foreach ($models as $val) {
            $jml += $val['jml_unit'];
            echo '<tr>';
            echo '<td  class="border-all"></td>';
            echo '<td align="center"  class="border-all">' . $val['no_wo'] . '</td>';
            echo '<td align="right"  class="border-all">' . date("d-M-Y", strtotime($val['tgl'])) . '&emsp;</td>';
            echo '<td align="left"  class="border-all">' . $val['kd_rubah'] . '</td>';
            echo '<td align="left"  class="border-all">' . $val['merk'] . ' ' . $val['tipe'] . '</td>';
            echo '<td align="left"  class="border-all">' . $val['bentuk_baru'] . '</td>';
            echo '<td align="center"  class="border-all">' . $val['no_chassis'] . '</td>';
            echo '<td align="left"  class="border-all">' . $val['nm_customer'] . '</td>';
            echo '</tr>';
        }
        ?>
        <tr height="30">
            <td class="border-all" style="vertical-align: middle; text-align: center">Total Unit</td>
            <td colspan="7" style="vertical-align: middle; text-align: left"><?php echo $jml ?></td>
        </tr>
    </table>
</div>
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