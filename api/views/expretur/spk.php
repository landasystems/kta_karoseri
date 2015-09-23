<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-SPK.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
        <tr>
            <td colspan="4" rowspan="2" align="center" valign="middle" class="border-all" width="25%" style="padding:10px;"><h3 style="font-size:16px;">REKAP SPK MASUK</h3></td>
            <td colspan="4" rowspan="2" valign="top" class="border-all">
                <table style="font-size: 12px;">
                    <tr>
                        <td width="100" valign="top">Dept/Bagian</td>
                        <td width="1" valign="top">:</td>
                        <td valign="top">Sales Support</td>
                    </tr>
                    <tr>
                        <td width="100" valign="top">Periode</td>
                        <td width="1" valign="top">:</td>
                        <td valign="top"></td>
                    </tr>
                </table>
            </td>
            <td colspan="2" valign="top" align="center" class="border-all" width="80">Dibuat</td>
            <td colspan="2" valign="top" align="center" class="border-all" width="80">Diketahui</td>
        </tr>
        <tr height="70">
            <td colspan="2" class="border-all"></td>
            <td colspan="2" class="border-all"></td>
        </tr>
        <tr>
            <td valign="top" align="center" class="border-all" width="10">NO</td>
            <td valign="top" align="center"class="border-all">TGL CHASSIS</td>
            <td valign="top" align="center" class="border-all">SALES</td>
            <td colspan="2" valign="top" align="center" class="border-all">CUSTOMER</td>
            <td valign="top" align="center" class="border-all">KODE TITIPAN</td>
            <td valign="top" align="center" class="border-all" width="70px;">NO WO</td>
            <td colspan="2" valign="top" align="center" class="border-all">MERK/TYPE</td>
            <td colspan="2" valign="top" align="center" class="border-all">MODEL</td>
            <td valign="top" align="center" class="border-all">NO SPK</td>
        </tr>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td class="border-all" align="center">' . $no . '</td>';
            echo '<td class="border-all">' . date("d/m/y", strtotime($val['tgl_chassis'])) . '</td>';
            echo '<td class="border-all">' . $val['sales'] . '</td>';
            echo '<td class="border-all" colspan="2">' . $val['nm_customer'] . '</td>';
            echo '<td class="border-all" align="right">' . $val['kd_titipan'] . '</td>';
            echo '<td class="border-all" align="center">' . $val['no_wo'] . '</td>';
            echo '<td class="border-all" colspan="2">' . $val['merk'] . '/' . $val['tipe'] . '</td>';
            echo '<td class="border-all" colspan="2">' . $val['model'] . '</td>';
            echo '<td class="border-all" align="right">' . $val['no_spk'] . '</td>';
            echo '</tr>';
            $no++;
        }
        ?>
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