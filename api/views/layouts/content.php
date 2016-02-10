<?php

use yii\helpers\Html;
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <title><?php echo Html::encode($this->title); ?></title>
<?php $this->head(); ?>
    </head>
    <body>
        <?php $this->beginBody(); ?>
        <?php echo $content; ?>
<?php $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage(); ?>