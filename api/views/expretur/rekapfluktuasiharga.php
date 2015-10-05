<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-Laporan-spp-rutin.xls");
}
?>
<?php
$i = 0;
$data = array();
foreach ($models as $key => $val) {
    $bulan = date('m', strtotime($val['tgl_pengiriman']));

    $data[$bulan]['body'][$i]['bulan'] = $bulan;
    $data[$bulan]['body'][$i]['nota'] = $val['nota'];
    $data[$bulan]['body'][$i]['nama_supplier'] = $val['nama_supplier'];
    $data[$bulan]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$bulan]['body'][$i]['satuan'] = $val['satuan'];
    $data[$bulan]['body'][$i]['hrg_barang'] = $val['hrg_barang'];

    $i++;
}
?>


<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
    <table style="border-collapse: collapse; font-size: 12px;" width="100%" border="1">
        <tr>
            <td class="border-right" rowspan="4" colspan="2" style="width:300px">
                <br>
        <center><b>FLUKTUASI HARGA</b></center>
        <br><br>
        <center>NO DOKUMEN FR-PCH-004 Rev.03</center>
        <br><br>

        </td>
        <td class="border-right" rowspan="4" colspan="3" valign="top">
            <table style="font-size:12px;">
                <tr>
                    <td>Departemen</td>
                    <td>:</td>

                </tr>
                <tr>
                    <td>Periode</td>

                    <td style="width: 250px;">:</td>
                </tr>
                <tr>
                    <td>Hal</td>

                    <td style="width: 250px;">:</td>
                </tr>

            </table>
        </td>
        <td class="border-bottom border-right" style="width:150px; text-align: center">Dibuat</td>
        <td class="border-bottom border-right" style="width:150px; text-align: center">Diketahui</td>
        </tr>
        <tr>
            <td class="border-bottom border-right" rowspan="2"></td>
            <td class="border-bottom border-right" rowspan="2"></td>
        </tr>
        <tr></tr>
        <tr>
            <td style="height: 20px"class="border-bottom border-right"></td>
            <td class="border-bottom border-right"></td>
        </tr>
    </table>
    <table style="margin-top: -2px;border-collapse: collapse; font-size: 12px;" width="100%" border="1">
        <tr>
            <th  class="border-bottom border-right" rowspan="2" style="text-align: center;">No</th>
            <th  class="border-bottom border-right" rowspan="2" style="text-align: center;">SUPPLIER</th>
            <th  class="border-bottom border-right" rowspan="2">NAMA BARANG</th>
            <th  class="border-bottom border-right" rowspan="2" style="text-align: center;">SAT</th>
            <th  class="border-bottom border-right" colspan="12" style="text-align: center; font-size: 16px; color: red;">HARGA BARANG</th>
        </tr>
        <tr>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">JAN</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">FEB</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">MAR</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">APR</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">MEI</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">JUN</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">JUL</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">AGS</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">SEP</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">OKT</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">NOP</td>
            <td style="text-align: center; width: 60px;" class="border-bottom border-right">DES</td>
        </tr>
        <?php
        $no = 1;
        foreach ($data as $datas) {
            foreach ($datas['body'] as $key) {
                if ($key['bulan'] == 01) {
                    ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
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
                        <td class="border-bottom border-right"></td>
                    </tr>

                    <?php
                } else if ($key['bulan'] == 02) {
                    ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
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
        } else if ($key['bulan'] == 03) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
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
        } else if ($key['bulan'] == 04) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
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
        } else if ($key['bulan'] == 05) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>

                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>

                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

            <?php
        } else if ($key['bulan'] == 06) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>

                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

            <?php
        } else if ($key['bulan'] == 07) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

            <?php
        } else if ($key['bulan'] == 08) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

            <?php
        } else if ($key['bulan'] == 09) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

            <?php
        } else if ($key['bulan'] == 10) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

            <?php
        } else if ($key['bulan'] == 11) {
            ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
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
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                        <td class="border-bottom border-right"></td>
                    </tr>

                    <?php
                } else if ($key['bulan'] == 12) {
                    ?>
                    <tr>
                        <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                        <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                        <td  class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
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
                        <td class="border-bottom border-right"></td>
                        <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    </tr>

                    <?php
                }
//            echo $key['bulan'];
                $no++;
            }
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
