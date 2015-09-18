<pre>
<?php
$data = array();
$i = 0 ;
foreach ($models as $value){
    $data[$i] = $model['qty'] - $model['jumlah'];
}

print_r($data);

?>
</pre>