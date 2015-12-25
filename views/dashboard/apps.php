<?php
use yii\helpers\Html;

?>
    <div class="col-md-12">
        <!-- DIRECT CHAT PRIMARY -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Applications</h3>

            </div><!-- /.box-header -->
            <div class="box-body">
                <?php foreach ($applications as $application) { ?>
                    <?php
                    $bundle = null;
                    $h_text = '$bundle = apps' . '\\' . $application->name . '\\backend\\' . $application->name . 'Asset::register($this);';
                    eval($h_text);

                    $img = $bundle->baseUrl . $application->app_icon;
                    ?>
                    <div class="col-lg-2 col-md-3 col-xs-12" >
                        <div class="thumbnail"> 
                            <div class="panel panel-default">
                                <!-- Default panel contents -->
                                <div class="panel-heading">
                                    <h3><?= $application->name ?></h3>
                                </div>

                                <div class="panel-body">
                                    <?php // echo $img;   ?>
                                    <img src="<?php echo $img ?>">
                                    <p><?= $application->descr ?></p>
                                    <?= Html::a('More &raquo;', ["/$application->name"], ['class' => 'btn btn-default btn-flat']) ?>  
                                </div>
                            </div>
                        </div>
                    </div>            
                <?php } ?>

            </div><!-- /.box-body -->
            <div class="box-footer">
                Test footer
            </div><!-- /.box-footer-->
        </div><!--/.direct-chat -->
    </div><!-- /.col -->   
