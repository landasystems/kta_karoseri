<?php
//header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-ujimutu.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>LAPORAN PENGUJIAN UJI MUTU</b></center>
<br><br>


<table border="1">
    <tr>
        <td rowspan="4" colspan="2">
            <br>
    <center><b> PENGAJUAN UJI MUTU</b></center>
    <br><br>
    <center>Kode Dokumen : FR-PPC-00-REV000</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="3" valign="top">
        <table>
            <tr>
                <td>PERIODE</td>
                <?php
                if (!empty($filter['tgl_periode'])) {
                    $value = explode(' - ', $filter['tgl_periode']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td>CETAK</td>
                <td> : <?php echo date('d M Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIajukan Oleh</td>
    <td>DIketahui Oleh</td>
    <td>DIsetujui Oleh</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>Customer Relation Officer</td>
    <td>Sales Suport Sect Head</td>
    <td>Finance Dept Head</td>
</tr>
</table>
<table border="1">
    <tr>
        <th rowspan="2">NO</th>
        <th rowspan="2">WO</th>
        <th rowspan="2">Merk / Type</th>
        <th rowspan="2">Bentuk Baru</th>
        <th rowspan="2">Chassis</th>
        <th rowspan="2">Customer</th>
        <th colspan="3">KELAS</th>
    </tr>
    <tr>
        <th>I</th>
        <th>II</th>
        <th>III</th>
    </tr>
    <?php
    $no=0;
    $kelas1='';
    $kelas2='';
    $kelas3='';
    foreach ($models as $key) {
        $no++;
        if($key['kelas']==1){
            $biaya = 148000;
            $kelas1 = 'v';
        }
        if($key['kelas']==2){
            $biaya = 138000;
            $kelas2 = 'v';
        }
        if($key['kelas']==3){
            $biaya = 138000;
            $kelas3 = 'v';
        }
        ?>
        <tr>
        <td valign="top">&nbsp;<?=$no;?></td>
        <td valign="top">&nbsp;<?=$key['no_wo'];?></td>
        <td valign="top">&nbsp;<?=$key['merk'];?>/<?=$key['tipe'];?></td>
        <td valign="top">&nbsp;<?=$key['bentuk_baru'];?></td>
        <td valign="top">&nbsp;<?=$key['no_chassis'];?></td>
        <td valign="top">&nbsp;<?=$key['nm_customer'];?></td>
        <td valign="top">&nbsp;<?=$kelas1;?></td>
        <td valign="top">&nbsp;<?=$kelas2;?></td>
        <td valign="top">&nbsp;<?=$kelas3;?></td>
        </tr>
        <?php
    }
    ?>
</table>

