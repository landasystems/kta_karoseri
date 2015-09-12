<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-Pergerakan-Barang.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%" border="1">
        <thead>
            <tr>
                <td colspan="3" rowspan="2" align="center" class="border-all">
                    <h2 style="font-size: 18px;">LAPORAN STOK BAHAN MINGGUAN</h2>
                    <p>No. Dokumen : FR-WHS-001.REEV.00</p>
                </td>
                <td rowspan="2" colspan="6" valign="top" width="50%" class="border-all">
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
                <td align="center" valign="top" height="15" colspan="2" class="border-right border-bottom">Dibuat Oleh</td>
                <td align="center" valign="top" colspan="2" class="border-right border-bottom">Diperiksa Oleh</td>
                <td align="center" valign="top" colspan="2" class="border-right border-bottom">Disetujui Oleh</td>
            </tr>
            <tr height="75">
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
    </table>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px; margin-top: -2px;" width="100%" border="1">
        <tr>
            <th rowspan="3" align="center">NO</th>
            <th rowspan="3" align="center">KODE BARANG</th>
            <th rowspan="3" align="center">NAMA BARANG</th>
            <th rowspan="3" align="center">SAT</th>
            <th rowspan="3" align="center">STOK MINIM</th>
            <th rowspan="3" align="center">STOK AWAL</th>
            <th colspan="7" align="center">MUTASI</th>
            <th rowspan="3" align="center">SALDO AKHIR</th>
            <th rowspan="3" align="center">OPNAME</th>
        </tr>
        <tr>
            <th rowspan="2" align="center">MASUK</th>
            <th colspan="6" align="center">KELUAR</th>
        </tr>
        <tr>
            <?php
            foreach ($tgl as $tanggal) {
                echo '<th align="center">' . date("d/m/y", strtotime($tanggal)) . '</th>';
            }
            ?>
        </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($models as $val) {
                echo '<tr>';
                echo '<td align="center" class="border-bottom border-right">' . $no . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['kd_barang'] . '</td>';
                echo '<td align="left" class="border-bottom border-right">' . $val['barang'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['satuan'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['stok_minim'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['saldo_awal'] . '</td>';
                echo '<td align="center" class="border-bottom border-right">' . $val['stok_masuk'] . '</td>';
                foreach ($tgl as $tanggal) {
                    if (isset($val[$tanggal])) {
                        echo '<td align="center" class="border-bottom border-right">' . $val[$tanggal]['jml'] . '</td>';
                    } else {
                        echo '<td align="center" class="border-bottom border-right">0</td>';
                    }
                }
                $no++;
                echo '<td class="border-bottom border-right" align="center">' . $val['saldo_akhir'] . '</td>';
                echo '<td class="border-bottom border-right"></td>';
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
        setTimeout(function() {
            window.close();
        }, 1);
    </script>
    <?php
}
?>