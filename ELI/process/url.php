<?php
$defaut = "index.php";
$page = page(@$_GET['url']);
switch ($page){
    
        case "p":
		include('process/post.php');
		break;       
        
        case "LOGOUT":
        unset($_SESSION['user']);
        session_destroy();
        if(!isset($_SESSION['user']))
        {
		  header('location: LOGIN');
        }
		break;       

                                
        default:
        $avoid = explode(',',"www");
        $pageurl = WWW.@$_GET['url'];
        
        if(isset($_GET['url']) && file_exists($pageurl) && $_GET['url']!="" )
        {
            if(!in_array($page,$avoid))
            {
                include($pageurl);
            }
            else
            {
                echo "403 ERROR - ACCESS DENIED";
            }
        }
        else
        {
            //echo "404, File doesn't Exist";
            include(WWW.$defaut);
        }
        
}
?>