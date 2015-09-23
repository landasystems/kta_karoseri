
<?php
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

    <table  style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
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

            <th class="border-bottom border-right" style="text-align: center">1</th>
            <th class="border-bottom border-right" style="text-align: center">2</th>
            <th class="border-bottom border-right" style="text-align: center">3</th>
        </tr>
    
        <?php
        foreach ($models as $val) {
            if ($val['selisih'] > 0) {
                ?>
                <tr>
                    <td style="text-align: center" class="border-bottom border-right"><?= $val['no_spp'] ?></td>
                    <td class="border-bottom border-right"><?= $val['kd_barang'] ?></td>
                    <td class="border-bottom border-right"> <?= $val['nm_barang'] ?></td>
                    <td style="text-align: center" class="border-bottom border-right"><?= $val['satuan'] ?></td>
                    <td style="text-align: center" class="border-bottom border-right"><?= $val['selisih'] ?></td>
                    <td class="border-bottom border-right"><?= $val['ket'] ?></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>