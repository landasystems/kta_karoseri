<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-barang-masuk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table class="border-all" style="border-collapse: collapse; font-size: 12px;" width="100%">
        <tr>
            <td rowspan="2" style="border: 1px solid #000000; font-size: 18px; width:35%" valign="middle" align="center">
                <br>
                <b>LAPORAN RETUR BARANG MASUK</b>
                <b>(BBM)</b>
                <br>
            </td>
            <td rowspan="2" style="border: 1px solid #000000" valign="top">
                <table style="border-collapse: collapse; font-size: 12px;">
                    <tr>
                        <td width="100">Nomer</td>
                        <td> : </td>
                    </tr>
                    <tr>
                        <td>Periode</td>
                        <?php
                        if (!empty($filter['tanggal'])) {
                            $value = explode(' - ', $filter['tanggal']);
                            $start = date("d-m-Y", strtotime($value[0]));
                            $end = date("d-m-Y", strtotime($value[1]));
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
            <td style="border: 1px solid #000000; height:15px; width:15%" valign="top" align="center" class="border-all">
                <b>Dibuat oleh</b>
            </td>
            <td style="border: 1px solid #000000;width:15%" valign="top" align="center" class="border-all">
                <b>Diperiksa oleh</b>
            </td>
        </tr>
        <tr>
            <td class="border-all" height="80"></td>
            <td class="border-all"></td>
        </tr>
    </table>

    <table class="border-all" style="border-collapse: collapse; font-size: 12px; margin-top:-2px;" width="100%">
        <tr>
            <th rowspan='2' class="border-all" align="center">TANGGAL</th>
            <th rowspan='2' class="border-all" align="center">KODE BARANG</th>
            <th rowspan='2' class="border-all" align="center">GOLONGAN</th>
            <th rowspan='2' class="border-all" align="center">NAMA BARANG</th>
            <th rowspan='2' class="border-all" align="center">SAT</th>
            <th colspan='3' class="border-all" align="center">NOMER</th>
            <th rowspan='2' class="border-all" align="center">KET</th>
        </tr>
        <tr>
            <th class="border-all" align="center">BBM</th>
            <th class="border-all" align="center">RETUR</th>
            <th class="border-all" align="center">SURAT JALAN</th>
        </tr>
        <?php
        $no = 0;
        foreach ($models as $key) {
            $no++;
            ?>
            <tr>
                <td valign="top" class="border-all"><?php echo date('d m Y', strtotime($key['tanggal'])) ?></td>
                <td valign="top" class="border-all"><?php echo $key['kd_barang'] ?></td>
                <td valign="top" class="border-all"><?php echo $key['jenis_brg'] ?></td>
                <td valign="top" class="border-all"><?php echo $key['nm_barang'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['satuan'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['no_bbm'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['no_retur_bbm'] ?></td>
                <td valign="top" class="border-all" align="center"><?php echo $key['surat_jalan'] ?></td>
                <td valign="top" class="border-all"><?php echo $key['ket'] ?></td>
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