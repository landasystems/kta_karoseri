<?php
exec('mysqldump --user=root --password=bismillah --host=localhost purchassing.det_standar --where=<purchassing.det_standar.kd_bom="bom150050"> > /var/www/file.sql');
?>