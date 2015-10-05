<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
//    header("Content-Disposition: attachment; filename=excel-Laporan-spp-rutin.xls");
}
?>
<?php
$i = 0;
$data = array();
foreach ($models as $key => $val) {
    $bulan = date('mm', strtotime($val['tgl_pengiriman']));

    $data[$bulan]['bulan'] = $bulan;
    $data[$bulan]['nama_supplier'] = $val['nama_supplier'];
    $data[$bulan]['nm_barang'] = $val['nm_barang'];
    $data[$bulan]['satuan'] = $val['satuan'];
    $data[$bulan]['hrg_barang'] = $val['hrg_barang'];

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
            <th rowspan="2" style="text-align: center;">No</th>
            <th rowspan="2" style="text-align: center;">Supplier</th>
            <th rowspan="2">Nama Barang</th>
            <th rowspan="2" style="text-align: center;">Sat</th>
            <th colspan="12" style="text-align: center;">Harga Barang</th>
        </tr>
        <tr>
            <td>JAN</td>
            <td>FEB</td>
            <td>MAR</td>
            <td>APR</td>
            <td>MEI</td>
            <td>JUN</td>
            <td>JUL</td>
            <td>AGS</td>
            <td>SEP</td>
            <td>OKT</td>
            <td>NOP</td>
            <td>DES</td>
        </tr>
        <?php
        $no = 1;
        foreach ($data as $key) {
            if ($key['bulan'] == 01) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 02) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 03) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td></td>
                    <td class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 04) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 05) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                     <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                   
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 06) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 07) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                     <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                   <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 08) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 09) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                     <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                   
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 10) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 11) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                    <td></td>
                </tr>

                <?php
                
            }
            else if ($key['bulan'] == 02) {
                ?>
                <tr>
                    <td  style="text-align: center;" class="border-bottom border-right" valign="top">&nbsp;<?= $no; ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['nama_supplier'] ?></td>
                    <td  class="border-bottom border-right" valign="top">&nbsp;<?= $key['nm_barang'] ?></td>
                    <td  class="border-bottom border-right" valign="top"><?= $key['satuan']; ?></td>
                     <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td  class="border-bottom border-right" valign="top" style="text-align: center;"><?= $key['hrg_barang']; ?></td>
                  </tr>

                <?php
                
            }
            echo $key['bulan'];
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
