<?php

class PVC { 
    
    /**
     * print any array
     * @param array $arr
     */    
    public static function printArray($arr){
      echo "<pre>";
      print_r($arr);
      echo "</pre>";
    }    

    /**
     * get photo from original
     * @param string $from URL to original photo
     * @example //http://img-fotki.yandex.ru/get/38/styleroom.b/0_19e4a_3c2689ab_orig
     * @return array
     */
    public static function getAllPhotoFromOrig($from)
    {      
      $base = substr($from, 0, strrpos($from, "_"));
      $arr = array(
          '50'=>$base.'_XXXS.jpg',
          '75'=>$base.'_XXS.jpg',
          '100'=>$base.'_XS.jpg',
          '150'=>$base.'_S.jpg',
          '300'=>$base.'_M.jpg',
          '500'=>$base.'_L.jpg',
          '800'=>$base.'_XL.jpg',
          'orig'=>$base.'_orig.jpg',
      );
      return $arr;

    }          
      
    /**
     * resize photo for shop department
     * @return array
     */
    public static function imgResizeShop($obj,$dst_w,$prefix=FALSE)
    { 

        $folder = 'sale';

      $path = $obj->tempName;

      $src_info = getimagesize($path);
      $src_info_width = $src_info[0];
      $src_info_height = $src_info[1];
      $src_info_mime = $src_info['mime'];

      switch ($src_info_mime) 
      {
        case 'image/jpeg':
            # создали изображение из оригинала
            $src_image = imagecreatefromjpeg($path); 
            break;      
        case 'image/gif':
            # создали изображение из оригинала
            $src_image = imagecreatefromgif($path); 
            break; 
        case 'image/png':
            # создали изображение из оригинала
            $src_image = imagecreatefrompng($path); 
            break;      
      } 

      $src_w = imagesx($src_image);
      $src_h = imagesy($src_image);

      # уменьшаем под размер по вертикали
      $dst_h = $dst_w * $src_h / $src_w;
      $dst_image = imagecreatetruecolor($dst_w,$dst_h);

      $img_name_new = self::nameRusToEngAddTime($obj->name,$prefix);

      $destination = $_SERVER['DOCUMENT_ROOT']."/$folder/";

      // =====================================================================

      if($src_info_width > 1000 OR $src_info_height > 600)
      {
          //уменьшаем и новое фото на сервер
          imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
          // сохранили превьюшку на сервере
          imagejpeg($dst_image, $destination . $img_name_new, 100); 
      }
      else
      {
          //копируем как есть
          // скопировали оригинал на сервер
          move_uploaded_file($obj->tempName, $destination . $img_name_new); 
      }

      // =====================================================================

      imagedestroy($dst_image);
      imagedestroy($src_image);

      $base_url = Yii::app()->request->getHostInfo();

      $full_path = $base_url . '/' . $folder . '/' . $img_name_new;

      $arr = array(
          'full_path'=>$base_url . '/' . $folder . '/' . $img_name_new,
          'dir_path'=>'/' . $folder . '/' . $img_name_new,
          'img_path'=>$img_name_new,
      );

      return $arr;
    }   

      public static function quotesReplaceBack($str)
      {
          $replace = array('"','«','»');
          $search = array('&quot;','&laquo;','&raquo;');
          return str_replace($search, $replace, $str);        
      }

      public static function quotesRemove($str)
      {
          $search = array('"','«','»',"'");
          $replace = array('');
          return str_replace($search, $replace, $str);        
      }    


      public static function quotesReplace($str)
      {
          $search = array('"','«','»');
          $replace = array('&quot;','&laquo;','&raquo;');
          return str_replace($search, $replace, $str);        
      }

      public static function printArr($arr)
      {
          echo "<pre>";
          print_r($arr);
          echo "</pre>";
      }

      public static function nameRusToRusAddTimeClear($str)
      {
          $name = substr($str, 0, strripos($str, '.'));
          $ext = substr($str, strripos($str, '.'));
          $tr = self::$tr_clear;
          return strtr($name,$tr).'_'.time().$ext;
      }    

      public static function nameRusToEngAddTime($str,$prefix = FALSE)
      {
          $name = substr($str, 0, strripos($str, '.'));
          $ext = substr($str, strripos($str, '.'));
          $tr = self::$tr;
          $pref = $prefix ? $prefix.'_' : FALSE;
          return $pref . strtr($name,$tr).'_'.time().$ext;
      } 

      public static function nameRusToRusClear($str)
      {
          $name = substr($str, 0, strripos($str, '.'));
          $ext = substr($str, strripos($str, '.'));
          $tr = self::$tr_clear;
          return strtr($name,$tr).$ext;
      } 

      public static function nameRusToEngWithoutPoint($str)
      {
          $tr = self::$tr_defis;
          return strtr($str,$tr);
      }    

      public static function nameRusToEng($str)
      {
          $name = substr($str, 0, strripos($str, '.'));
          $ext = substr($str, strripos($str, '.'));
          $tr = self::$tr;
          return strtr($name,$tr).$ext;
      }

      public static $tr_clear = array(
                "."=>""," "=>"_","?"=>"","/"=>"","\\"=>"",
                "*"=>"",":"=>"","'"=>"","\""=>"_","<"=>"",
                ">"=>"","|"=>"","«"=>"","»"=>"",","=>"","("=>"",")"=>""
            );

      public static $tr_defis = array(
                "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
                "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
                "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
                "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
                "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
                "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
                "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
                "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
                "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
                "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
                "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
                "."=>""," "=>"-","?"=>"","/"=>"","\\"=>"",
                "*"=>"",":"=>"","'"=>"","\""=>"-","<"=>"",
                ">"=>"","|"=>"","«"=>"","»"=>"",","=>"","("=>"",")"=>""
            );    

      public static $tr = array(
                "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
                "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
                "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
                "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
                "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
                "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
                "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
                "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
                "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
                "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
                "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
                "."=>""," "=>"_","?"=>"","/"=>"","\\"=>"",
                "*"=>"",":"=>"","'"=>"","\""=>"_","<"=>"",
                ">"=>"","|"=>"","«"=>"","»"=>"",","=>"","("=>"",")"=>""
            );

      public static $arr_month = array(
          '01'=>'января',
          '02'=>'февраля',
          '03'=>'марта',
          '04'=>'апреля',
          '05'=>'мая',
          '06'=>'июня',
          '07'=>'июля',
          '08'=>'августа',
          '09'=>'сентября',
          '10'=>'октября',
          '11'=>'ноября',
          '12'=>'декабря'
          );

    public static $arr_day_in_month = array(
        '01'=>'31',
        '02'=>'29',
        '03'=>'31',
        '04'=>'30',
        '05'=>'31',
        '06'=>'30',
        '07'=>'31',
        '08'=>'31',
        '09'=>'30',
        '10'=>'31',
        '11'=>'30',
        '12'=>'31'
        );

}
