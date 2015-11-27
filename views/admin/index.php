<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\apps\models\AppsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Apps');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Apps'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Install Apps'), ['install'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'descr:ntext',
            'type',
            'alias',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{uninstall}',
                'buttons' => [
                    'uninstall' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                                    'title' => Yii::t('app', 'Uninstall'),
                                        //'data-method' => 'post'
                        ]);
                    }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'uninstall') {
                        $url = 'admin/uninstall?id=' . $model->id;
                        return $url;
                    }
                }
                    ],
                    // 'key',
                    // 'vendor',
                    // 'vendor_email:email',
                    // 'version',
                    // 'created_at',
                    // 'updated_at',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>

</div>
