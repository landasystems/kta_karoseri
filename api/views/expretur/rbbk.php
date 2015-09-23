<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-Barang_Keluar.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
      <table style="border-collapse: collapse; font-size: 12px;" width="100%"  border="1">
        <tr>
            <td rowspan="2" colspan="3" style="width:30%; border: 1px solid #000000; padding-bottom:5px; padding-top:5px;">
                <h3 style="font-size: 16px;"><b><center>LAPORAN BARANG KELUAR</center>
                    <center>BBK</center></b></h3>
                No Dok : FR-WHS-015-REV.00
            </td>

            <td rowspan="2" colspan="4" style="border: 1px solid #000000">
                <table style="font-size: 12px;">
                    <tr>
                        <td width="100">Nomer</td>
                        <td> : </td>
                    </tr>
                    <tr>
                        <td>Kategori</td>
                        <td> : </td>
                    </tr>
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
            </td>

            <th colspan="1" style="text-align: center; width:110px;height:15px;">Dibuat oleh</th>
            <th colspan="1" style="text-align: center;width:110px;">Diperiksa oleh</th>
            <th colspan="1" style="text-align: center;width:110px;">Diketahui oleh</th>
        </tr>
        <tr>
            <td class="border-bottom border-right" colspan="1" rowspan="1"><br><br></td>
            <td class="border-bottom border-right" colspan="1" rowspan="1"></td>
            <td class="border-bottom border-right" colspan="1" rowspan="1"></td>
        </tr>
    </table>

    <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px; margin-top:-2px;">
        <tr>
            <th style="text-align: center;">TANGGAL</th>
            <th style="text-align: center;">NO WO</th>
            <th style="text-align: center;">NO BBK</th>
            <th style="text-align: center;">BAGIAN</th>
            <th style="text-align: center;">PLK KERJA</th>
            <th>KODE BARANG</th>
            <th>NAMA BARANG</th>
            <th style="text-align: center;">SAT</th>
            <th style="text-align: center;">JML</th>
            <th>KET</th>
        </tr>
        <?php
        foreach ($models as $key) {
            ?>
            <tr>
                <td class="border-right" style="text-align: center;" valign="top"><?php echo date('d/m/y', strtotime($key['tanggal'])) ?></td>
                <td class="border-right" style="text-align: center;" valign="top" width="90px">&nbsp;<?php echo $key['no_wo'] ?></td>
                <td class="border-right" style="text-align: center;" valign="top" >&nbsp;<?php echo $key['no_bbk'] ?></td>
                <td class="border-right" valign="top" width="100px"><?php echo $key['jabatan'] ?></td>
                <td class="border-right" valign="top">&nbsp;<?php echo $key['nama'] ?></td>
                <td class="border-right" valign="top" width="100px">&nbsp;<?php echo $key['kd_barang'] ?></td>
                <td class="border-right" valign="top"><?php echo $key['nm_barang'] ?></td>
                <td class="border-right" style="text-align: center;" valign="top"><?php echo $key['satuan'] ?></td>
                <td class="border-right" style="text-align: center;"valign="top">&nbsp;<?php echo $key['jml'] ?></td>
                <td class="border-right" valign="top" width="100px"><?php echo $key['ket'] ?></td>
            </tr>
            <?php
        }
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
