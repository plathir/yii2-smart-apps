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
            <div class="col-lg-3 col-md-6 col-xs-12" >
                <div class="thumbnail"> 
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                        <div class="panel-heading">
                            <h3><?= $application->name ?></h3>
                        </div>
                        <div class="panel-body">
                            <p><?= $application->descr ?></p>
                            <?= Html::a('More &raquo;', ["/$application->name"], ['class' => 'btn btn-default btn-flat']) ?>  
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

