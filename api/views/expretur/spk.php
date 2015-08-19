<?php
//header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-PO.xls");
?>
<table width="100%" border="1">
    <tr>
        <td colspan="4" rowspan="2" align="center" valign="top"><h2>REKAP SPK MASUK</h2></td>
        <td colspan="4" rowspan="2" valign="top">
            <table>
                <tr>
                    <td width="100" valign="top">Dept/Bagian</td>
                    <td width="1" valign="top">:</td>
                    <td valign="top">Sales Support</td>
                </tr>
                <tr>
                    <td width="100" valign="top">Periode</td>
                    <td width="1" valign="top">:</td>
                    <td valign="top">Sales Support</td>
                </tr>
            </table>
        </td>
        <td colspan="2" valign="top">Dibuat</td>
        <td colspan="2" valign="top">Diketahui</td>
    </tr>
    <tr height="70">
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>NO</td>
        <td>SALES</td>
        <td>TGL CHASSIS</td>
        <td colspan="2">CUSTOMER</td>
        <td>KODE TITIPAN</td>
        <td>NO WO</td>
        <td colspan="2">MERK/TYPE</td>
        <td colspan="2">MODEL</td>
        <td>NO SPK</td>
    </tr>
    <?php
    $no=1;
    foreach($models as $val){
        echo '<tr>';
        echo '<td>'.$no.'</td>';
        echo '<td>'.$val[''].'</td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '</tr>';
    }
    ?>
</table>