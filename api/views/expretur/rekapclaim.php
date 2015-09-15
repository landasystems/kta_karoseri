<?php
if ($_GET['excel'] == 'ex') {
    header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-Claim.xls");
}
//echo $_SERVER['PHP_SELF'];
?>

<body>
    <!--    <h3>PT. KARYA TUGAS ANDA</h3>
        Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
        <br>
        Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
        <hr>
        <br>
    <center><b>REKAP CLAIM UNIT</b></center>
    <br><br>-->
    <script>
        window.addEventListener('load', function () {
            var rotates = document.getElementsByClassName('rotate');
            for (var i = 0; i < rotates.length; i++) {
                rotates[i].style.height = rotates[i].offsetWidth + 'px';
            }
        });
    </script> 
    <style>
        .no_border{
            border-top: none;
            border-bottom: none;
        }
        .no_border_LR{
            border-left: none;
            border-right:  none;
        }
        .rotate {
            /* FF3.5+ */
            -moz-transform: rotate(-90.0deg);
            /* Opera 10.5 */
            -o-transform: rotate(-90.0deg);
            /* Saf3.1+, Chrome */
            -webkit-transform: rotate(-90.0deg);
            /* IE6,IE7 */
            /* IE8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
            /* Standard */
            transform: rotate(-90.0deg);   
        }
        .tds {
            border-collapse:collapse;
            border: 1px black solid;
        }
        trs:nth-of-type(5) td:nth-of-type(1) {
            visibility: hidden;
        }
    </style>
    <!--<script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>-->

    <?php
    $data = array();
    $i = 0;

    foreach ($models as $key => $val) {

        $data[$val['no_wo']]['title']['no_wo'] = $val['no_wo'];
        $data[$val['no_wo']]['title']['model'] = $val['model'];
        $data[$val['no_wo']]['title']['nm_customer'] = $val['nm_customer'];
        $data[$val['no_wo']]['title']['lokasi_kntr'] = $val['lokasi_kntr'];
        $data[$val['no_wo']]['title']['nama'] = $val['nama'];

        $data[$val['no_wo']]['stat'][$val['stat']]['title'] = $val['stat'];

        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['totalby_mat'] = $val['biaya_mat'];
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['totalby_tk'] = $val['biaya_tk'];
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['totalby_spd'] = $val['biaya_spd'];
//
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['tgl'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl'] . $val['tgl'] . '<br>' : $val['tgl'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['tgl_pelaksanaan'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl_pelaksanaan']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl_pelaksanaan'] . $val['tgl_pelaksanaan'] . '<br>' : $val['tgl_pelaksanaan'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['pelaksana'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['pelaksana']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['pelaksana'] . $val['pelaksana'] . '<br>' : $val['pelaksana'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['jns_komplain'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['jns_komplain']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['jns_komplain'] . '- ' . $val['jns_komplain'] . '<br>' : '- ' . $val['jns_komplain'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['problem'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['problem']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['problem'] . '- ' . $val['problem'] . '<br>' : '- ' . $val['problem'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['solusi'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['solusi']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['solusi'] . '- ' . $val['solusi'] . '<br>' : '- ' . $val['solusi'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['biaya_mat'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_mat']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_mat'] . $val['biaya_mat'] . '<br>' : $val['biaya_mat'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['biaya_tk'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_tk']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_tk'] . $val['biaya_tk'] . '<br>' : $val['biaya_tk'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['biaya_spd'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_spd']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['biaya_spd'] . $val['biaya_spd'] . '<br>' : $val['biaya_spd'] . '<br>';
        $data[$val['no_wo']]['stat'][$val['stat']]['body'][$i]['total'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['total']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['total'] . ($val['biaya_mat'] + $val['biaya_tk'] + $val['biaya_spd']) . '<br>' : ($val['biaya_mat'] + $val['biaya_tk'] + $val['biaya_spd']) . '<br>';


        $i++;
    }
    ?>
    <link rel="stylesheet" href="../../../css/print.css" type="text/css" />
    <div style="width:26cm">
        <!--<div>-->
        <table border="1" style="border-collapse: collapse; font-size: 12px;" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td rowspan="4" colspan="4" class="border-bottom border-right">
                    <br>
            <center><b>REKAP DATA CLAIM</b></center>
            <br><br>
            <center>NO DOKUMEN : FR-SS-018</center>
            <br><br>

            </td>
            <td rowspan="4" colspan="6" valign="top">
                <table>

                    <tr>
                        <td>Periode</td>
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
                        <td>Tanggal</td>
                        <td>: <?= date('d/m/Y') ?></td>

                    </tr>
                </table>
            </td>
            <th colspan="2" style="text-align: center">DIBUAT</th>
            <th colspan="2" style="text-align: center">DIPERIKSA</th>
            <th colspan="2" style="text-align: center">DIKETAHUI</th>
            </tr>
            <tr>
                <td class="border-bottom border-right" colspan="2" rowspan="3"></td>
                <td class="border-bottom border-right" colspan="2" rowspan="3"></td>
                <td class="border-bottom border-right" colspan="2" rowspan="3"></td>
            </tr>

        </table>
        <table border="1" style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <th align="center" style="font-size: 12px;">No</th>
                <th align="center" style="font-size: 12px;">No WO</th>
                <th align="center" style="font-size: 12px;">Model</th>
                <th align="center" style="font-size: 12px;">Customer</th>
                <th align="center" style="font-size: 12px;">Area</th>
                <th align="center" style="font-size: 12px;">Sales</th>
                <th width="60px">Tgl Claim</th>
                <th align="center">Tgl Penanganan</th>
                <th>Pelakasana</th>
                <th>Jenis Komplain</th>
                <th>Problem</th>
                <th>Perubahan yg dilakukan</th>
                <th align="center">Biaya Material</th>
                <th align="center">Biaya TK</th>
                <th align="center">Biaya SPD</th>
                <th align="center">Biaya Total</th>
            </tr>
            <?php
            $n = 1;
            $biaya_mat = 0;
            $biaya_tk = 0;
            $biaya_spd = 0;
            foreach ($data as $key) {
                ?>
                <tr>
                    <td class="border-bottom border-right" valign="top"><?= $n ?></td>
                    <td class="border-bottom border-right" valign="top"><?php echo $key['title']['no_wo'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?php echo $key['title']['model'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?php echo $key['title']['nm_customer'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?php echo $key['title']['lokasi_kntr'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?php echo $key['title']['nama'] ?></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                    <td class="border-bottom border-right"></td>
                </tr>
                <?php
                foreach ($key['stat'] as $value) {
                    ?>
                    <tr>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right" valign="top"><b><?= $value['title'] ?></b></td>
                        <td class="border-bottom border-right""></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>
                    <?php
                    foreach ($value['body'] as $val) {
                        ?>
                        <tr>
                            <td class="border-bottom border-right"></td>
                            <td class="border-bottom border-right"></td>
                            <td class="border-bottom border-right"></td>
                            <td class="border-bottom border-right"></td>
                            <td class="border-bottom border-right"></td>
                            <td class="border-bottom border-right"></td>
                            <td class="border-bottom border-right"><?= $val['tgl'] ?></td>
                            <td class="border-bottom border-right"><?= $val['tgl_pelaksanaan'] ?></td>
                            <td class="border-bottom border-right"><?= $val['pelaksana'] ?></td>
                            <td class="border-bottom border-right"><?= $val['jns_komplain'] ?></td>
                            <td class="border-bottom border-right"><?= $val['problem'] ?></td>
                            <td class="border-bottom border-right"><?= $val['solusi'] ?></td>
                            <td class="border-bottom border-right" style="text-align: right">&nbsp;<?= $val['biaya_mat'] ?></td>
                            <td  class="border-bottom border-right" style="text-align: right">&nbsp;<?= $val['biaya_tk'] ?></td>
                            <td  class="border-bottom border-right" style="text-align: right">&nbsp;<?= $val['biaya_spd'] ?></td>
                            <td  class="border-bottom border-right" style="text-align: right">&nbsp;<?= $val['total'] ?></td>
                        </tr>
                        <?php
                        $biaya_mat += $val['totalby_mat'];
                        $biaya_tk += $val['totalby_tk'];
                        $biaya_spd += $val['totalby_spd'];
                    }
                }

                $n++;
            }
            ?>
            <td style="border-right: none;"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR">TOTAL</td>
            <td class="no_border_LR"></td>
            <td class="no_border_LR" style="text-align: right">&nbsp;<?= $biaya_mat ?></td>
            <td  class="no_border_LR"style="text-align: right">&nbsp;<?= $biaya_tk ?></td>
            <td  class="no_border_LR"style="text-align: right">&nbsp;<?= $biaya_spd ?></td>
            <td style="border-left: none;"></td>

        </table>
    </div>
</body>


<?php
if ($_GET['excel'] == 'print') {
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