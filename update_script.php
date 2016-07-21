<?php

set_time_limit(0);
$url_yml = 'partners_utf.yml';

/**
 * 1. открытие лога ошибок и файла YML ----------------------------------------
 */
$hand_log = fopen("error_mysql_log.txt", "a");
ftruncate($hand_log, 0);

$handle = @fopen($url_yml, "r");

$stop_arr = array('\\','/');


/**
 * 2. подключение к базе данных -----------------------------------------------
 */
$host="localhost";
$user="admin_fast";
$password="admin_fast";
$db="admin_fast";
$link = mysql_connect($host, $user, $password);
mysql_select_db($db);
mysql_set_charset('utf8');




/**
 * 3. работа с жанрами - genres -----------------------------------------------
 */
mysql_query('TRUNCATE TABLE `genres`');

$res_g = '';
$i_g = 0;
if ($handle) 
{
    while (($buffer_g = fgets($handle, 4096)) !== false AND $i_g < 50) 
    {
        $res_g .= $buffer_g;
        $i_g++;
    }
    fclose($handle);
}

$patt_g = '/<categories>(.*?)<\/categories>/si';
preg_match($patt_g, $res_g, $out_g);
$categories_g = $out_g[1];

$xml_str_g = '<?xml version="1.0" encoding="UTF-8"?><root>'.$categories_g.'</root>';
$xml_g = simplexml_load_string($xml_str_g);

$start_ins_g = "INSERT INTO `genres` (`id_gen`, `id_parent`, `name`) VALUES ";

foreach($xml_g->category as $val_g) 
{
    $id_g = $val_g->attributes()->id;
    $parentId_g = $val_g->attributes()->parentId;
    $name_g = trim($val_g);
        $nm_g = str_replace($stop_arr, '', $name_g);
        $nm_g = mysql_real_escape_string($nm_g);
    $ins_g = $start_ins_g . "('$id_g','$parentId_g','$nm_g');";
    mysql_query($ins_g); 
    
    if (mysql_errno()) 
    {
        $error_g = 'GENRES === '.mysql_errno() . ": " . mysql_error()."\n".$ins_g."\n\n\n";
        echo $error_g;
        fwrite($hand_log, $error_g);
    }    
    
}


/**
 * 4. работа с книгами и всем остальным ---------------------------------------
 */
$reader = new XMLReader();
$reader->open($url_yml);

$err_start = 0;

while($reader->read()) 
{
    if($reader->name == "offer") 
    {
        $xml = simplexml_load_string($reader->readOuterXML());
        
        /**
         * variables ----------------------------------------------------------
         */
        $id_off = $xml->attributes()->id;
        $type = trim($xml->attributes()->type);
        $url = $xml->url;
        $price = $xml->price;
        $id_gen = $xml->categoryId;
        $picture = $xml->picture;
        $autor = trim($xml->author);
        $name = $xml->name;
        $publisher = trim($xml->publisher);
        $series = trim($xml->series);
        $year = trim($xml->year);
        $isbn = trim($xml->ISBN);        
            $isbn = str_replace($stop_arr, '', $isbn);
        $description = $xml->description;
            $description = trim($description);
            $description = str_replace(array("\r\n","\n"), '', $description);
        $age = trim($xml->age);
        
        /**
         * checking -----------------------------------------------------------
         */

        ### clear string
        $aut = mysql_real_escape_string($autor);
        $pub = mysql_real_escape_string($publisher);
        $ser = mysql_real_escape_string($series);        
        $isbn = mysql_real_escape_string($isbn);
        
        # types
        if (!empty($type)) 
        {
            mysql_query("INSERT INTO `types` VALUES (NULL, '$type') ON DUPLICATE KEY UPDATE `id_type` = `id_type`");          
        }        
        
        
        # authors
        if (!empty($autor)) 
        {            
            mysql_query("INSERT INTO `authors` VALUES (NULL, '$aut') ON DUPLICATE KEY UPDATE `id_auth` = `id_auth`");
        }
        
        
        # publishers
        if (!empty($publisher)) 
        {            
            mysql_query("INSERT INTO `publishers` VALUES (NULL, '$pub') ON DUPLICATE KEY UPDATE `id_pub` = `id_pub`");
        } 
        
        
        # series
        if (!empty($series)) 
        {            
            mysql_query("INSERT INTO `series` VALUES (NULL, '$ser') ON DUPLICATE KEY UPDATE `id_ser` = `id_ser`");
        } 
        
        
        /**
         * insert books -------------------------------------------------------
         */
        
        $row_id_auth = mysql_fetch_row(mysql_query("SELECT id_auth FROM authors WHERE name = '$aut' LIMIT 1"));
        $row_id_ser = mysql_fetch_row(mysql_query("SELECT id_ser FROM series WHERE name = '$ser' LIMIT 1"));
            if (empty($row_id_ser[0])) 
            {
                $row_id_ser[0] = 999999;
            }
        $row_id_pub = mysql_fetch_row(mysql_query("SELECT id_pub FROM publishers WHERE name = '$pub' LIMIT 1"));
        $row_id_type = mysql_fetch_row(mysql_query("SELECT id_type FROM types WHERE name = '$type' LIMIT 1"));
        
        $nm = mysql_real_escape_string($name);
        $descr = mysql_real_escape_string($description);
        
        if (empty($year)) 
        {
            $year = '111';
        }
        
        if (empty($isbn)) 
        {
            $isbn = '777';
        }
        
        if (empty($age)) 
        {
            $age = '99';
        }        
        
        $sql = "INSERT IGNORE INTO `books` VALUES ("
                . "$id_off, "
                . "'$nm', "
                . "'$descr', "
                . "{$row_id_auth[0]}, "
                . "{$row_id_ser[0]}, "
                . "{$row_id_pub[0]}, "
                . "$id_gen, "
                . "{$row_id_type[0]}, "
                . "'$url', "
                . "'$picture', "
                . "$price, "
                . "'$year', "
                . "'$isbn',"
                . "NOW(),"
                . "'$age');";
        
        if ( !empty($name) ) 
        {
            mysql_query($sql);            
            if (mysql_errno()) 
            {
                $error = 'ALL === '.mysql_errno() . ": " . mysql_error()."\n".$sql."\n\n\n";
                echo $error;
                fwrite($hand_log,$error);
            }
            $sql = false;
        }              
        
    }
    
}

/**
 * 5. отключение от базы данных и закрытие лога -------------------------------
 */
mysql_close($link);
fclose($hand_log);
