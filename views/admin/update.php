<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\apps\models\Apps */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Apps',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="apps-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
