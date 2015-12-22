<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=rekap-wip.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; font-size: 10px;" width="100%"  border="1">
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
            <br>
    <center><b>WORK IN PROCESS</b></center>
    <br>
    <center>Kode Dokumen : FR-PPC-012 Rev.00</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table style="border-collapse: collapse; font-size: 10px; margin-left: -1px" width="100%">

            <tr>
                <td style="border-bottom: 1px solid #000">Periode</td>
                <?php
                if (!empty($filter['tgl'])) {
                    $value = explode(' - ', $filter['tgl']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td style="border-bottom: 1px solid #000"> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000">Cetak</td>
                <td style="border-bottom: 1px solid #000"> : <?php echo date('d F Y') ?></td>
            </tr>

        </table>
    </td>
    <td  style="border: 1px solid #000000;width: 15%" valign="top">
    <center><b>Dibuat oleh</b></center>
    <hr style="border: 1px solid #000000">

    </td>
    <td  style="border: 1px solid #000000;width: 15%" valign="top">
    <center><b>Diperiksa oleh</b></center>
    <hr style="border: 1px solid #000000">


    </tr>
</table>
<?php
$data = array();
$start = array();
$end = array();
$i = 0;
foreach ($models as $key => $val) {
    if (!empty($val['act_start']) && $val['act_start'] != "-" && isset($val['act_start'])) {
        $tgPS = explode('/', $val['act_start']);
        $isips = $tgPS[2] . '-' . $tgPS[1] . '-' . $tgPS[0];
    } else {
        $isips = "";
    }
    // hitung umur
    // memecah string tanggal awal untuk mendapatkan
    // tanggal, bulan, tahun
    if(!empty($isips)){
    $pecah1 = explode("-", $isips);
    $date1 = $pecah1[2];
    $month1 = $pecah1[1];
    $year1 = $pecah1[0];

    // memecah string tanggal akhir untuk mendapatkan
    // tanggal, bulan, tahun
    $pecah2 = explode("-", date('Y-m-d'));
    $date2 = $pecah2[2];
    $month2 = $pecah2[1];
    $year2 = $pecah2[0];

    // mencari total selisih hari dari tanggal awal dan akhir
    $jd1 = GregorianToJD($month1, $date1, $year1);
    $jd2 = GregorianToJD($month2, $date2, $year2);

    $umur = $jd2 - $jd1;
    }else{
       $umur = '-'; 
    }

    $data[$val['no_wo']]['nowo'] = $val['no_wo'];
    $data[$val['no_wo']]['merk'] = $val['merk'] . $val['tipe'];
    $data[$val['no_wo']]['in_chassis'] = $val['tgl_terima'];
    $data[$val['no_wo']]['in_spk'] = $val['tgl'];
    $data[$val['no_wo']]['in_prod'] = (!empty($val['act_start'])) ? $val['act_start'] : '-';
    $data[$val['no_wo']]['umur'] = $umur;
    $data[$val['no_wo']]['kontrak'] = $val['jml_hari'];
    $data[$val['no_wo']]['customer'] = $val['nm_customer'];

    $detwip = app\models\Wip::findAll(['no_wo' => $val['no_wo']]);
    foreach ($detwip as $r) {
        $data[$val['no_wo']][$r['kd_kerja']] = $r['hasil'];
        $hk[$val['no_wo']][$r['kd_kerja']] = $r['hk'];
        $end[$val['no_wo']][$r['kd_kerja']] = $r['act_finish'];
    }
//    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['kd_titipan'] = $val['kd_titipan'];
    $i++;
}
?>
<table style="border-collapse: collapse; font-size: 9px;" width="100%"  border="1">
    <thead>
        <tr >
            <th class="border-all" style="font-size:8px">NO</th>
            <th class="border-all">NO WO</th>
            <th class="border-all">MERK/TYPE</th>
            <th class="border-all">IN CHASSIS</th>
            <th class="border-all">IN SPK</th>
            <th class="border-all">IN PRODUCTION</th>
            <th class="border-all">AGE</th>
            <th class="border-all">KONTRAK</th>
            <th class="border-all">CUSTOMER</th>
            <th class="border-all">CHASSIS</th>
            <th class="border-all">S/O</th>
            <th class="border-all">LANTAI</th>
            <th class="border-all">KOMP</th>
            <th class="border-all">HK</th>
            <th class="border-all">BODY</th>
            <th class="border-all">HK</th>
            <th class="border-all">PRIMER</th>
            <th class="border-all">HK</th>
            <th class="border-all">DEMPUL</th>
            <th class="border-all">HK</th>
            <th class="border-all">PRA. PAINT</th>
            <th class="border-all">HK</th>
            <th class="border-all">PAINTING</th>
            <th class="border-all">TRIM</th>
            <th class="border-all">HK</th>
            <th class="border-all">PDC</th>
            <th class="border-all">KET</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 0;

      
        foreach ($data as $s) {
//            $deliver = \app\models\Delivery::find()->where('no_wo="' . $s['nowo'] . '"')->one();
//            if ($deliver['no_wo'] == $s['nowo']) {
//                $warna = 'class="back-grey"';
//            } else {
//                $warna = '';
//            }
            $no++;
            

            echo'<tr>
                 <td class="border-all" style="font-size:8px">' . $no . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['nowo'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['merk'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['in_chassis'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['in_spk'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['in_prod'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['umur'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['kontrak'] . '</td>
                 <td class="border-all" style="font-size:8px">' . $s['customer'] . '</td>
                 <td class="border-all" style="font-size:8px"></td>
                <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG001']) ? $data[$s['nowo']]['BAG001'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG004']) ? $data[$s['nowo']]['BAG004'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG002']) ? $data[$s['nowo']]['BAG002'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px;text-align: center;">' . (isset($hk[$s['nowo']]['BAG002']) ? $hk[$s['nowo']]['BAG002'] : '') . '</td>
                     
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG006']) ? $data[$s['nowo']]['BAG006'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px;text-align: center;">' . (isset($hk[$s['nowo']]['BAG006']) ? $hk[$s['nowo']]['BAG006'] : '') . '</td>
                     
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG007']) ? $data[$s['nowo']]['BAG007'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px;text-align: center;">' . (isset($hk[$s['nowo']]['BAG007']) ? $hk[$s['nowo']]['BAG007'] : '') . '</td>
                 
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG009']) ? $data[$s['nowo']]['BAG009'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px;text-align: center;">' . (isset($hk[$s['nowo']]['BAG009']) ? $hk[$s['nowo']]['BAG009'] : '') . '</td>
                 
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG0010']) ? $data[$s['nowo']]['BAG0010'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px;text-align: center;">' . (isset($hk[$s['nowo']]['BAG010']) ? $hk[$s['nowo']]['BAG010'] : '') . '</td>
                 <td class="border-all" style="font-size:8px"></td>
                 <td class="border-all" style="font-size:8px">' . (isset($data[$s['nowo']]['BAG0010']) ? $data[$s['nowo']]['BAG0010'] . ' %' : '') . '</td>
                 <td class="border-all" style="font-size:8px;text-align: center;">' . (isset($hk[$s['nowo']]['BAG010']) ? $hk[$s['nowo']]['BAG010'] : '') . '</td>
                     <td class="border-all" style="font-size:8px"></td>
                 <td class="border-all" style="font-size:8px"></td>';

            echo' </tr>
                 ';
        }
        ?>
    </tbody>
</table>
        <?php
        if (isset($_GET['print'])) {
            ?>
                    <!--    <style>
                            @media print
                            {
                                table { page-break-after:auto }
                                   td    { page-break-inside:avoid; page-break-after:auto }
                                thead { display:table-header-group }
                                tfoot { display:table-footer-group }
                            }
                        </style>-->
    <script type="text/javascript">
        window.print();
        setTimeout(function() {
            window.close();
        }, 1);
    </script>
    <?php
}
?>