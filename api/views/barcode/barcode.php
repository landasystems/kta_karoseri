
<!--This element id should be passed on to options-->
<style media="print">
    .table-barcode td {
        /*        padding-left: 15px;
                padding-right: 15px;*/
    }
</style>


<?php

use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

$optionsArray = [];


for ($i = 0; $i < $jumlah; $i++) {

    echo '<table class="table-barcode" style="width:100%; margin-left:12px; margin-bottom:10px;margin-top:10px;">
            <tbody>
                <tr>
                    <td style="width:33%; padding:0px;" align="center"><div class="tinggi" id="showBarcode1' . $i . '" ></div></td>
                    <td style="width:33%; padding:0px;" align="center"><div class="tinggi" id="showBarcode2' . $i . '"></div></td>
                    <td style="width:33%; padding:0px 0px 0px 3px;"><div class="tinggi" id="showBarcode3' . $i . '" ></div></td>
                </tr>
            </tbody>
        </table>';

    $optionsArray1 = array(
        'elementId' => 'showBarcode1' . $i, /* div or canvas id */
        'value' => ((string) $kode) . '00', /* value for EAN 13 be careful to set right values for each barcode type */
        'type' => 'upc', /* supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
        'settings' => array(
            'barWidth' => 1,
            'barHeight' => 30,)
    );

    $optionsArray2 = array(
        'elementId' => 'showBarcode2' . $i, /* div or canvas id */
        'value' => ((string) $kode) . '00', /* value for EAN 13 be careful to set right values for each barcode type */
        'type' => 'upc', /* supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
        'settings' => array(
            'barWidth' => 1,
            'barHeight' => 30,)
    );

    $optionsArray3 = array(
        'elementId' => 'showBarcode3' . $i, /* div or canvas id */
        'value' => ((string) $kode) . '00', /* value for EAN 13 be careful to set right values for each barcode type */
        'type' => 'upc', /* supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
        'settings' => array(
            'barWidth' => 1,
            'barHeight' => 30,
        )
    );

    echo BarcodeGenerator::widget($optionsArray1);
    echo BarcodeGenerator::widget($optionsArray2);
    echo BarcodeGenerator::widget($optionsArray3);
}
?>
<script type="text/javascript">
    window.print();
//    setTimeout(function () {
//        window.close();
//    }, 3);
</script>