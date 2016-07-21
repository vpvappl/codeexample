<?php

namespace rest\modules\v1\models;

use Yii;
use frontend\models\WpUsers;
use frontend\models\WpTerms;
use frontend\models\WpTermTaxonomy;
use frontend\models\WpTermRelationships;
use frontend\models\WpPostmeta;

class WpPosts extends \yii\db\ActiveRecord
{  
    
    public $post_link;
    public $post_anons = false;
    
    public function fields()
    {        
        $fields = parent::fields(); 
        
        // add new field
        $fields['post_link'] = function ($model) {
            $model->post_link = "http://iamruss.ru/{$model->post_name}/";
            return $model->post_link;
        };
        
        $fields['post_anons'] = function ($model) {
            $anons = $model->post_anons;
                $anons = strip_tags($anons);
                $anons = trim($anons);
                $anons = preg_replace('/\r\n/si', ' ', $anons);
            return $anons;
        };
        
        $fields['post_content'] = function ($model) {
            require($_SERVER['DOCUMENT_ROOT'].'/advanced/frontend/components/wp_helper.php');
            return wpautop($model->post_content);
        };

        // remove some fields
        unset($fields['post_name']); 
        
        if (!$this->post_anons) 
        {
            unset($fields['post_anons']);
        }

        return $fields;
    } 
    
    public function oneArticle ($link = false) 
    {
        $sql = "SELECT "
                . "post_date, post_title,post_content,post_name "
                . "FROM wp_posts "
                . "WHERE wp_posts.post_status = 'publish' ";
        if ($link) 
        {
            $sql .= "AND wp_posts.post_name = '{$link}'";
        } 
        else 
        {
            $sql .= "ORDER BY wp_posts.post_date DESC "
                    . "LIMIT 1";
        }

        $data = parent::findBySql($sql)->one();
        return $data;
    }    
    
    public function lastArtAnons ($count=10) 
    {  
        $sql = "SELECT "
                . "wp_posts.post_title,wp_posts.post_name,"
                . "'post_link',"
                . "SUBSTRING_INDEX(post_content, ' ', 30) as 'post_anons', "
                . "wp_posts.post_date "
                . "FROM wp_posts "
                . "WHERE wp_posts.post_status = 'publish' "
                . "AND wp_posts.post_type = 'post' "
                . "ORDER BY wp_posts.post_date DESC "
                . "LIMIT {$count}";
        $data = parent::findBySql($sql)->all();
        return $data;
    }    
    
    public function lastArt ($count=10) 
    {  
        $sql = "SELECT "
                . "wp_posts.post_title,wp_posts.post_name,"
                . "'post_link',"
                . "wp_posts.post_date "
                . "FROM wp_posts "
                . "WHERE wp_posts.post_status = 'publish' "
                . "AND wp_posts.post_type = 'post' "
                . "ORDER BY wp_posts.post_date DESC "
                . "LIMIT {$count}";
        $data = parent::findBySql($sql)->all();
        return $data;
    } 
    
    // ------------------------------------------------------------------------
    
    public function infoShort ($count=10, $format = 'xml') 
    {  
        $sql = "SELECT "
                . "wp_posts.post_title,wp_posts.post_name,"
                . "'post_link',"
                . "wp_posts.post_date "
                . "FROM wp_posts "
                . "WHERE wp_posts.post_status = 'publish' "
                . "AND wp_posts.post_type = 'post' "
                . "ORDER BY wp_posts.post_date DESC "
                . "LIMIT {$count}";
        $data = parent::findBySql($sql)->all();
        $result = $data;
        return $result;
    }     
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wp_posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_author', 'post_parent', 'menu_order', 'comment_count'], 'integer'],
            [['post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'], 'safe'],
            [['post_content', 'post_title', 'post_excerpt', 'to_ping', 'pinged', 'post_content_filtered'], 'required'],
            [['post_content', 'post_title', 'post_excerpt', 'to_ping', 'pinged', 'post_content_filtered'], 'string'],
            [['post_status', 'comment_status', 'ping_status', 'post_password', 'post_type'], 'string', 'max' => 20],
            [['post_name'], 'string', 'max' => 200],
            [['guid'], 'string', 'max' => 255],
            [['post_mime_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'post_author' => 'Post Author',
            'post_date' => 'Post Date',
            'post_date_gmt' => 'Post Date Gmt',
            'post_content' => 'Post Content',
            'post_title' => 'Post Title',
            'post_excerpt' => 'Post Excerpt',
            'post_status' => 'Post Status',
            'comment_status' => 'Comment Status',
            'ping_status' => 'Ping Status',
            'post_password' => 'Post Password',
            'post_name' => 'Post Name',
            'to_ping' => 'To Ping',
            'pinged' => 'Pinged',
            'post_modified' => 'Post Modified',
            'post_modified_gmt' => 'Post Modified Gmt',
            'post_content_filtered' => 'Post Content Filtered',
            'post_parent' => 'Post Parent',
            'guid' => 'Guid',
            'menu_order' => 'Menu Order',
            'post_type' => 'Post Type',
            'post_mime_type' => 'Post Mime Type',
            'comment_count' => 'Comment Count',
        ];
    }

}
