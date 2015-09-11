<?php
//header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-bom.xls");
?>
<link href="../../../css/print.css" rel="stylesheet" type="text/css" />
<div style="width:24cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%">
        <thead>
            <tr>
                <td rowspan="2" width="30%" class="border-right">
                    <h6 style="margin: 0px; padding: 0px; font-size: 16px; font-weight: normal;" align="center">REALISASI STANDART</h6>
                    <h3 style="margin: 0px; padding: 0px;" align="center">PEMAKAIAN BAHAN</h3>
                    <p align="center">No. Dokumen. FR-WHS-006.REV.00</p>
                </td>
                <td rowspan="2">
                    <table style="font-size: 12px;">
                        <tr>
                            <td width="80">Nomor</td>
                            <td width="5">:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Merk</td>
                            <td width="5">:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td width="5">:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Model</td>
                            <td width="5">:</td>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td height="20" width="15%" class="border-left border-bottom" align="center">
                    Dibuat Oleh
                </td>
                <td width="15%" class="border-left border-bottom" align="center">
                    Diperiksa Oleh
                </td>
            </tr>
            <tr>
                <td height="70" class="border-left"></td>
                <td height="70" class="border-left"></td>
            </tr>
        </thead>
    </table>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 11px; margin-top: -2px;" width="100%">
        <thead>
            <tr>
                <td class="border-left" align="center" width="100">KODE BARANG</td>
                <td class="border-left" align="center">NAMA BARANG</td>
                <td class="border-left" align="center">SAT</td>
                <td class="border-left" align="center">STANDAR</td>
                <?php
                for ($i = 0; $i < 5; $i++) {
                    echo '<td width="80" class="border-left border-right" align="center">' . (isset($no_wo[$i]) ? $no_wo[$i] : '-') . '</td>';
                }
                ?>
            </tr>
            <?php
            $no = 1;
            foreach ($data as $val) {
                echo '<tr>';
                echo '<td colspan="9" class="border-top" style="background-color:#DFDFDF">' . $val['title'] . '</td>';
                echo '</tr>';
                foreach ($val['body'] as $vDet) {
                    echo '<tr>';
                    echo '<td class="border-top border-right" align="center">' . $vDet['kd_barang'] . '</td>';
                    echo '<td class="border-top border-right">' . $vDet['nm_barang'] . '</td>';
                    echo '<td class="border-top border-right" align="center">' . $vDet['satuan'] . '</td>';
                    echo '<td align="center" class="border-top border-right">' . $vDet['qty'] . '</td>';
                    for ($i = 0; $i < 5; $i++) {
                        $wo = isset($no_wo[$i]) ? $no_wo[$i] : 0;
                        if (isset($vDet['jml_keluar'][$wo])) {
                            echo '<td align="center" class="border-right border-bottom">' . $vDet['jml_keluar'][$no_wo[$i]] . '</td>';
                        } else {
                            echo '<td align="center" class="border-right border-bottom"></td>';
                        }
                    }
                    echo '</tr>';
                    $no++;
                }
            }
            ?>
        </thead>
    </table>
</div>