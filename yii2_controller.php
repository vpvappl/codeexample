<?php
namespace frontend\controllers;

use Yii;
use frontend\components\Controller;
use yii\helpers\ArrayHelper;

use yii\data\SqlDataProvider;

use frontend\models\WpPosts;
use frontend\models\WpTerms;

use yii\web\NotFoundHttpException;

class ContentController extends Controller
{
    
    public function actions()
    {
        return [
            'feed' => [
                'class' => 'frontend\controllers\actions\FeedAction',
                ], 
            'archive' => [
                'class' => 'frontend\controllers\actions\ContentArchiveAction',
                ],            
        ];
    }
    
    public $only_actions = ['postpage','category','author','tag','archive'];    
    
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\PageCache',
                'only' => $this->only_actions,
                'duration' => Yii::$app->params['cache_duration'], 
                'variations' => [
                    Yii::$app->request->get('first'),
                    Yii::$app->request->get('second'),
                    Yii::$app->request->get('link'),
                    Yii::$app->request->get('year'),
                    Yii::$app->request->get('month'),
                    Yii::$app->request->get('page'),
                ]                
            ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => $this->only_actions,
                'etagSeed' => function ($action, $params) {
                    return serialize(Yii::$app->request->get());
                },
            ],
        ];
    }    
    
    public $defaultPageSize = 10;
    
    public $layout = 'theme1_sidebar';

    public function actionPostpage ($first = false, $second = false) 
    {
        $sql_parent = "SELECT
            COUNT(wp1.post_parent) as cou,
            wp2.ID,
            wp2.post_name,
            wp2.post_title
            FROM wp_posts as wp1
            LEFT JOIN wp_posts as wp2 ON wp1.post_parent = wp2.ID
            WHERE wp1.post_parent != 0 
                AND wp1.post_status = 'publish' 
                AND wp1.post_type = 'page'
            GROUP BY wp1.post_parent
            HAVING cou > 1
            ORDER BY cou DESC";
        
        $arr_parent = Yii::$app->db->createCommand($sql_parent)->cache(300)->queryAll();
        $arr = ArrayHelper::map($arr_parent, 'ID','cou','post_name');      
        
        $find = WpPosts::find()->with('author'); 
        
        $find_child = false;
        $prevnext = false;
        $prevnext_get = false;
        $meta = false;
        $meta_get = false;
        
        if ( $first AND !$second AND array_key_exists($first, $arr) ) 
        {
            // СТРАНИЦА имеющая дочерние
            $find = $find->andWhere(['post_name'=>$first]);
            $find_child = WpPosts::find()
                    ->asArray()
                    ->select('post_title, post_name')
                    ->addSelect(['SUBSTRING_INDEX(post_content," ",50) as post_content'])
                    ->where(['post_status'=>'publish','post_type'=>'page'])
                    ->andWhere(['post_parent'=>key($arr[$first])])
                    ->orderBy('ID ASC')
                    ->all();           
        } 
        elseif ( $first AND !$second AND !array_key_exists($first, $arr) ) 
        {
            // ОДИН пост или одна страница
            $find = $find->andWhere(['post_name'=>$first]);
            $prevnext_get = true;
        }         
        elseif ( $first AND $second ) 
        {
            // ОДНА дочерняя страница
            $find = $find->andWhere(['post_name'=>$second]);
            $prevnext_get = true;
        }
        
        $find = $find->andWhere(['post_status' => 'publish']);
        $find = $find->one();       
        
        if (empty($find)) 
        {
            throw new NotFoundHttpException('Такой статьи не существует!');
        }
        
        if ($prevnext_get) 
        {
            $prevnext = @$this->prevnextLinks($find->post_date);
        }
        
        $meta = @$this->metaData($find->ID);
        

        
        if ( $first == 'who-people-lives-well-in-russia' ) 
        {
            $this->layout = 'theme1_fullpage';
        }
        
        return $this->render('postpage', [
            'data' => $find,
            'data_child' => $find_child,
            'prevnext' => $prevnext,
            'meta' => $meta
        ]);
    }     

    public function actionTag ($link = false) 
    {
        
        /**
         * не ставь в конце запроса точку с запятой!
         * не будет работать пагинация!
         */       
        
        $render = 'tag';
        $sql = '';       
        
        if (!$link) 
        {
            // выводим список всех тегов
            
            Yii::$app->view->title = "Ключевые слова на сайте iamruss.ru";
            
            $sql = "SELECT
                        wp_term_taxonomy.count AS posts_count,
                        wp_terms.term_id,wp_terms.name,wp_terms.slug
                    FROM wp_term_taxonomy
                    LEFT JOIN wp_terms USING (term_id)
                    WHERE wp_term_taxonomy.taxonomy = 'post_tag' 
                    ORDER BY posts_count DESC";
            
            $this->defaultPageSize = 30;
            
        } 
        else 
        {
            // выводим записи одного тега
            
            $render = 'tag_one';
            
            $name = Yii::$app->db->createCommand("SELECT name FROM wp_terms WHERE slug = '$link'")->queryOne();
            
             Yii::$app->view->title = "Статьи по ключевому слову &laquo;{$name['name']}&raquo;";
            
            $sql = "SELECT DISTINCT
                        wp_terms.name, wp_terms.slug, 
                        wp_term_taxonomy.term_id,
                        wp_term_relationships.object_id,
                        wp_posts.post_title, wp_posts.post_name, wp_posts.post_content
                    FROM wp_terms
                    LEFT JOIN wp_term_taxonomy USING (term_id)
                    LEFT JOIN wp_term_relationships USING (term_taxonomy_id)
                    LEFT JOIN wp_posts ON wp_term_relationships.object_id = wp_posts.ID
                    WHERE wp_terms.slug = '{$link}' ORDER BY wp_posts.post_date DESC";
                    
            $this->defaultPageSize = Yii::$app->params['opt']['posts_per_page'];
           
        }        
            
        $count = Yii::$app->db->createCommand($sql)->queryAll();
        
        if (empty($count)) 
        {
            throw new NotFoundHttpException('Такой метки не существует!');
        }        

        $provider = new SqlDataProvider ([
            'sql' => $sql,
            'totalCount' => count($count),               
            'pagination' => [
                'defaultPageSize' => $this->defaultPageSize,
            ],
        ]);        
        
        return $this->render($render, [
            'data' => $provider
        ]);
    }
    
    public function actionCategory ($link = false) 
    {
        
        /**
         * не ставь в конце запроса точку с запятой!
         * не будет работать пагинация!
         */       
        
        $render = 'category';
        $sql = '';        
        
        if (!$link) 
        {
            // выводим список всех категорий
            
            $sql = "SELECT
                        wp_term_taxonomy.count AS posts_count,
                        wp_terms.term_id,wp_terms.name,wp_terms.slug
                    FROM wp_term_taxonomy
                    LEFT JOIN wp_terms USING (term_id)
                    WHERE wp_term_taxonomy.taxonomy = 'category' 
                    ORDER BY posts_count DESC";          
            
            $this->defaultPageSize = 30;
            
        } 
        else 
        {
            // выводим записи одной категории
            
            $render = 'category_one';

            $tit = WpTerms::find()
                    ->select('name')
                    ->where(['slug'=>$link])
                    ->one();
            
            if (empty($tit)) 
            {
                throw new NotFoundHttpException('Такой категории не существует!');
            }            
            
            Yii::$app->view->title = $tit->name;
            
            $sql = "SELECT DISTINCT
                        wp_terms.name, wp_terms.slug, 
                        wp_term_taxonomy.term_id,
                        wp_term_relationships.object_id,
                        wp_posts.post_title, wp_posts.post_name, wp_posts.post_content,
                        wp_posts.post_date, wp_users.user_nicename,
                        wp_term_taxonomy.description
                    FROM wp_terms
                    LEFT JOIN wp_term_taxonomy USING (term_id)
                    LEFT JOIN wp_term_relationships USING (term_taxonomy_id)
                    LEFT JOIN wp_posts ON wp_term_relationships.object_id = wp_posts.ID
                    LEFT JOIN wp_users ON wp_users.ID = wp_posts.post_author
                    WHERE wp_terms.slug = '{$link}' AND wp_posts.post_status = 'publish' ORDER BY post_date DESC";
            
            $this->defaultPageSize = Yii::$app->params['opt']['posts_per_page'];
            
        }
            
        $count = Yii::$app->db->createCommand($sql)->queryAll();

        $provider = new SqlDataProvider ([
            'sql' => $sql,
            'totalCount' => count($count),               
            'pagination' => [
                'defaultPageSize' => $this->defaultPageSize,
            ],
        ]); 
        
        $models = $provider->getModels();
        
        return $this->render($render, [
            'data' => $provider
        ]);
    }
    
    public function actionAuthor ($link = false) 
    {
        Yii::$app->view->title = "Статьи автора &laquo;{$link}&raquo;";
        
        $sql = "SELECT
                    wp_posts.post_title,
                    wp_posts.post_name,
                    wp_posts.post_date,
                    wp_terms.name as cat_name,
                    wp_terms.slug as cat_slug,
                    wp_users.user_nicename
                FROM wp_posts
                LEFT JOIN wp_users ON wp_users.ID = wp_posts.post_author
                LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
                LEFT JOIN wp_term_taxonomy USING (term_taxonomy_id)
                LEFT JOIN wp_terms USING (term_id)
                WHERE wp_users.user_nicename = '{$link}'
                    AND wp_term_taxonomy.taxonomy = 'category'
                    AND wp_posts.post_status = 'publish'
                GROUP BY post_title
                ORDER By wp_posts.post_date DESC";
                
        $count = Yii::$app->db->createCommand($sql)->queryAll();

        $provider = new SqlDataProvider ([
            'sql' => $sql,
            'totalCount' => count($count),               
            'pagination' => [
                'defaultPageSize' => $this->defaultPageSize,
            ],
        ]);                
        
        return $this->render('author', [
            'data' => $provider
        ]);
    }
    
    protected function metaData ($ID) 
    {        
        $sql = "SELECT * FROM `wp_postmeta` where post_id = $ID";
        
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        
        $arr = ArrayHelper::map($res, 'meta_key','meta_value');       
        
        if (array_key_exists('ogimg', $arr)) 
        {
            Yii::$app->params['ogimg'] = $arr['ogimg'];
        }
        if (array_key_exists('from_here', $arr)) 
        {
            Yii::$app->params['from_here'] = $arr['from_here'];
        }         
        return $arr;
    }    
    
    protected function prevnextLinks ($post_date) 
    {
        $sql = ""
                . "("
                . "SELECT post_title, post_name "
                . "FROM wp_posts "
                . "WHERE `post_status` = 'publish' AND `post_date` < '{$post_date}' "
                . "ORDER BY `post_date` DESC LIMIT 1)"
                . "UNION"
                . "("
                . "SELECT post_title, post_name "
                . "FROM wp_posts "
                . "WHERE `post_status` = 'publish' AND `post_date` > '{$post_date}' "
                . "ORDER BY `post_date` ASC LIMIT 1)";            
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        $arr['prev'] = @$res[0] ? $res[0] : false;
        $arr['next'] = @$res[1] ? $res[1] : false;
        return $arr;
    }
    
    public $month = [
        '01'=>'январь','02'=>'февраль',
        '03'=>'март','04'=>'апрель','05'=>'май',
        '06'=>'июнь','07'=>'июль','08'=>'август',
        '09'=>'сентябрь','10'=>'октябрь','11'=>'ноябрь',
        '12'=>'декабрь',
    ];
    
}
