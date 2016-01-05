<style media="print">
    @media print {
        html, body {
            display: block; 
            font-family: "Arial";
            margin: 0;
            -webkit-transform-origin: 0 0;
            -moz-transform-origin: 0 0;
        }
    }
</style>
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; font-size: 14px;margin-top: 0px;text-decoration-color: #000000;border: 1px #000000 solid;width: 99%"   >
    <tr>
        <td colspan="4"><b> <div style="text-transform: uppercase;text-align: right;margin-right: 0px; font-size: 16px">WORK ORDER MINI BUS</div></b></td>
        <td colspan="2" style="text-align: right;font-size: 15px">FR-PPC-003 Rev 03</td>
    </tr>
    <tr>
        <td style=" border-right: 1px #000 solid;border-left: 1px #000 solid;border-bottom: 1px #000 solid;border-top: 1px #000 solid;" colspan="6"><b>I. DATA UNIT</b></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">No WO</td>
        <td colspan="2" style="border-bottom: 1px solid #000000; font-weight: 500;font-size: 18px;width: 160px" >: <b></b></td>
        <td style="border-bottom: 1px solid #000000;">Model</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: </td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">Customer</td>
        <td colspan="2" style="border-bottom: 1px solid #000000;">: </td>
        <td style="border-bottom: 1px solid #000000;">No.Rangka</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: </td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">Merk</td>
        <td colspan="2" style="border-bottom: 1px solid #000000;">:</td>
        <td style="border-bottom: 1px solid #000000;">No Mesin</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: </td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">Type</td>
        <td colspan="2" style="border-bottom: 1px solid #000000;">: </td>
        <td style="border-bottom: 1px solid #000000;">Tgl Masuk</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: </td>
    </tr>
    <tr>
           <?php
                $table = 'Small Bus';
                if ($table == "Small Bus") {
                    $font = 'font-size:12.6px';
                } else {
                   $font = 'font-size:13.6px';
                }
                ?>
        <td colspan="6" style="">
            <table style="border-collapse: collapse; font-size: 13px; " width="100%">
                <tr>
                    <td  style="text-align: center;width: 2%;font-size: 10px;">II</td>
                    <td  style="border-bottom: 1px solid #000000;width:19%;"><b> SPESIFIKASI</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:32%;<?php echo $font ?>;text-align: center"><b> URAIAN</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:1%;font-size: 10px;text-align: center"><b> V/X</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:28%;text-align: center"><b>KETERANGAN</b></td>
                </tr>
             
                <tr>
                    <td style="text-align: center;width: 1%;border-top: 1px solid #000000"><b>E</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Plat Body</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>K</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Ventilasi Atas</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>S</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Kaca Spion</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>T</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Kaca Depan</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>E</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Kaca Belakang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>R</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Kaca Samping</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>I</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Lampu Depan</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>O</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Lampu Belakang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>

                    <td style="text-align: center;width: 1%"><b>R</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Pintu Depan</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Pintu Penumpang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Pintu Bagasi Samping</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Pintu Belakang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Wyper Set</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Anti Karat</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Warna</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Strip</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>"><b>Letter</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td  style="border-bottom:  1px solid #000000;text-align: center;width: 1%"></td>
                    <td  style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>;height: 30px;"><b>Lain-lain</b></td>
                    <td  style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;"><b></b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                       <tr style="padding: 0px">
                        <td style="text-align: center;width: 1%"><b><b>I</b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Plavon</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?> ">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td  style="text-align: center;width: 1%"><b>N</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Duchting</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="text-align: center;width: 1%"><b>T</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Trimming</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="text-align: center;width: 1%"><b>E</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Lampu Plavon</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>

                    <tr >
                        <td style="text-align: center;width: 1%"><b>R</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Lantai</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b>I</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Karpet</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b>O</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Konf Seat 1</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b>R</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Konf Seat 2</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Konf Seat 3</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Konf Seat 4</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Konf Seat 5</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Cover Seat</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>

                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Total Seat</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>Lain-lain</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;" rowspan="4">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>&nbsp;</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>&nbsp;</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>&nbsp;</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;<?php echo $font ?>">&nbsp;<b>AC</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;<?php echo $font ?>;">&nbsp;<b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <table style="border-collapse: collapse; font-size: 10px;border-top: 1px #000000 solid;border-left: 1px #000000 solid;border-bottom: 1px #000000 solid" width="100%">
                <tr>
                    <td style="border-bottom: 1px solid #000000;width:2.7%;"></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"><b> STRIP OFF</b></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"><b> KOMP</b></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> <b>B/W</b></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"><b> PUTTY</b></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"><b> PAINT</b></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"><b> FINISH</b></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> <b>FI</b></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000000;width:2.7%;"><b>P</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>

                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000000;width:2.7%;"><b>A   </b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;"></td>

                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            
            <table style="border-collapse: collapse; font-size: 12px;border-left: 1px #000000 solid;border-bottom:1px #000000 solid;border-top: 1px #000000 solid" width="100%"  >
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;font-weight: 300">
                        NOTE : 
                    </td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center"><b>DIBUAT</b></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center"><b>DIPERIKSA</b></td>
                    
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center"><b>DITERIMA</b></td>
                    <td style="border-bottom: 1px solid #000000;text-align: center"><b>DIKETAHUI</b></td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                   
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                </tr>
                <tr> 
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;"></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">PPIC Head</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Engineering Head</td>
                    
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Production Head</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Plan Head</td>
                </tr>
                <tr> 
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;"></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    <td style="border-bottom: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                </tr>
            </table>
            
        </td>
    </tr>
</table>
<script type="text/javascript">
    window.print();
    setTimeout(function () {
        window.close();
    }, 1);
</script>
