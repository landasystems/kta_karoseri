<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-retur-Barang_Masuk.xls");
}
?>

<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <td rowspan="4" colspan="2" style="font-size: 16px;width:90px;border: 1px solid #000000">
                <br>
        <center><b>LAPORAN RETUR BARANG KELUAR (BBK)</b></center>
        <br>

        </td>
        <td rowspan="4" colspan="4" style="border: 1px solid #000000">
            <table style="font-size:12px;">
                <tr>
                    <td>Nomer</td>
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
                    <td> : <?php echo date('d-m-Y') ?></td>
                </tr>
            </table>
        </td>
        <th style="width: 60px;text-align: center;" class="border-bottom border-right" colspan="2" valign="top">
            Dibuat oleh
        </th>
        <th style="width: 60px;text-align: center;" class="border-bottom border-right" colspan="2" valign="top">
            Diperiksa oleh
        </th>

        </tr>
       <tr>
            <td class="border-all" colspan="2" style="height:60px;"></td>
            <td class="border-all" colspan="2"></td>
        </tr>
        
    </table>

    <table style="margin-top: -2px;border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th class="border-bottom border-right" rowspan='2'>Tanggal</th>
            <th style="text-align: center;" class="border-bottom border-right" colspan='3'>NOMER</th>
            <th style="text-align: center;" class="border-bottom border-right" rowspan='2'>KODE BARANG</th>
            <th style="text-align: center;" class="border-bottom border-right" rowspan='2'>GOLONGAN</th>
            <th class="border-bottom border-right" rowspan='2'>NAMA BARANG</th>
            <th style="text-align: center;" class="border-bottom border-right" rowspan='2'>SAT</th>
            <th style="text-align: center;" class="border-bottom border-right" rowspan='2'>KETERANGAN</th>
        </tr>
        <tr>
            <th style="text-align: center;" class="border-bottom border-right">BBK</th>
            <th style="text-align: center;" class="border-bottom border-right">RETUR</th>
            <th style="text-align: center;" class="border-bottom border-right">NO WO</th>
        </tr>
        <?php
        foreach ($models as $key) {
            ?>
            <tr>
                <td class="border-bottom border-right" style="text-align: center;width: 80px;" valign="top"><?php echo date('d/m/Y', strtotime($key['tanggal'])) ?></td>
                <td class="border-bottom border-right" style="text-align: center;width: 80px;" valign="top"><?php echo $key['no_bbk'] ?></td>
                <td class="border-bottom border-right" style="text-align: center;width: 80px;" valign="top"><?php echo $key['no_retur_bbk'] ?></td>
                <td class="border-bottom border-right" style="text-align: center;width: 80px;" valign="top"><?php echo $key['no_wo'] ?></td>
                <td class="border-bottom border-right" style="text-align: center;width:100px;" valign="top"><?php echo $key['kd_barang'] ?></td>
                <td class="border-bottom border-right" style="text-align: center;" valign="top"><?php echo $key['jenis_brg'] ?></td>
                <td class="border-bottom border-right" valign="top"><?php echo $key['nm_barang'] ?></td>
                <td class="border-bottom border-right" style="text-align: center;" valign="top"><?php echo $key['satuan'] ?></td>
                <td class="border-bottom border-right" valign="top"><?php echo $key['ket'] ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="border-bottom border-right" style="height: 25px;" colspan="9"></td>
        </tr>
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