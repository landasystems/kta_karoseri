<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-Laporan-bbm-dan-bbk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%" border="1">
        <tr>
            <td rowspan="2" style="vertical-align: middle;text-align: center;width: 30%;" class="border-all">
                <h3 style="font-size:15px; margin:0px;">
                    REKAP BARANG MASUK (BBM)<br>
                    & BARANG KELUAR (BBK)
                </h3>
            </td>
            <td class="border-all" rowspan="2">
                <table style="font-size:12px;">
                    <tr>
                        <td width="75">Nomer</td>
                        <td>: </td>
                    </tr>
                    <tr>
                        <td width="75">Periode</td>
                        <td>: <?php echo $periode; ?></td>
                    </tr>
                    <tr>
                        <td width="75">Cetak</td>
                        <td>: <?php echo date("d/m/Y") ?></td>
                    </tr>
                </table>
            </td>
            <td height="15" align="center" class="border-all" style="width:15%">
                Dibuat Oleh
            </td>
            <td height="15" align="center" class="border-all" style="width:15%">
                Diperiksa Oleh
            </td>
        </tr>
        <tr>
            <td class="border-all" style="height:60px;"></td>
            <td class="border-all"></td>
        </tr>
    </table>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px; margin-top:-2px;" width="100%" border="1">
        <tr>
            <th rowspan="2" style="text-align:center; width: 8%">KODE BARANG</th>
            <th rowspan="2" style="text-align:center; width: 15%">GOLONGAN</th>
            <th rowspan="2" style="text-align:center; width: 25%">NAMA BARANG</th>
            <th rowspan="2" style="text-align:center; width: 10%">SAT</th>
            <th colspan="2" style="text-align:center; width: 10%">JUMLAH</th>
            <th rowspan="2" style="text-align:center; width: 20%">KETERANGAN</th>
        </tr>
        <tr>
            <th style="text-align:center;">BARANG MASUK</th>
            <th style="text-align:center;">BARANG KELUAR</th>
        </tr>
        <?php
        $bbm = 0;
        $bbk = 0;
        foreach ($models as $val) {
            $bbm += $val['jmlBbm'];
            $bbk += $val['jmlBbk'];
            echo '<tr>';
            echo '<td class="border-all" align="center">' . $val['kd_barang'] . '</td>';
            echo '<td class="border-all" align="center">' . $val['golongan'] . '</td>';
            echo '<td class="border-all">' . $val['barang'] . '</td>';
            echo '<td class="border-all" align="center">' . $val['satuan'] . '</td>';
            echo '<td class="border-all" align="center">' . $val['jmlBbm'] . '</td>';
            echo '<td class="border-all" align="center">' . $val['jmlBbk'] . '</td>';
            echo '<td class="border-all" align="center">-</td>';
            echo '</tr>';
        }
        ?>
        <tr>
            <td colspan="4" class="border-all back-grey" align="center">JUMLAH ITEM BARANG</td> 
            <td class="border-all back-grey" align="center"><?php echo $bbm?></td>
            <td class="border-all back-grey" align="center"><?php echo $bbk?></td>
            <td class="border-all back-grey"></td>
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