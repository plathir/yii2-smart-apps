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
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title"><?=  Yii::t('app', 'List of Applications')  ?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-flat btn-loader btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-flat btn-loader btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <p>
                <?= Html::a(Yii::t('app', 'Install new App'), ['install'], ['class' => 'btn btn-success btn-flat']) ?>
            </p>


            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'id',
                    [
                        'label' => 'Status',
                        'attribute' => 'active',
                        'format' => 'raw',
                        'value' => function ($data) {
                            if ($data->active) {
                                $label = '<span class="label label-success">Active</span>';
                            } else {
                                $label = '<span class="label label-danger">Inactive</span>';
                            }

                            return Html::a(Html::decode($label), Url::to(['admin/activate', 'id' => $data->id]), [
                                        'data-method' => 'post',
                            ]);
                        },
                    ],
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
                    // 'app_key',
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
    </div>