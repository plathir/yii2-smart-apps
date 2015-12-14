<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

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
        <?php // Html::a(Yii::t('app', 'Create Apps'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Install Apps'), ['install'], ['class' => 'btn btn-success']) ?>
    </p>

    
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(Html::encode($data->name), Url::to(['admin/view', 'id' => $data->id]));
                },
                    ],
                    'descr:ntext',
                    'type',
                    'alias',
                    // 'key',
                    // 'vendor',
                    // 'vendor_email:email',
                    // 'version',
                    'created_at:datetime',
                    'updated_at:datetime',
                    // ['class' => 'yii\grid\ActionColumn'],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{uninstall}',
                        'buttons' => [
                            'uninstall' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                                            'title' => Yii::t('app', 'Uninstall'),
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to uninstall application ?'
                                ]);
                            }
                                ],
                                'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'uninstall') {
                                $url = Url::to(['admin/uninstall', 'id' => $model->id]);
                                return $url;
                            }
                        }
                            ],
                        ],
                    ]);
                    ?>

</div>
