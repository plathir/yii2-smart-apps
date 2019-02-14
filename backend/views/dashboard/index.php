<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\apps\models\AppsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Apps Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-index">
    <div class="row">
        <?php foreach ($applications as $application) { ?>
            <?php
            $bundle = null;
            $h_text = '$bundle = apps' . '\\' . $application->name . '\\backend\\' . $application->name . 'Asset::register($this);';
            eval($h_text);

            $img = $bundle->baseUrl . $application->app_icon;
            ?>
            <div class="col-lg-3 col-md-6 col-xs-12" >
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= $application->name ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-flat btn-loader btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button class="btn btn-flat btn-loader btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <?php
                        echo Html::a(Html::img($img, ['alt' => '...',
                                    'width' => '80',
                                        ]
                                ), ["/$application->name"], ['class' => 'btn btn-default']);
                        ?>
                        <p><?= $application->descr ?></p>
                        <?= Html::a('More &raquo;', ['/apps/admin/view', 'id' => $application->id], ['class' => 'btn btn-default btn-flat']) ?>  
                        <?= Html::a('Settings &raquo;', ["/$application->name" . '/settings'], ['class' => 'btn btn-default btn-flat']) ?>              
                        <?=
                        Html::a('Deactivate', ['/apps/admin/activate', 'id' => $application->id], [
                            'class' => 'btn btn-danger btn-flat',
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to Deactivate application ?'
                        ])
                        ?>  
                    </div> 
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

