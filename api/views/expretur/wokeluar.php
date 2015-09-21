<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-wokeluar.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <?php
    if (isset($_GET['print'])) {
        ?>
        <table>
            <tr>
                <td width="80"><img src="../../../img/logo.png"></td>
                <td valign="top">
                    <b style="font-size: 18px; margin:0px; padding:0px;">PT KARYA TUGAS ANDA</b>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Jl. Raya Sukorejo No. 1 Sukorejo 67161, Pasuruan Jawa Timur</p>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com</p>
                </td>
            </tr>
        </table>
        <hr>
        <?php
    }
    ?>


    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <td colspan="2" style="border: 1px solid #000000">
                <br>
        <center><b>LAPORAN UNIT KELUAR</b></center>
        <br>
        <center>No Dokumen : FR-PPC-017 Rev 00</center>
        <center>Applicable To Realisasi OI & Budget Opname</center>
        <br>

        </td>
        <td colspan="4" style="border: 1px solid #000000">
            <table>
                <tr>
                    <td>Nomer</td>
                    <td> : </td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <?php
                    if (!empty($filter['tgl_terima'])) {
                        $value = explode(' - ', $filter['tgl_terima']);
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
        <td  style="border: 1px solid #000000" valign="top">
        <center><b>Dibuat oleh</b></center>

        </td>
        <td  style="border: 1px solid #000000" valign="top">
        <center><b>Diperiksa oleh</b></center>

        </td>

        </tr>
    </table>
    <?php
    $data = array();
    $i = 0;
    foreach ($models as $key => $val) {
        $data[$val['nm_customer']]['title']['pro'] = $val['provinsi'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['customer'] = $val['nm_customer'];

        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['jml_unit'] = $val['jml_unit'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_wo'] = $val['no_wo'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['model'] = $val['model'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['merk'] = $val['merk'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tipe'] = $val['tipe'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nama'] = $val['nama'];
        $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl_keluar'] = $val['tgl_keluar'];
        $i++;
    }
    ?>
    <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <th>IN CHASIS</th>
            <th>UNIT</th>
            <th>NO WO</th>
            <th >MODEL</th>
            <th>MERK TYPE</th>
            <th>SALES</th>
            <th>OUT</th>
            <th>KETERANGAN</th>
        </tr>
        <?php
        $jml = 1;

        $grandtotal = 0;
        foreach ($data as $keys) {
            ?>
            <tr><td colspan="8" style="text-align: left;background-color: bisque;color:#000;"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

            <?php
            $total = 0;
            foreach ($keys['customer'] as $val1) {
                echo'<tr><td class="border-all" colspan="8" style="text-align: left;background-color: darkkhaki;">' . $val1['customer'] . '</td></tr>';
                foreach ($val1['body'] as $val) {
                    $total += $jml;
                    ?>
                    <tr>
                        <td class="border-all"><?= Yii::$app->landa->date2Ind($val['tgl_terima']); ?>&nbsp;</td>
                        <td class="border-all"><center>1</center></td>
                    <td class="border-all"><?= $val['no_wo']; ?></td>
                    <td class="border-all"><?= $val['model']; ?></td>
                    <td class="border-all"><?= $val['merk']; ?> <?= $val['tipe']; ?></td>
                    <td class="border-all"><?= $val['nama']; ?></td>
                    <td class="border-all"><?= Yii::$app->landa->date2Ind($val['tgl_keluar']); ?>&nbsp;</td>
                    <td class="border-all"></td>

                    </tr>
                    <?php
                }
                echo'<tr>
                <th> Total</th>
                <th>' . $total . ' </th>
                <th colspan="7"> </th>
                </tr>';
                echo'<tr>
                <th colspan="9">&nbsp;</th>
                </tr>';
                $grandtotal += $total;
            }
        }
        echo'<tr>
                <th>Grand Total</th>
                <th>' . $grandtotal . '</th>
                    <th colspan="7"> </th>
                </tr>';
        ?>
    </table>
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