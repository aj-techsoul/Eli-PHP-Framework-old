<?php

/**
 *  ELI FRAMEWORK
 *  FRAMEWORK CREATED BY AJAY KUMAR (TECHSOUL.IN) // EAGLE LITE - ELI FRAMEWORK
 *  +91 9862542983
 *  techsoul4@gmail.com
 */


include('define.php');
?>
<?php
	function page($url2)
    {
        $url = explode('/', $url2);
        return $url;
    }
?>
<?php
 $classes =	scandir(CLASS_DIR);
 $exclude = explode(',','.,..,');
 foreach($classes as $c)
 {
    if(!in_array($c,$exclude))
    {
        if(file_exists(CLASS_DIR.$c))
        {
            include(CLASS_DIR.$c);
        }
    }
 }
?>
<?php
	function csrf()
    {
        if(!isset($_SESSION)){
        session_start();
        $_SESSION['formStarted'] = true;
        }
        if (!isset($_SESSION['token']))
        {
            $token = md5(date('Y-m-d-H-i').uniqid(rand(), TRUE));
            $_SESSION['token'] = $token;
        }
    }

	function check_csrf($csrf_got)
    {
        if ($_SESSION['token'] == $csrf_got)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }  
    }
?>
<?php
function getData($query,$optionvalue="N")
{

        $getdata = new TSMYSQL();
        $getdata->connect_ts();
        $query = $query;
        $option_array = $getdata->query_ts($query);        
        $getdata->disconnect_ts();        
if($optionvalue=="N")
{
if(count($option_array)>0)
{
    $opitem = "";
       foreach($option_array as $op)
       {
        $opitem.="$op[item],";
       }
}
return $opitem;
}
else
{
    if(count($option_array)>0)
    {
           foreach($option_array as $op)
           {
             $opitem["$op[id]"]="$op[item]";
           }
    }
    return $opitem;   
}
}
?>