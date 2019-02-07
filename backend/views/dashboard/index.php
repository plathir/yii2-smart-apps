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
                <div class="thumbnail"> 
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                        <div class="panel-heading">
                            <h3><?= $application->name ?></h3>
                        </div>

                        <div class="panel-body">
                            <?php
                            echo Html::a(Html::img($img, ['alt' => '...',
                                        'width' => '80',
                                            ]
                                    ), ["/$application->name"], ['class' => 'btn btn-default']);
                            ?>
                            <p><?= $application->descr ?></p>
                            <?= Html::a('More &raquo;', ["/$application->name"], ['class' => 'btn btn-default btn-flat']) ?>  
                            <?= Html::a('Settings &raquo;', ["/$application->name".'/settings'], ['class' => 'btn btn-default btn-flat']) ?>  
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

