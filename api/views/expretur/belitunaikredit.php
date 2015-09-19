<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-laporan-pembelian-tunai-kredit.xls");
}
?>
<?php
$data = array();
$i = 0;

foreach ($models as $key => $val) {

    $data[$val['bayar']]['title']['status_bayar'] = $val['bayar'];
    $data[$val['bayar']]['nota'][$val['nota']]['title'] = $val['nota'];

    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['no_bbm'] = $val['no_bbm'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['jml'] = $val['jml'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['harga'] = $val['harga'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['jenis_brg'] = $val['jenis_brg'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['surat_jalan'] = $val['surat_jalan'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['nota'] = $val['nota'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['nama_supplier'] = $val['nama_supplier'];
    $i++;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
        <tr>
            <td class="border-right" rowspan="4" colspan="3">
                <br>
        <center><b>LAPORAN PEMBELIAN</b></center>
        <br><br>
        <center>No Dokumen : FR-PCH-019Rev.0</center>
        <br><br>

        </td>
        <td class="border-right" rowspan="4" colspan="5" valign="top">
            <table>
                <tr>
                    <td>NOMOR</td>
                    <td> : </td>
                </tr>
                <tr>
                    <td>PERIODE</td>
                    <?php
                    if (!empty($filter['tanggal'])) {
                        $value = explode(' - ', $filter['tanggal']);
                        $start = date("d/m/Y", strtotime($value[0]));
                        $end = date("d/m/Y", strtotime($value[1]));
                    } else {
                        $start = '';
                        $end = '';
                    }
                    ?>
                    <td> : <?php echo $start . ' - ' . $end ?></td>
                </tr>
                <tr>
                    <td>CETAK</td>
                    <td> : <?php echo date('d/m/Y') ?></td>
                </tr>
            </table>
        </td>
        <td style="text-align: center;" class="border-bottom border-right">DIBUAT</td>
        <td style="text-align: center;" class="border-bottom border-right">DIPERIKSA</td>
        <td style="text-align: center;" class="border-bottom border-right">DIKETAHUI</td>
        </tr>
        <tr>
            <td class="border-bottom border-right" rowspan="2"></td>
            <td class="border-bottom border-right" rowspan="2"></td>
            <td class="border-bottom border-right" rowspan="2"></td>
        </tr>
        <tr></tr>
        <tr>
            <td style="text-align: center;" class="border-right">ADM PURC</td>
            <td style="text-align: center;" class="border-right">PURC HEAD</td>
            <td style="text-align: center;" sclass="border-right">FINANCE HEAD</td>
        </tr>
    </table>
    <table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
        <tr>
            <th style="text-align: center;" valign="top">TANGGAL</th>
            <th style="text-align: center;" valign="top">NO BBM</th>
            <th style="text-align: center;" valign="top">KODE BARANG</th>
            <th style="text-align: center;">NAMA BARANG</th>
            <th style="text-align: center;">SAT</th>
            <th style="text-align: center;">JUMLAH</th>
            <th style="text-align: center;">HARGA SATUAN</th>
            <th style="text-align: center;">JENIS BARANG</th>
            <th style="text-align: center;">SURAT JALAN</th>
            <th style="text-align: center;">PO</th>
            <th style="text-align: center;">SUPPLIER</th>
        </tr>
        <?php
        foreach ($data as $key) {
            $status_bayar = ($key['title']['status_bayar'] == 0) ? 'Tunai' : 'Kredit';
            ?>
            <tr>
                <td class="border-all back-grey" style="background-color: rgb(226, 222, 222)" colspan="11">
                    <b><?= $status_bayar ?></b>
                </td>
            </tr>
            <?php
            foreach ($key['nota'] as $keys) {
                ?>
                <tr>
                    <td class="border-bottom border-right" colspan="11"><?= $keys['title'] ?></td>
                </tr>
                <?php
                foreach ($keys['body'] as $val) {
                    ?>
                    <tr>
                        <td class="border-bottom border-right"><?= $val['tgl_terima'] ?></td>
                        <td class="border-bottom border-right"><?= $val['no_bbm'] ?></td>
                        <td class="border-bottom border-right"><?= $val['kd_barang'] ?></td>
                        <td class="border-bottom border-right"><?= $val['nm_barang'] ?></td>
                        <td class="border-bottom border-right"><?= $val['satuan'] ?></td>
                        <td class="border-bottom border-right"><?= $val['jml'] ?></td>
                        <td class="border-bottom border-right"><?= $val['harga'] ?></td>
                        <td class="border-bottom border-right"><?= $val['jenis_brg'] ?></td>
                        <td class="border-bottom border-right"><?= $val['surat_jalan'] ?></td>
                        <td class="border-bottom border-right"><?= $val['nota'] ?></td>
                        <td class="border-bottom border-right"><?= $val['nama_supplier'] ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
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