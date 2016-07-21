<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\Theme1Asset;
use common\widgets\Alert;

Theme1Asset::register($this);

$contr = Yii::$app->controller->id;
$act = Yii::$app->controller->action->id;
$contract = $contr.$act;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?= Yii::$app->name; ?>">
    <meta name="yandex-verification" content="6da2f6ee3d64a046">
    <meta property="og:image" content="<?= Yii::$app->params['ogimg']; ?>">    
    <link rel="icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" href="/favicon.ico" type="image/x-icon"> 
    <link href="http://iamruss.ru/feed/" rel="alternate" type="application/rss+xml" title="Я русский &raquo; Лента" >
    <?= Html::csrfMetaTags() ?>
    <title><?php echo Yii::$app->mainTitle; ?></title>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--><?php echo "\n"; $this->head(); ?>    
</head>
<body>
<?php $this->beginBody() ?>    
    
<?php echo $content; ?>    
    
<?php $this->endBody() ?>   
    
<?php

$url_biskni = 'http://bistrokniga.ru/api/v1/rusbook/onebook';
$str_biskni = file_get_contents($url_biskni);

?>
<div style="display: none;" id="str_biskni"><?= $str_biskni; ?></div> 

<script async type="text/javascript">
function func() {
    var d = document;
    var s = 'script';
    var id = 'facebook-jssdk'
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); 
    js.id = id;
    js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.5&appId=1735690529983120";
    fjs.parentNode.insertBefore(js, fjs);
}

$(document).ready(function(){
    $('#bottom_place').load('/advanced/frontend/web/_bottom.php');
    $('#content img').addClass('img-thumbnail');
    $('li.dropdown[id!="site"]:has("ul.dropdown-menu"):has("li.active")').addClass('active');
    $('button[data-target="#donateModal"]').click(function(){
        $('#donate_form').load('/advanced/frontend/web/_donate.php');
    });
    
    setTimeout(function () {
        var obj = $.parseJSON($('#str_biskni').html());
        var banner = '<div class="panel panel-default"><div class="panel-heading"><a title="узнать подробнее о книге" href="'+obj.book_url+'" target="_blank"><strong>'+obj.name+'</strong></a></div><div class="panel-body"><p><a title="узнать подробнее о книге" href="'+obj.book_url+'" target="_blank"><img width="240" class="img-thumbnail" src="'+obj.picture+'" alt=""></a></p><div class="panel-footer text-right"><small>автор: '+obj.author_name+'</small></div></div></div>';        
        $('#random_book').append(banner); 
    }, 3000);    
    
});    
</script>

<script src="//iamruss.ru/watch.js" type="text/javascript"></script>
<script async type="text/javascript">
  var yaCounter24673511 = new Ya.Metrika({id: 24673511,clickmap:true,trackLinks:true,accurateTrackBounce:true});
</script>
    
</body>
</html>    
<?php $this->endPage() ?>
