<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
//    header("Content-Disposition: attachment; filename=excel-laporan-kekurangan-pemenuhan-spp.xls");
}

if (!empty($filter['tgl_periode'])) {
    $value = explode(' - ', $filter['tgl_periode']);
    $start = date("d-m-Y", strtotime($value[0]));
    $end = date("d-m-Y", strtotime($value[1]));
    $hasil = $start . ' - ' . $end;
} else {
    $start = '';
    $end = '';
    $hasil = '';
}

//print_r($models);
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table  style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
        <tr>
            <td  class="border-right border-bottom"  rowspan="4" colspan="2">
                <br>
        <center><b>LAMPIRAN KEKURANGAN PEMENUAHAN SPP</b></center>
        <br><center>Periode  : <?= $hasil ?></center>
        <br>
        <center>No Dokumen :  FR-INV-024 Rev. 0</center>
        <br>

        </td>
        <td style="width:230px" class="border-right border-bottom"  rowspan="4" colspan="3" valign="top">

        </td>
        <td class="border-right border-bottom" style="text-align: center; width: 100px;">Dibuat Oleh</td>
        <td class="border-right border-bottom" style="text-align: center; width: 100px;">Diperiksa Oleh</td>
        <td class="border-right border-bottom" style="text-align: center; width: 100px;">Diketahui Oleh</td>
        </tr>
        <tr>
            <td class="border-right border-bottom" style="text-align: center;" rowspan="2"></td>
            <td class="border-right border-bottom" style="text-align: center;" rowspan="2"></td>
            <td class="border-right border-bottom" style="text-align: center;"rowspan="2"></td>
        </tr>
        <tr></tr>
    </table>

    <table  style="margin-top:-2px;border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
        <tr>
            <th class="border-bottom border-right"rowspan="2" style="text-align: center">No. Spp</th>
            <th class="border-bottom border-right" rowspan="2">Kode Barang</th>
            <th class="border-bottom border-right" rowspan="2">Nama Barang</th>
            <th class="border-bottom border-right" rowspan="2" style="text-align: center">Satuan</th>
            <th class="border-bottom border-right" rowspan="2" style="text-align: center">QTY</th>
            <th class="border-bottom border-right" rowspan="2" >Keteranagan</th>
            <th class="border-bottom border-right" rowspan="2" style="text-align: center">Re - SCH</th>
            <th class="border-bottom border-right" colspan="3" style="text-align: center">Realisasi</th>
        </tr>
        <tr>

            <th class="border-bottom border-right" style="text-align: center" width="10%">1</th>
            <th class="border-bottom border-right" style="text-align: center" width="10%">2</th>
            <th class="border-bottom border-right" style="text-align: center" width="10%">3</th>
        </tr>

        <?php
        foreach ($models as $val) {

            $poBbm = new yii\db\Query;
            $poBbm->from('trans_po')
                    ->join('JOIN', 'detail_po', ' trans_po.nota = detail_po.nota')
                    ->join('RIGHT JOIN', 'trans_bbm', 'trans_bbm.no_po = trans_po.nota')
                    ->join('RIGHT JOIN', 'det_bbm', 'det_bbm.kd_barang = detail_po.kd_barang and det_bbm.no_bbm = trans_bbm.no_bbm')
                    ->select("det_bbm.jumlah as jumlah_bbm, (" . $val['jumlah_spp'] . " - det_bbm.jumlah) as selisih")
                    ->where('trans_po.spp = "' . $val['no_spp'] . '" and detail_po.kd_barang = "' . $val['kd_barang'] . '"');
            $command = $poBbm->createCommand();
            $model = $command->queryAll();

            if (!empty($model)) {

                foreach ($model as $value) {
                    if($value['selisih'] > 0){
                    ?>
                    <tr>
                        <td style="text-align: center" class="border-bottom border-right"><?= $val['no_spp'] ?></td>
                        <td class="border-bottom border-right"><?= $val['kode_barang'] ?></td>
                        <td class="border-bottom border-right"> <?= $val['nm_barang'] ?></td>
                        <td style="text-align: center" class="border-bottom border-right"><?= $val['satuan'] ?></td>
                        <td style="text-align: center" class="border-bottom border-right"><?= $value['selisih']?></td>
                        <td class="border-bottom border-right"><?= $val['ket'] ?></td>
                        <td class="border-bottom border-right" style="width: 50px;"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>
                    <?php
            }
                }
            }
//            else if(empty ($models)){
//                 ?>
<!--                    <tr>
                        <td style="text-align: center" class="border-bottom border-right"><?= $val['no_spp'] ?></td>
                        <td class="border-bottom border-right"><?= $val['kode_barang'] ?></td>
                        <td class="border-bottom border-right"> <?= $val['nm_barang'] ?></td>
                        <td style="text-align: center" class="border-bottom border-right"><?= $val['satuan'] ?></td>
                        <td style="text-align: center" class="border-bottom border-right"><?= $val['jumlah_spp']?></td>
                        <td class="border-bottom border-right"><?= $val['ket'] ?></td>
                        <td class="border-bottom border-right" style="width: 50px;"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>-->
                    <?php
//            }
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