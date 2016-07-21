<?php

class ShopController extends Controller 
{
    
    public $layout = '//layouts/inner_sidebar';
    
    public function actionIndex()
    {
        $this->pageTitle = 'Фирменный магазин Микояновского мясокомбината';
        $this->render('index');
    }     
    
    public function actionTodayinshop($id = false)
    {
        $this->pageTitle = 'Акции в магазине Микояновского мясокомбината';
        $now = date("Y-m-d H:i:s");       
        $crit = new CDbCriteria();
        $crit->select = "*,"
                . PVC::mysqlDate('dt_start').","
                . PVC::mysqlDate('dt_end');
        $crit->condition = "`show` = 'yes' AND `dt_end` > '$now'";
        $crit->order = "importance, name ASC";  
        $one_action = false;
        
        if ($id) 
        {
            // одна акция
            $one_action = true;
            $crit->condition .= " AND `id_sale` = '$id'";
        }

        $dataProvider=new CActiveDataProvider('SaleNew', array(
            'criteria'   => $crit,
            'pagination'=>array(
                'pageSize'=> 100,
            ),            
        ));        
        
        $this->render('todayinshop', array(
            'dataProvider' => $dataProvider,
            'one_action' => $one_action,
        ));
    } 
    
    public function actionEvents($id = false)
    {
        $this->pageTitle = 'События магазина Микояновского мясокомбината';
        
        $now = date("Y-m-d H:i:s");       
        $crit = new CDbCriteria();
        $crit->select = "*,"
                . PVC::mysqlDate('dt_start').","
                . PVC::mysqlDate('dt_end');
        $crit->condition = "`show` = 'yes' AND `dt_end` > '$now'";
        $crit->order = "`dt_end` DESC, `name` ASC";     
        $one_event = false;
        
        if ($id) 
        {
            // одно событие
            $one_event = true;
            $crit->condition .= " AND `id_ev` = '$id'";
        }

        $dataProvider=new CActiveDataProvider('Events', array(
            'criteria'   => $crit,
            'pagination'=>array(
                'pageSize'=> 100,
            ),            
        ));
        
        $this->render('events', array(
            'dataProvider' => $dataProvider,
            'one_event' => $one_event,
        ));
    }    
        
}
