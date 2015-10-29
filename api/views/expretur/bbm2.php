<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-laporan-barang-masuk.xls");
}

if ($filter['bayar'] == "0") {
    $hd = "CASH";
    $no = 008;
} else {
    $hd = "KREDIT";
    $no = 009;
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <?php
    if (isset($_GET['print'])) {
        ?>
        <table class="border-all" style="border-collapse: collapse; font-size: 12px;" width="100%">
            <tr>
                <td align="center" style="width:25%;padding-top: 10px;" rowspan="2">
                    <b style="font-size: 14px;">LAPORAN BARANG MASUK (BBM)</b>
                    <p style="margin:5px;"><b style="font-size: 14px;"><?php echo $hd ?></b></p>
                    <p style="font-size: 12px; margin:0px; padding: 0px;">No Dok : FR-WHS-<?php echo $no ?>-REV.00</p>
                </td>
                <td style="border: 1px solid #000000; width: 40%" rowspan="2">
                    <table style="font-size: 12px;">
                        <tr>
                            <td width="75">Nomer </td>
                            <td> : </td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td> : <?php echo isset($filter['kat']) ? $filter['kat'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Periode</td>
                            <?php
                            if (!empty($filter['tgl_nota'])) {
                                $value = explode(' - ', $filter['tgl_nota']);
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
                            <td>Cetak</td>
                            <td> : <?php echo date('d/m/Y') ?></td>
                        </tr>
                    </table>
                </td>
                <td valign="top" align="center" width="10%" class="border-all" style="height:15px;"><b>Dibuat oleh</b></td>
                <td valign="top" align="center" width="10%" class="border-all"><b>Diperiksa oleh</b></td>
                <td valign="top" align="center" width="10%" class="border-all"><b>Diketahui oleh</b></td>
            </tr>
            <tr>
                <td class="border-all"><br><br></td>
                <td class="border-all"><br><br></td>
                <td class="border-all"><br><br></td>
            </tr>
        </table>
        <?php
    }
    ?>

    <table class="border-all" style="border-collapse: collapse; font-size: 12px; margin-top:-2px;" width="100%">
        <tr>
            <th class="border-all " align="center">TGL</th>
            <th class="border-all " align="center">NO BBM</th>
            <th class="border-all " align="center">KD BRG</th>
            <th class="border-all ">NAMA BARANG</th>
            <th class="border-all " align="center">SAT</th>
            <th class="border-all " align="center">JML</th>
            <th class="border-all " align="center">SURAT JALAN</th>
            <th class="border-all " align="center">PO</th>
            <th class="border-all ">SUPPLIER</th>
            <th class="border-all ">KET</th>
        </tr>
        <?php
//        $sorted = array_orderby($data, 'return_fare', SORT_ASC, 'one_way_fare', SORT_ASC);
        foreach ($models as $key) {
            ?>
            <tr>
                <td valign="top" class="border-all" align="center"><?php echo date("d/m/Y", strtotime($key['tanggal_nota'])) ?></td>
                <td valign="top" class="border-all"><?php echo $key['no_bbm'] ?></td>
                <td valign="top" class="border-all" width="60" align="center"><?php echo $key['kd_barang'] ?></td>
                <td valign="top" class="border-all"><?php echo $key['nm_barang'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['satuan'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['jumlah'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['surat_jalan'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['no_po'] ?></td>
                <td valign="top" class="border-all"><?php echo $key['nama_supplier'] ?></td>
                <td valign="top" class="border-all" width="120"><?php echo $key['keterangan'] ?></td>
            </tr>
            <?php
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