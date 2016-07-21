<?php

namespace rest\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use rest\modules\v1\models\WpPosts;
use yii\web\Response;
use yii\filters\ContentNegotiator;

class ArticlesController extends ActiveController
{
    public $modelClass = 'rest\modules\v1\models\WpPosts';
    
    /**
     * Какое API может быть доступно
     * 1. последние статьи без анонсов
     * 2. последние статьи с анонсами
     * 3. любимая статья одна
     */
    
    protected function formatData ($format) 
    {
        if ($format == 'json') 
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        else 
        {
            Yii::$app->response->format = Response::FORMAT_XML;
        }
    }
    
    public function behaviors()
    {
        return [
            'class' => 'rest\components\LimiterFilter',
        ];
    } 
    
    public function actionOne($format = 'xml', $link = false) 
    {
        $this->formatData($format);
        $model = new WpPosts();
        return $model->oneArticle($link);        
    }    
    
    public function actionLastanons($count=10, $format = 'xml') 
    {
        $this->formatData($format);
        $model = new WpPosts();
        return $model->lastArtAnons();        
    }    
    
    public function actionLast($count=10, $format = 'xml') 
    {
        $this->formatData($format);
        $model = new WpPosts();
        return $model->lastArt();        
    }    
    
    public function actionNovelty ($count=10, $format = 'xml') 
    {
        $this->formatData($format);
        $model = new WpPosts();
        return $model->infoShort($count);        
    }
}
