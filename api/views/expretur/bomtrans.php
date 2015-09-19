<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan-HPP.xls");
}
?>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width: 26cm">
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px;" width="100%" border="1">
        <thead>
            <tr valign="top">
                <td align="center" class="border-right" width="35%" valign="middle"><h2 style="font-size: 20px;">BILL OF <br>MATERIAL</h2></td>
                <td colspan="8">
                    <table style="font-size: 12px;">
                        <tr>
                            <td width="100">Produk</td>
                            <td>: <?php echo $model['merk'] ?></td>
                        </tr>
                        <tr>
                            <td>Model / Type</td>
                            <td>: <?php echo $model['model'] . '/' . $model['tipe'] ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </thead>
    </table>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px; margin-top: -2px;" width="100%" border="1">
        <thead>
            <tr>
                <td align="center" class="border-all"><b>NAMA MATERIAL</b></td>
                <td align="center" class="border-all"><b>SATUAN</b></td>
                <td align="center" class="border-all"><b>QTY</b></td>
                <td align="center" colspan="2" width="200" class="border-all"><b>HARGA SATUAN</b></td>
                <td align="center" colspan="2" width="200" class="border-all"><b>HARGA</b></td>
                <td align="center" colspan="2" width="250" class="border-all"><b>KETERANGAN</b></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $grandTotal = 0;
            foreach ($detail as $bag) {
                echo '<tr>
                        <td class="border-all back-grey" colspan="9" style="background-color: rgb(226, 222, 222)"><b>' . $bag['nama_jabatan'] . '</b></td>
                      </tr>';
                $total = 0;
                foreach ($bag['body'] as $det) {
                    $total += $det['harga'] * $det['jumlah'];
                    $grandTotal += $det['harga'] * $det['jumlah'];
                    echo '<tr height="25">
                            <td class="border-all" width="35%">' . $det['nama_barang'] . '</td>
                            <td class="border-all" align="center" width="70">' . $det['satuan'] . '</td>
                            <td class="border-all" align="center" width="70">' . $det['jumlah'] . '</td>
                            <td class="border-all" align="right" colspan="2" width="100">' . $det['harga'] . '</td>
                            <td class="border-all" align="right" colspan="2" width="100">' . $det['harga'] * $det['jumlah'] . '</td>
                            <td class="border-all" colspan="2">' . $det['ket'] . '</td>
                        </tr>';
                }
                echo '<tr>
                            <td colspan="5" align="left" class="border-all"><b>Sub Total</b></td>
                            <td colspan="2" align="right" class="border-all"><b>' . $total . '</b></td>
                            <td colspan="2" class="border-all"></td>
                        </tr>';
            }
            ?>
            <tr>
                <td colspan="5" align="left" class="border-all"><b>Grand Total</b></td>
                <td colspan="2" align="right" class="border-all"><b><?php echo $grandTotal ?></b></td>
                <td colspan="2" class="border-all"></td>
            </tr>
            <tr>
                <td>Note</td>
                <td colspan="3" class="border-all" align="center">PREPARED</td>
                <td colspan="3" class="border-all" align="center">CHECKED</td>
                <td colspan="2" class="border-all" align="center">APPROVED</td>
            </tr>
            <tr>
                <td height="100" ></td>
                <td height="100" colspan="3" class="border-all"></td>
                <td height="100" colspan="3" class="border-all"></td>
                <td height="100" colspan="2" class="border-all"></td>
            </tr>
        </tbody>
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