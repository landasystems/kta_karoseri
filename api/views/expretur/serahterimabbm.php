<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-serah-terima-bbm.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table class="border-all" style="border-collapse: collapse; font-size: 11px;" width="100%">
    <tr>
        <td rowspan="2">
            <table style="font-size: 11px;">
                <tr>
                    <td colspan="3" align="center">
                        <h4 style="font-size: 14px;">SERAH TERIMA BUKTIBARANG MASUK (BBM)</h4>
                    </td>
                </tr>
                <tr>
                    <td width="110">No. Dokumen</td>
                    <td width="5">:</td>
                    <td>FR-WHS-013-REV.00</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td width="5">: </td>
                    <td><?php echo isset($periode) ? $periode : ''; ?></td>
                </tr>
                <tr>
                    <td>Cetak</td>
                    <td width="5">:</td>
                    <td><?php echo date("d-m-Y") ?></td>
                </tr>
            </table>
        </td>
        <td class="border-left border-bottom">Diserahkan Oleh</td>
        <td class="border-left border-bottom">Diterima  Oleh</td>
    </tr>
    <tr>
        <td height="70" class="border-left"></td>
        <td class="border-left"></td>
    </tr>
</table>
<table class="border-all" style="border-collapse: collapse; margin-top: -2px; font-size: 11px;" width="100%">
    <thead>
        <tr>
            <td rowspan="2" class="border-right border-bottom" align="center" width="20">NO.</td>
            <td rowspan="2" class="border-right border-bottom" align="center">NO. BBM</td>
            <td rowspan="2" class="border-right border-bottom" align="center">SUPLLIER</td>
            <td colspan="6" class="border-right border-bottom" align="center" width="300">LAMPIRAN</td>
            <td rowspan="2" class="border-bottom" align="center">KETERANGAN</td>
        </tr>
        <tr>
            <td align="center" class="border-right border-bottom" width="50">BBM ASLI</td>
            <td align="center" class="border-right border-bottom" width="50">BBM COPY</td>
            <td align="center" class="border-right border-bottom" width="50">SJ ASLI</td>
            <td align="center" class="border-right border-bottom" width="50">SJ COPY</td>
            <td align="center" class="border-right border-bottom" width="50">NOTA ASLI</td>
            <td align="center" width="50" class="border-bottom">NOTA COPY</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $bbm = '';
        $border = '';
        foreach ($models as $key => $val) {
            if (isset($models[$key + 1]) and $models[$key + 1]['nama_supplier'] == $val['nama_supplier']) {
                $border = '';
            } else {
                $border = 'border-bottom';
            }

            if (isset($models[$key + 1]) and $models[$key + 1]['no_bbm'] != $val['no_bbm']) {
                echo '<tr>';
                echo '<td class="border-right ' . $border . '" align="center">' . $no . '</td>';
                echo '<td class="border-right ' . $border . '">' . $val['no_bbm'] . '</td>';
                echo '<td class="border-right ' . $border . '">' . $val['nama_supplier'] . '</td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '<td class="border-right ' . $border . '"></td>';
                echo '</tr>';
                $no++;
            }
        }
        ?>
    </tbody>
</table>
<?php
if (isset($_GET['print'])) {
    ?>
    <!--    <script type="text/javascript">
            window.print();
            setTimeout(function () {
                window.close();
            }, 1);
        </script>-->
    <?php
}
?>