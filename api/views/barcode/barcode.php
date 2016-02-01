
<!--This element id should be passed on to options-->
<style media="print">
    .table-barcode td {
        padding-left: 20px;
        padding-right: 20px;
    }
</style>


<?php

use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

$optionsArray = [];


for ($i = 0; $i < 8; $i++) {

    echo '<table class="table-barcode">
            <tbody>
                <tr>
                    <td><div class="tinggi" id="showBarcode1' . $i . '"></div></td>
                    <td><div class="tinggi" id="showBarcode2' . $i . '"></div></td>
                    <td><div class="tinggi" id="showBarcode3' . $i . '"></div></td>
                </tr>
            </tbody>
        </table>';

    $optionsArray1 = array(
        'elementId' => 'showBarcode1' . $i, /* div or canvas id */
        'value' => ((string) $kode) . '0', /* value for EAN 13 be careful to set right values for each barcode type */
        'type' => 'ean8', /* supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
        'settings' => array(
            'barWidth' => 2,
            'barHeight' => 40,)
    );

    $optionsArray2 = array(
        'elementId' => 'showBarcode2' . $i, /* div or canvas id */
        'value' => ((string) $kode) . '0', /* value for EAN 13 be careful to set right values for each barcode type */
        'type' => 'ean8', /* supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
        'settings' => array(
            'barWidth' => 2,
            'barHeight' => 40,)
    );

    $optionsArray3 = array(
        'elementId' => 'showBarcode3' . $i, /* div or canvas id */
        'value' => ((string) $kode) . '0', /* value for EAN 13 be careful to set right values for each barcode type */
        'type' => 'ean8', /* supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
        'settings' => array(
            'barWidth' => 2,
            'barHeight' => 40,)
    );

    echo BarcodeGenerator::widget($optionsArray1);
    echo BarcodeGenerator::widget($optionsArray2);
    echo BarcodeGenerator::widget($optionsArray3);
}
?>
<script type="text/javascript">
    window.print();
    setTimeout(function () {
        window.close();
    }, 1);
</script>