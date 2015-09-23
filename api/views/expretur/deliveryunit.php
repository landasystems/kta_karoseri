
<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-Delivery_masuk.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <td colspan="5" rowspan="4" style="border: 1px solid #000000">
                <br>
        <center>
            <b>
                LAPORAN 
                <br> 
                <br> 
                REKAP DELIVERY UNIT
            </b>
        </center>


        </td>
        <td   rowspan="4" style="border: 1px solid #000000">
            <table style="font-size: 12px;">

                <tr>
                    <td>Periode</td>
                    <?php
                    if (!empty($filter['tgl_delivery'])) {
                        $value = explode(' - ', $filter['tgl_delivery']);
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
                    <td> : <?= Yii::$app->landa->date2Ind(date('d-M-Y')) ?></td>
                </tr>
                <tr>
                    <td colspan='2'></td>
                </tr>
            </table>
        </td>
        <td colspan="1" style="width: 100px" class="border-bottom border-right" valign="top">
        <center><b>DIBUAT</b></center>

        </td>
        <td colspan="1" style="width: 100px" class="border-bottom border-right" valign="top">
        <center><b>DIKETAHUI</b></center>

        </td>

        </tr>
        <tr>
            <td style="height:50px" rowspan="2" class="border-top border-right"></td>
            <td style="height:50px"  rowspan="2" class="border-top border-right"></td>
        </tr>
        <tr></tr>
        <tr>
            <td style="height: 15px" class="border-top border-right"></td>
            <td style="height: 15px" class="border-top border-right"></td>
        </tr>

        <?php
        $data = array();
        $i = 0;
        foreach ($models as $key => $val) {
            $data[$val['lokasi_kntr']]['title']['lokasi'] = $val['lokasi_kntr'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['sales'] = $val['nama'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['jenis'] = $val['jenis'];

            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['tgl_delivery'] = $val['tgl_delivery'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['no_delivery'] = $val['no_delivery'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['no_wo'] = $val['no_wo'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['nm_customer'] = $val['nm_customer'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['merk'] = $val['merk'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['tipe'] = $val['tipe'];
            $data[$val['lokasi_kntr']]['sales'][$val['nik']]['jenis'][$val['jenis']]['body'][$i]['model'] = $val['model'];
            $i++;
        }
        ?>

<!--<table style="margin-top: -2px;border-collapse: collapse; font-size: 12px;" width="100%"  border="1">-->
        <tr>
            <th style="text-align:center;">NO</th>
            <th style="text-align:center;">TGL DELV</th>
            <th style="text-align:center;">UNIT</th>
            <th style="text-align:center;">NO DELV</th>
            <th style="text-align:center;">NO WO</th>
            <th style="text-align:center;">CUSTOMER</th>
            <th style="text-align:center;">MERK / TYPE</th>
            <th style="text-align:center;">MODEL</th>
        </tr>
        <?php
        $total = 0;
        foreach ($data as $key) {
            echo '<tr>'
            . '<td class="border-all"></td>'
            . '<td class="border-all back-blue">' . $key['title']['lokasi'] . ' :</td>'
            . '<td class="border-all"></td>'
            . '<td class="border-all"></td>'
            . '<td class="border-all"></td>'
            . '<td class="border-all"></td>'
            . '<td class="border-all"></td>'
            . '<td class="border-all"></td>'
            . '</tr>';

            foreach ($key['sales'] as $val) {
                echo '<tr>'
                . '<td class="border-all"></td>'
                . '<td style="width:100px;" class="border-all back-yellow">' . $val['sales'] . '</td>'
                . '<td class="border-all"></td>'
                . '<td class="border-all"></td>'
                . '<td class="border-all"></td>'
                . '<td class="border-all"></td>'
                . '<td class="border-all"></td>'
                . '<td class="border-all"></td>'
                . '</tr>';


                foreach ($val['jenis'] as $val2) {
                    echo '<tr>'
                    . '<td class="border-all"></td>'
                    . '<td class="border-all back-grey">' . $val2['jenis'] . '</td>'
                    . '<td class="border-all"></td>'
                    . '<td class="border-all"></td>'
                    . '<td class="border-all"></td>'
                    . '<td class="border-all"></td>'
                    . '<td class="border-all"></td>'
                    . '<td class="border-all"></td>'
                    . '</tr>';
                    $no = 0;
                    $jml = 0;
                    foreach ($val2['body'] as $val3) {
                        $no++;
                        $jml++;
                        if (!empty($val3['tgl_delivery'])) {
                            $unit = '1';
                        } else {
                            $unit = '';
                        }
                        echo'<tr>
                        <td class="border-bottom border-right" style="text-align:center">' . $no . '</td>
                        <td class="border-bottom border-right" style="text-align:center">' . date('d/m/Y', strtotime($val3['tgl_delivery'])) . '</td>
                        <td class="border-bottom border-right" style="text-align:center">' . $unit . '</td>
                        <td class="border-bottom border-right" style="text-align:center">' . $val3['no_delivery'] . '</td>
                        <td class="border-bottom border-right" style="text-align:center">' . $val3['no_wo'] . '</td>
                        <td class="border-bottom border-right">' . $val3['nm_customer'] . '</td>
                        <td class="border-bottom border-right">' . $val3['merk'] . '/' . $val3['tipe'] . '</td>
                        <td class="border-bottom border-right">' . $val3['model'] . '</td>
                            </tr>';
                    }
                    $total += $jml;
                    echo'<tr>
                    <th></th>
                    <th style="text-align:center;">TOTAL</th>
                    <th style="text-align:center;">' . $jml . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    </tr>';
                }
            }
        }
        echo'<tr>
         <th></th>
         <th style="text-align:center;">GRAND TOTAL</th>
         <th style="text-align:center;">' . $total . '</th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         </tr>';
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
