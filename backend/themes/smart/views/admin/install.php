<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\apps\models\Apps */

$this->title = Yii::t('app', 'Create Apps');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-create">
    <?= $this->render('_form_install', [
        'model' => $model,
    ]) ?>

</div>
