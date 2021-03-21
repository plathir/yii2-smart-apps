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

    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Yii::t('app', $model->name) ?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-flat btn-loader btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-flat btn-loader btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">

            <p>
                <?=
                Html::a(Yii::t('apps', 'Uninstall'), ['admin/uninstall', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to uninstall application ?')
                ])
                ?>
                <?=
                Html::a(Yii::t('apps', 'Reload App Defaults'), ['admin/reloadxml', 'appName' => $model->name], [
                    'class' => 'btn btn-primary btn-flat',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to reload application defaults ?')
                ])
                ?>
                <?=
                Html::a(Yii::t('apps', 'Reload App Permissions'), ['admin/reloadpermissions', 'appName' => $model->name], [
                    'class' => 'btn btn-primary btn-flat',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to reload application Permissions ?')
                ])
                ?>      
                <br>
                <br>
                <?=
                Html::a(Yii::t('apps', 'Reload App Settings'), ['admin/reloadsettings', 'appName' => $model->name], [
                    'class' => 'btn btn-primary btn-flat',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to reload application Settings ?')
                ])
                ?>                 
                <?=
                Html::a(Yii::t('apps', 'Reload App Tables'), ['admin/reloadtables', 'appName' => $model->name], [
                    'class' => 'btn btn-primary btn-flat',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to reload application Tables ?')
                ])
                ?> 
            </p>

            <?=
            DetailView::widget([
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
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ])
            ?>
        </div>
    </div>
</div>
