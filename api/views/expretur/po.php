<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-retur-PO.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>REKAP PURCHASE ORDER</b></center>
<br><br>

<table border="1">
    <tr>
        <th>No PO</th>
        <th>Supplier</th>
        <th>NO BBM</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Total</th>
        <th>Tgl Kirim</th>
        <th>Jenis Bayar</th>
        <th>Keterangan</th>
    </tr>
    <?php
    $data = array();
    $i = 0;

    foreach ($models as $key => $val) {
        $data[$key] = $val;
        $data[$i]['bayar'] = ($val == '0') ? 'Tunai' : 'Kredit';
        $sup = \app\models\Supplier::find()
                ->where(['kd_supplier' => $data[$i]['suplier']])
                ->One();
        $bbm = \app\models\DetBbm::find()
                ->where(['no_po' => $data[$i]['nota']])
                ->One();
        $brg = \app\models\Barang::find()
                ->where(['kd_barang' => $data[$i]['kd_barang']])
                ->one();
        $supplier = (isset($sup->nama_supplier)) ? $sup->nama_supplier : '';
        $barang = (isset($brg->nm_barang)) ? $brg->nm_barang : '';
        $bb = (isset($bbm->no_bbm)) ? $bbm->no_bbm : '';
        $kodebarang = (isset($brg->kd_barang)) ? $brg->kd_barang : '';
        $data[$i]['suplier'] = ['suplier' => $supplier, 'no_bbm' => $bb];
        $data[$i]['kd_barang'] = ['kd_barang' => $kodebarang, 'nm_barang' => $barang];
        $colspan = 0;
        if (isset($data[$i + 1]) && $data[$i]['suplier']['no_bbm'] == $data[$i + 1]['suplier']['no_bbm']) {
            $datcol = $data[$i]['suplier']['no_bbm'];
            $colspan++;
        } else {
            $datcol = $data[$i]['suplier']['no_bbm'];
            $colspan = 0;
        }
        ?>
        <tr>
            <td rowspan="<?= $colspan ?>">&nbsp;<?= $data[$i]['nota'] ?></td>
            <td rowspan="<?= $colspan ?>"><?= $data[$i]['suplier']['suplier'] ?></td>
            <td rowspan="<?= $colspan ?>"><?= $datcol ?></td>
            <td>&nbsp;<?= $data[$i]['kd_barang']['kd_barang'] ?></td>
            <td><?= $data[$i]['kd_barang']['nm_barang'] ?></td>
            <td>&nbsp;<?= $data[$i]['jml'] ?></td>
            <td>&nbsp;<?= $data[$i]['harga'] ?></td>
            <td>&nbsp;<?= ($data[$i]['jml'] * $data[$i]['harga']) ?></td>
            <td><?= date('d-m-Y', strtotime($data[$i]['tgl_pengiriman'])) ?></td>
            <td><?= $data[$i]['bayar'] ?></td>
            <td><?= $data[$i]['ket'] ?></td>
        </tr>
        <?php
        $i++;
    }
    ?>
</table>

