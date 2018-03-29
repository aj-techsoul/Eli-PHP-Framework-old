<?php
if(isset($_FILES["FileInput"]) && $_FILES["FileInput"]["error"]== UPLOAD_ERR_OK  && check_csrf($csrf_got))
{
    ############ Edit settings ##############
    $UploadDirectory    = 'user/'; //specify upload directory ends with / (slash)
    $userid = $_SESSION['user']['id'];
    ##########################################
    
    /*
    Note : You will run into errors or blank page if "memory_limit" or "upload_max_filesize" is set to low in "php.ini". 
    Open "php.ini" file, and search for "memory_limit" or "upload_max_filesize" limit 
    and set them adequately, also check "post_max_size".
    */
    
    //check if this is an ajax request
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
        die();
    }
    
    
    //Is file size is less than allowed size.
    if ($_FILES["FileInput"]["size"] > 5242880) {
        die("File size is too big!");
    }
    //echo $_FILES['FileInput']['type'];
    //allowed file type Server side check
    switch(strtolower($_FILES['FileInput']['type']))
        {
            //allowed file types
            case 'image/png': 
            case 'image/gif': 
            case 'image/jpeg': 
            case 'image/pjpeg':
            case 'text/plain':
            case 'text/html': //html file
            case 'application/x-zip-compressed':
            case 'application/pdf':
            case 'application/msword':
            case 'application/vnd.ms-excel':
            case 'video/mp4':
            break;
            default:
               // die('<script>Materialize.toast(" Unsupported File!", 20000,"red");</script>'); //output error
               die('Unsupported File!');
    }
    
    $foldertype = explode('/',strtolower($_FILES['FileInput']['type']));
    $foldertype = $foldertype[0];
    switch($foldertype)
    {
        case "image":
        $folder = "photo/";
        break;
        case "text":
        $folder = "doc/";
        break;
        case "application":
        $folder = "application/";
        break;
        case "video":
        $folder = "video/";
        break;
        
    }
    
    if(!file_exists($UploadDirectory.$folder))
    {
        mkdir($UploadDirectory.$folder,777);
    }
    
    $File_Name          = strtolower($_SESSION['user']['id']."-".$_FILES['FileInput']['name']);
    $File_Ext           = substr($File_Name, strrpos($File_Name, '.')); //get file extention
    $Random_Number      = rand(0, 9999999999); //Random number to be added to name.
    $NewFileName        = $_SESSION['user']['id']."-".$Random_Number.$File_Ext; //new file name
    
    if(move_uploaded_file($_FILES['FileInput']['tmp_name'], $UploadDirectory.$folder.$NewFileName ))
       {
        // do other stuff 
        $uploadeddata['userid'] = $userid;
        $uploadeddata['title']=$_FILES['FileInput']['name'];
        $uploadeddata['type']=strtolower($_FILES['FileInput']['type']);
        $uploadeddata['filepath'] = $UploadDirectory.$folder.$NewFileName;
        $uploadeddata['logdate'] = date('Y-m-d H:i:s');
        
                $uploaded = new TSMYSQL;
                $uploaded->connect_ts();
                $uploaded->insert_ts('files',$uploadeddata);
                $uploaded->disconnect_ts();
                
               die('<script>Materialize.toast("Success! File Uploaded", 5000,"green");</script>');
    }else{
        die('<script>Materialize.toast("error uploading File!", 5000,"red");</script>');
    }
    
}
else
{
    die('<script>Materialize.toast("Something wrong with upload! Is "upload_max_filesize" set correctly?", 5000,"orange");</script>');
}
?>