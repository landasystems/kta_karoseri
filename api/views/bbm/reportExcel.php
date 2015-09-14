<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-bukti-barang-masuk.xls");
?>
<script type="text/css">
    .joke{
        /*padding: 20px;*/

        border-bottom: 1px;
        border-top: 1px;
        border-left: 1px;
        border-color:red;
    }

</script>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr/>
<center><h4>Laporan Bukti Barang Masuk</h4></center>
<br>
<div class="joke" border="1">
    <table border="1px" style="border-collapse: collapse;width:100%">
        <tr>
            <td style="border-bottom: 1px;width: 40%" colspan="2" border="1">
                Tanggal : <?= $model->tgl_nota ?><br>
                No. BBM : <?= $model->no_bbm ?><br>
                No. PO : <?= $model->no_po ?><br>
            </td>
            <td style="border-top:none !important;border-bottom:none !important;"></td>
            <td style="border:1px;width:55%" colspan="3">
                Dari : <?= $model->supplier->nama_supplier ?> - <?= $model->supplier->alamat ?>
            </td>
        </tr>
    </table>
</div>
<br>
<table border="1" class="detail-barang" style="border-collapse: collapse;width: 100%">
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama Barang</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: center;">Kode Barang</th>
            <th style="text-align: center;" colspan="2">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($detail as $key => $val) {
            echo '<tr>';
            echo '<td style="text-align: center;">' . $no++ . '</td>';
            echo '<td>' . $val->barang->nm_barang . '</td>';
            echo '<td style="text-align: center;">' . $val->jumlah . '</td>';
            echo '<td style="text-align: center;">' . $val->kd_barang . '</td>';
            echo '<td colspan="2">' . $val->keterangan . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
<br>
<table border="1" style="width:100%;border-collapse: collapse;">
    <tr>
        <td colspan="2" rowspan="3" style="height: 100px;font-">Catatan : </td>
        <td style="text-align: center;">PIMPINAN</td>
        <td style="text-align: center;">INSPEKSI</td>
        <td style="text-align: center;">ADM. GUDANG</td>
        <td style="text-align: center;">DISERAHKAN</td>
    </tr>
    <tr>
        <td style="height: 60px !important;"></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>TGL : </td>
        <td>TGL : </td>
        <td>TGL : </td>
        <td>TGL : </td>
    </tr>
</table>
1. Supplier ; 2. Keuangan ; 3. Gudang

