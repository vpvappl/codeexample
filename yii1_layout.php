<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Микояновский мясокомбинат">
        <link rel="icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/favicon.ico">
        <link rel="shortcut icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/favicon.ico" />
        <link rel="icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/favicon.ico" type="image/x-icon" />                
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title><?php echo $this->pageTitle; ?></title>
        <link href="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/bootstrap.mikoyan.3.1.1.css" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link href="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/my.css" rel="stylesheet">
        <link href="/jscss/colorbox1.css" rel="stylesheet">
        <link href="/themes/masonry/jscss/print.css" media="print" rel="stylesheet" type="text/css">
    </head>
    <body>
        
        <?php include 'menu.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <?php echo $content; ?>
            </div><!-- row -->
            <div class="row" id="bottom">
                <div class="col-md-3">
                    <?php $this->widget('WidgetRender', array('render'=>'copyright')); ?>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>
                                Разделы сайта | 
                                <small>
                                    <a role="button" 
                                       data-toggle="collapse" 
                                       href="#collapseCounters" 
                                       aria-expanded="false" 
                                       aria-controls="collapseCounters" 
                                       title="яндекс метрика, движок и т.д.">
                                        счетчики
                                    </a>
                                </small>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <?php $this->widget('WidgetMenu', array('view'=>'bottom', 'class'=>'menu-bottom')); ?>
                            <?php $this->widget('WidgetRender', array('render'=>'counters')); ?>
                        </div>
                    </div>                    
                </div>                
                <div class="col-md-3">
                    <?php $this->widget('WidgetRender', array('render'=>'contacts')); ?>
                </div>               
            </div>
        </div><!-- container-fluid -->
        
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/jquery.min.1.11.3.js"></script>  
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/bootstrap.min.3.3.5.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/masonry.pkgd.min.js"></script>    
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/masonry-initial.js"></script>
    <script src="/jscss/colorbox-min.js"></script> 
    <script src="/jscss/colorbox-activate.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/jscss/my.js"></script>
    
    </body>
</html>
