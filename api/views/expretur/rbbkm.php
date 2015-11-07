<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-Barang_Keluar.xls");
}

$data = array();
foreach ($models as $key => $val) {
    $data[$val['kd_barang']]['kd_barang'] = $val['kd_barang'];
    $data[$val['kd_barang']]['nm_barang'] = $val['nm_barang'];
    $data[$val['kd_barang']]['satuan'] = $val['satuan'];
    $data[$val['kd_barang']]['jml'] = isset($data[$val['kd_barang']]['jml']) ? $data[$val['kd_barang']]['jml'] + $val['jml'] : $val['jml'];
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <?php
    if (isset($_GET['print'])) {
        ?>
        <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
            <tr>
                <th rowspan="2" colspan="3" style="width:30%; border: 1px solid #000000; padding-bottom:5px; padding-top:5px;">
            <h3 style="font-size: 16px;"><b><center>LAPORAN BARANG KELUAR MINGGUAN</center></b></h3>
            </th>
            <th rowspan="2" colspan="4" style="border: 1px solid #000000">
            <table style="font-size: 12px;">
                <tr>
                    <td>Periode</td>
                    <?php
                    if (!empty($filter['tgl_periode'])) {
                        $value = explode(' - ', $filter['tgl_periode']);
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
            </th>
            <th colspan="1" style="text-align: center; width:110px;height:15px;">Dibuat oleh</th>
            <th colspan="1" style="text-align: center;width:110px;">Diperiksa</th>
            <th colspan="1" style="text-align: center;width:110px;">Diketahui</th>
            </tr>
            <tr>
                <td class="border-bottom border-right" colspan="1" rowspan="1"><br><br></td>
                <td class="border-bottom border-right" colspan="1" rowspan="1"></td>
                <td class="border-bottom border-right" colspan="1" rowspan="1"></td>
            </tr>
        </table>
        <?php
    }
    ?>

    <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px; margin-top:-2px;">
        <tr>
            <th style="text-align: center;">NO</th>

            <th>KD BARANG</th>
            <th>NAMA BARANG</th>
            <th style="text-align: center;">SATUAN</th>
            <th style="text-align: center;">JUMLAH</th>
            <th style="text-align: center;">KETERANGAN</th>
        </tr>
        <?php
        $no = 1;
        $sorted = Yii::$app->landa->array_orderby($data, 'nm_barang', SORT_ASC);
        foreach ($sorted as $key) {
            ?>
            <tr>

                <td class="border-all" valign="top">&nbsp;<?= $no; ?></td>
                <td class="border-all" valign="top" width="100px">&nbsp;<?php echo $key['kd_barang'] ?></td>
                <td class="border-all" valign="top"><?php echo $key['nm_barang'] ?></td>
                <td class="border-all" style="text-align: center;" valign="top"><?php echo $key['satuan'] ?></td>
                <td class="border-all" style="text-align: center;"valign="top">&nbsp;<?php echo $key['jml'] ?></td>
                <td class="border-all" style="width:20%"></td>
            </tr>
            <?php
            $no++;
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
