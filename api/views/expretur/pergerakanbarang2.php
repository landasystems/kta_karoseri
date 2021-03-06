<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-Pergerakan-Barang.xls");
}
?>
<!--<style>
    @media print{
        @page {
            size: portrait;
            margin: 25px;
        }
    }
</style>-->
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:100%">
    <?php
    if (isset($_GET['print'])) {
        ?>
        <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%" border="1">
            <tr>
                <td colspan="3" rowspan="2" align="center" class="border-all" width="30%">
                    <h2 style="font-size: 18px; margin-top:8px;">LAPORAN STOK BAHAN MINGGUAN</h2>
                    <p>No. Dokumen : FR-WHS-011.REV.00</p>
                </td>
                <td rowspan="2" colspan="6" valign="top" class="border-all">
                    <table style="font-size: 12px;">
                        <tr valign="top">
                            <td width="75">Nomor</td>
                            <td>: </td>
                        </tr>
                        <tr valign="top">
                            <td>Periode</td>
                            <td>: <?php echo isset($periode) ? $periode : '-' ?></td>
                        </tr>
                        <tr valign="top">
                            <td>Cetak</td>
                            <td>: <?php echo date("d/m/Y"); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="12%" align="center" valign="top" height="15" colspan="2" class="border-right border-bottom">Dibuat Oleh</td>
                <td width="12%" align="center" valign="top" colspan="2" class="border-right border-bottom">Diperiksa Oleh</td>
                <td width="12%" align="center" valign="top" colspan="2" class="border-right border-bottom">Disetujui Oleh</td>
            </tr>
            <tr height="75">
                <td colspan="2" class="border-all"></td>
                <td colspan="2" class="border-all"></td>
                <td colspan="2" class="border-all"></td>
            </tr>
        </table>
        <?php
    }
    ?>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px; margin-top: -2px;" width="100%" border="1">
        <tr>
            <th rowspan="2" align="center" style="vertical-align:middle">NO</th>
            <th rowspan="2" align="center" width="100" style="vertical-align:middle">KODE BARANG</th>
            <th rowspan="2" align="center" style="vertical-align:middle;text-align: left;">NAMA BARANG</th>
            <th rowspan="2" align="center" style="vertical-align:middle">SAT</th>
            <th rowspan="2" align="center" style="vertical-align:middle">STOK MINIM</th>
            <th rowspan="2" align="center" style="vertical-align:middle">SALDO AWAL</th>
            <th rowspan="2" align="center" style="vertical-align:middle">MASUK</th>
            <th rowspan="2" align="center" style="vertical-align:middle">KELUAR</th>
            <th rowspan="2" align="center" style="vertical-align:middle">SALDO AKHIR</th>
            <th colspan="2" align="center" style="vertical-align:middle">OPNAME</th>
        </tr>
        <tr>
            <th width="50" class="border-all">TGL</th>
            <th width="50" class="border-all">JML</th>
        </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            $sorted = Yii::$app->landa->array_orderby($models, 'barang', SORT_ASC);
            foreach ($sorted as $val) {
                $saldo = $val['saldo_awal'] + ($val['stok_masuk'] - $val['stok_keluar']);
                echo '<tr>';
                echo '<td align="center" class="border-bottom border-right">' . $no . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['kd_barang'] . '</td>';
                echo '<td align="left" class="border-bottom border-right">' . $val['barang'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['satuan'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['stok_minim'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['saldo_awal'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['stok_masuk'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['stok_keluar'] . '</td>';
                echo '<td class="border-bottom border-right" align="center">' . $saldo . '</td>';
                echo '<td class="border-all"></td>';
                echo '<td class="border-all"></td>';
                $no++;
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
