<table width="100%" border="1">
    <tr>
        <td width="250" rowspan="2" align="center" valign="top">
            <h2>LAPORAN STOK BAHAN MINGGUAN</h2>
            <p>No. Dokumen : FR-WHS-001.REEV.00</p>
        </td>
        <td rowspan="2" valign="top" width="50%">
            <table>
                <tr valign="top">
                    <td width="75">Nomor</td>
                    <td width="1">:</td>
                    <td></td>
                </tr>
                <tr valign="top">
                    <td>Periode</td>
                    <td width="1">:</td>
                    <td></td>
                </tr>
                <tr valign="top">
                    <td>Cetak</td>
                    <td width="1">:</td>
                    <td><?php echo date("d/m/Y");?></td>
                </tr>
            </table>
        </td>
        <td align="center" valign="top" height="15">Dibuat Oleh</td>
        <td align="center" valign="top">Diperiksa Oleh</td>
        <td align="center" valign="top">Disetujui Oleh</td>
    </tr>
    <tr height="75">
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
<table width="100%" border="1">
    <thead >
        <tr>
            <th rowspan="3">NO</th>
            <th rowspan="3">KODE BARANG</th>
            <th rowspan="3">NAMA BARANG</th>
            <th rowspan="3">SAT</th>
            <th rowspan="3">STOK MINIM</th>
            <th rowspan="3">STOK AWAL</th>
            <th colspan="7">MUTASI</th>
            <th rowspan="3">KELUAR</th>
        </tr>
        <tr>
            <td rowspan="2">SALDO AKHIR</td>
            <td colspan="6">OPNAME</td>
        </tr>
        <tr>
            <?php
            foreach ($tgl as $tanggal) {
                echo '<td>' . date("d/m/y", strtotime($tanggal)) . '</td>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td>' . $no . '</td>';
            echo '<td>' . $val['kd_barang'] . '</td>';
            echo '<td>' . $val['barang'] . '</td>';
            echo '<td>' . $val['satuan'] . '</td>';
            echo '<td>' . $val['stok_minim'] . '</td>';
            echo '<td>' . $val['saldo_awal'] . '</td>';
            echo '<td>' . $val['stok_masuk'] . '</td>';
            foreach ($tgl as $tanggal) {
                if (isset($val[$tanggal])) {
                    echo '<td>' . $val[$tanggal]['jml'] . '</td>';
                } else {
                    echo '<td>0</td>';
                }
            }
            $no++;
            echo '<td>' . $val['saldo_akhir'] . '</td>';
        }
        ?>
    </tbody>
</table>