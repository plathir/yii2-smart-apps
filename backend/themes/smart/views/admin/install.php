<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\apps\models\Apps */

$this->title = Yii::t('apps', 'Install new App');
$this->params['breadcrumbs'][] = ['label' => Yii::t('apps', 'Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-create">
    <?= $this->render('_form_install', [
        'model' => $model,
    ]) ?>

</div>
