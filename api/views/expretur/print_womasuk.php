
<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<table style="border-collapse: collapse; font-size: 11.5px;margin-top: 0px;text-decoration-color: #000000" width="100%"  border="1">
    <tr>
        <td colspan="5"><b> <div style="text-transform: uppercase;text-align: right;margin-right: 20px">WORK ORDER <?php echo $models['jenis'] ?></div></b></td>
        <td style="text-align: right;">FR-PPC-003 Rev 02</td>
    </tr>
    <tr>
        <td style=" border-right: 1px #000 solid;border-left: 1px #000 solid;border-bottom: 1px #000 solid;border-top: 1px #000 solid;" colspan="6"><b>I. DATA UNIT</b></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">No WO</td>
        <td style="border-bottom: 1px solid #000000;" colspan="2">: <b><?php echo $models['no_wo'] ?></b></td>
        <td style="border-bottom: 1px solid #000000;">Model</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: <?php echo $models['model'] ?></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">Customer</td>
        <td colspan="2" style="border-bottom: 1px solid #000000;">: <?php echo $models['nm_customer'] ?></td>
        <td style="border-bottom: 1px solid #000000;">No.Rangka</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: <?php echo $models['no_chassis'] ?></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">Merk</td>
        <td colspan="2" style="border-bottom: 1px solid #000000;">: <?php echo $models['merk'] ?></td>
        <td style="border-bottom: 1px solid #000000;">No Mesin</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: <?php echo $models['no_mesin'] ?></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000000;">type</td>
        <td colspan="2" style="border-bottom: 1px solid #000000;">: <?php echo $models['tipe'] ?></td>
        <td style="border-bottom: 1px solid #000000;">Tgl Masuk</td>
        <td colspan="2" style="border-right: 1px #000 solid;border-bottom: 1px solid #000000;">: <?php echo $models['tgl_terima'] ?></td>
    </tr>
    <tr>
        <td colspan="6">
            <table style="border-collapse: collapse; font-size: 11.5px;" width="100%"  border="1">
                <tr>
                    <td  style="text-align: center;width: 1%;font-size: 10px;">II</td>
                    <td  style="border-bottom: 1px solid #000000;width:9%;"><b> SPESIFIKASI</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;text-align: center"><b> URAIAN</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;font-size: 10px;text-align: center"><b> V/X</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;text-align: center"><b>KETERANGAN</b></td>
                </tr>
                <?php
                $table = '';
                if ($models['jenis'] == "Small Bus") {
                    $eks = \app\models\Smalleks::find()->where('no_wo="' . $models['no_wo'] . '"')->one();
                    $warna = \app\models\Warna::find()->where('kd_warna="' . $eks['warna'] . '"')->one();
                } else {
                    $eks = \app\models\Minieks::find()->where('no_wo="' . $models['no_wo'] . '"')->one();
                    $warna = \app\models\Warna::find()->where('kd_warna="' . $eks['warna'] . '"')->one();
                }
                ?>
                <tr>
                    <td style="text-align: center;width: 1%;border-top: 1px solid #000000"><b>E</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Plat Body</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['plat_body'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>K</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Ventilasi Atas</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['ventilasi_atas'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>S</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Kaca Spion</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['kaca_spion'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>T</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Kaca Depan</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['kaca_depan'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>E</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Kaca Belakang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['kaca_belakang'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>R</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Kaca Samping</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['kaca_samping'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>I</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lampu Depan</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['lampu_depan'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"><b>O</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lampu Belakang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['lampu_belakang'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>

                    <td style="text-align: center;width: 1%"><b>R</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Pintu Depan</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['pintu_depan'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Pintu Penumpang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['pintu_penumpang'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Pintu Bagasi Samping</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['pintu_bagasi_samping'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Pintu Belakang</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['pintu_belakang'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Wyper Set</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['wyper_set'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Anti Karat</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['anti_karat'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Warna</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $warna['warna'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Strip</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['strip'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;width: 1%"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;"><b>Letter</b></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $eks['letter'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <tr>
                    <td  style="border-bottom:  1px solid #000000;text-align: center;width: 1%"></td>
                    <td  style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;height: 30px;"><b>Lain-lain</b></td>
                    <td  style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;"><?php echo $eks['lain2'] ?></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                    <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                </tr>
                <?php
                if ($models['jenis'] == "Small Bus") {
                    $int = \app\models\Smallint::find()->where('no_wo="' . $models['no_wo'] . '"')->one();
                    ?>
                    <tr style="padding: 0px">
                        <td style="padding: 0px;text-align: center;width: 1%"><b><b>I</b></b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Plavon</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15% ">&nbsp;<?php echo $int['plavon'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%"></td>
                    </tr>
                    <tr style="padding: 0px;">
                        <td style="padding: 0px;text-align: center;width: 1%"><b>N</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Bagasi Dalam</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['bagasi_dalam'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td  style="padding:0px;text-align: center;width: 1%"><b>T</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Duchting</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['duchting_louver'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="padding: 0px;text-align: center;width: 1%"><b>E</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Trimming</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['trimming_deck'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="padding: 0px;text-align: center;width: 1%"><b>R</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lampu Plavon</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['lampu_plavon'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="padding: 0px;text-align: center;width: 1%"><b>I</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Dashboard</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['dashboard'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="padding: 0px;text-align: center;width: 1%"><b>O</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lantai</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['lantai'] ?></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="padding: 0px;text-align: center;width: 1%"><b>R</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Karpet</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['karpet'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"><b></b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Peredam</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['peredam'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"><b></b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Peg Tgn Atas</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['pegangan_tangan_atas'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 1px;text-align: center;width: 1%"></td>
                        <td style="padding: 1px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Pengmn Penumpang</b></td>
                        <td style="padding: 1px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;"><?php echo $int['pengaman_penumpang'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Pengmn Kc Smpg</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['pengaman_kaca_samping'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Pengmn </b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['pengaman_driver'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 1px;text-align: center;width: 1%"></td>
                        <td style="padding: 1px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Gordyn</b></td>
                        <td style="padding: 1px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['gordyn'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 1px;text-align: center;width: 1%"></td>
                        <td style="padding: 1px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Driver Fan</b></td>
                        <td style="padding: 1px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['driver_fan'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Radio Tape</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['radio_tape'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Spek Seat</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['spek_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Driver Seat</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['driver_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Cover Seat</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['cover_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr ng-if="form.jenis == 'Small Bus'">
                        <td style="padding: 0px;text-align: center;width: 1%"></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Optional Seat</b></td>
                        <td style="padding: 0px;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['optional_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Total Seat</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['total_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lain-lain</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['lain2'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;AC</td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['merk_ac'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <?php
                } else {
                    $int = app\models\Miniint::find()->where('no_wo="' . $models['no_wo'] . '"')->one();
                    ?>
                    <tr style="padding: 0px">
                        <td style="text-align: center;width: 1%"><b><b>I</b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Plavon</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15% ">&nbsp;<?php echo $int['plavon'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td  style="text-align: center;width: 1%"><b>N</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Duchting</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['duchting_louver'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="text-align: center;width: 1%"><b>T</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Trimming</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['trimming_deck'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr style="padding: 0px">
                        <td style="text-align: center;width: 1%"><b>E</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lampu Plavon</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['lampu_plavon'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    
                    <tr >
                        <td style="text-align: center;width: 1%"><b>R</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lantai</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['lantai'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b>I</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Karpet</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['karpet'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b>O</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Konf Seat 1</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['konf_seat1'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b>R</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Konf Seat 2</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['konf_seat2'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Konf Seat 3</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['konf_seat3'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Konf Seat 4</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['konf_seat4'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"><b></b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Konf Seat 5</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['konf_seat5'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Cover Seat</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['cover_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                   
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Total Seat</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['total_seat'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;<b>Lain-lain</b></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['lain2'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <tr >
                        <td style="text-align: center;width: 1%"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:9%;">&nbsp;AC</td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:15%;">&nbsp;<?php echo $int['merk_ac'] ?></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:2%;"></td>
                        <td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:13%;"></td>
                    </tr>
                    <?php
                }
                ?>

            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <table style="border-collapse: collapse; font-size: 10px;" width="100%"  border="1">
                <tr>
                    <td style="border-bottom: 1px solid #000000;width:2.7%;"></td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> STRIP OFF</td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> KOMP</td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> B/W</td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> PUTTY</td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> PAINT</td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> FINISH</td>
                    <td colspan="2" style="text-align: center;border-left: 1px solid #000000;border-bottom: 1px solid #000000;width:12%;"> FI</td>
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
            <table style="border-collapse: collapse; font-size: 11px;" width="100%"  border="1">
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;">
                        NOTE : 
                    </td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center">DIBUAT</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center">DIPERIKSA</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center">DIPERIKSA</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center">DIKETAHUI</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;text-align: center">DITERIMA</td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
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
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                    <td style="border-right: 1px solid #000000;">&nbsp;</td>
                </tr>
                <tr> 
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;"></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">PPIC Head</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Engineering Head</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Component Head</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Plan Head</td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;text-align: center">Production Head</td>
                </tr>
                <tr> 
                    <td style="border-left: 1px solid #000000;border-right: 1px solid #000000;"></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                    <td style="border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size: 10px;">TGL : <?php echo date('d-m-Y') ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script type="text/javascript">
    window.print();
    setTimeout(function() {
        window.close();
    }, 1);
</script>