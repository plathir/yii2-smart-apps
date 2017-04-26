<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\apps\models\Apps */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'descr:ntext',
            'type',
            'alias',
            'app_key',
            'vendor',
            'vendor_email:email',
            'version',
            'menu',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
