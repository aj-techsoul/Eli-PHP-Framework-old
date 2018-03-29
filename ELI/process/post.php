<?php
$csrf_got = @$_POST['csrf'];
$validateEmail = false; // VALIDATE EMAIL (true/false)

switch ($url[1]){
    
        case "login":
		
        if(isset($_POST['email']) && isset($_POST['password']) && check_csrf($csrf_got))
        {
           $email = addslashes($_POST['email']);
           $pass = addslashes($_POST['password']);
           $sql = "SELECT username,email,role,status validate,userid id FROM user WHERE email='$email' AND password='$pass'"; 
           //echo $sql;
            $result = new TSMYSQL;
            $result->connect_ts();
            $data = $result->query_ts($sql);
            $userid = $data[0]['id'];
            
            if($data[0]['role']==2)
            {
                $companydata = $result->query_ts("SELECT id,company_name,industry_type,logo,verified,cwebsite,rate FROM company WHERE userid='$userid'");
            }
            //---  SETTINGS -----
            $settings = $result->query_ts("SELECT settings,value,logdate FROM settings WHERE userid='$userid' ");
            if(COUNT($settings)>0)
            {
                foreach($settings as $set)
                {
                    $setting[$set['settings']] = $set['value'];
                }
            }
            
            //--------//
            
            $result->disconnect_ts();
            
           // print_r($data);
            
            if(COUNT($data)>0)
            {             
if($data[0]['validate']=='Y' && $validateEmail)
{
              $_SESSION['user'] = $data[0];
              $_SESSION['settings'] = @$setting;
              logit('login');
              // echo "<div class='alert alert-success' >Successfull Login</div>";
              echo '<script type="text/javascript"> 
                      swal({
                          title: "Great!",
                          text: "Login Successful!",
                          type: "success",
                          timer: 2500,
                          showConfirmButton: false
                        }); 
              </script>';
              //sleep(3);
             // GET CURRENT PAGES
               $refer = $_SERVER["HTTP_REFERER"];
               $ref = end(explode('/',$refer));
               $urlquery = end(explode('?',$ref));
               $ref = str_replace($urlquery,'',$ref);
               $ref = str_replace("?",'',$ref);
               
               if($ref!="LOGIN")
               {
                    $redirect = $refer;
               }
               else
               {
                $redirect = _BASEPATH_."HOME";
               }
              ////////////////////
              echo "<script type='text/javascript'> setTimeout(function(){ window.location = '".$redirect."'; }, 3000); </script>";
               
}
elseif($data[0]['validate']!='Y' && $validateEmail)
{
    //echo "<div class='alert alert-warning' >You cannot Login Now, Please check your email and get validated</div>";
    echo '<script type="text/javascript"> swal("You cannot Login Now!", "Please check your email and get validated!"); </script>';
}
else
{
                $_SESSION['user'] = $data[0];
                $_SESSION['settings'] = @$setting;
                logit('login');
                if(count($companydata)>0)
                {
                    $_SESSION['company'] = $companydata[0];
                }
              // echo "<div class='alert alert-success' >Successfull Login</div>";
              echo '<script type="text/javascript"> 
                      swal({
                          title: "Great!",
                          text: "Login Successful!",
                          type: "success",
                          timer: 2500,
                          showConfirmButton: false
                        }); 
              </script>';
              //sleep(3);
              // GET CURRENT PAGES
               $refer = $_SERVER["HTTP_REFERER"];
               $ref = end(explode('/',$refer));
               $urlquery = end(explode('?',$ref));
               $ref = str_replace($urlquery,'',$ref);
               $ref = str_replace("?",'',$ref);
               if($ref!="LOGIN")
               {
                    $redirect = $refer;
               }
               else
               {
                $redirect = _BASEPATH_."HOME";
               }
              ////////////////////              
              echo "<script type='text/javascript'> setTimeout(function(){ window.location = '".$redirect."'; }, 3000); </script>";    
}
                             
            }
            else
            {
               //  echo "<div class='alert alert-danger' >Invalid Login, Kindly Try Again!</div>";
               echo '<script type="text/javascript"> swal("Oops", "Invalid Login, Kindly Try Again!", "error"); </script>';
               logit('login',"Invalid Login");
            }
            
        }
        else
        {
            //echo "<div class='alert alert-warning' >Invalid Login, Try Again!</div>";
            echo '<script type="text/javascript"> swal("Oops", "Invalid Login, Kindly Try Again!", "error"); </script>';
        }
        
        
		break; 
      
      case "signup":
        
        $validate = new validator;
        
      //  print_r($_POST);
        if(isset($_POST['name']) && $validate->validate_name($_POST['name']) && isset($_POST['email']) && isset($_POST['role']) && is_numeric($_POST['role']) && $validate->validate_email($_POST['email']) && isset($_POST['password']) && check_csrf($csrf_got))
        { 
           $name=ltrim($_POST['name']);
           $email=ltrim($_POST['email']);
           $mobile=ltrim($_POST['phone']);
           $mobile=str_replace(' ','',$mobile);
            
           $name=rtrim($name);
           $email=rtrim($email);
           $mobile=rtrim($mobile);
            
           $name=addslashes($name);
           $email=addslashes($email);
           $mobile=addslashes($mobile);
           $pass=addslashes($_POST['password']);
           $logdate = date("Y-m-d H:i:s");
           
           $roleid = @$_POST['role'];
           
           $fullname = $validate->validate_fullname($name);
           
            $result = new TSMYSQL;
            $result->connect_ts();
            $emailcheck = "SELECT COUNT('email') total FROM `personal` WHERE `email` LIKE '$email'";
            $echeck = $result->query_ts($emailcheck);
            if($echeck[0]['total']==0)
            {
               $fdata['firstname'] = $fullname['fname'];
               $fdata['midname'] = $fullname['mname'];
               $fdata['lastname'] = $fullname['lname'];
               $fdata['email'] = $email;
               $fdata['mobile'] = $mobile;
               $fdata['status'] = 1;
               $fdata['logdate'] = $logdate;
               
               $fdata2['username'] = $fullname['fname'];
               $fdata2['email'] = $email;
               $fdata2['role'] = $roleid;
               $fdata2['status'] = 1;
               $fdata2['password'] = $pass;
                  
      
            $data = $result->insert_ts('personal',$fdata);
            
           // print_r($data);
            
            $fdata2['userid'] = $data['id'];
            $data2 = $result->insert_ts('user',$fdata2);
            
            $userid = $data['id'];
            $result->disconnect_ts();

            if($data>0 && mysql_error()=="")
            {
              // Use Rich Email
              $RichEmail = true;               
              
              /////////////////////////////////
              $datenow = date("F j, Y, g:i a");
              //
              $ma['verifyemailurl']=_BASEPATH_;
              $ma['secret'] = urlencode(base64_encode($userid."|".$email));
              $ma['to'] = $GLOBALS['impMail'];
              $ma['subject'] = "Thank You for Signing Up in {$GLOBALS[site][name]} \n";
              
              $htmlemail = file_get_contents('default/email_verification_ui.php');
              $htmlemail = str_replace('{name}',$fullname['fname'],$htmlemail);
              $htmlemail = str_replace('{sitename}',$GLOBALS['site']['name'],$htmlemail);
              $htmlemail = str_replace('{email}',$email,$htmlemail);
              $htmlemail = str_replace('{verification_url}',"$ma[verifyemailurl]/validate/$ma[secret]",$htmlemail);
              $htmlemail = str_replace('{siteurl}',_BASEPATH_,$htmlemail);
              //
              
              if($RichEmail)
              {
                 $ma['msg'] = $htmlemail;                
              }
              else
              {              
              $ma['msg'] = "Dear $fullname[fname],";
              $ma['msg'].=($ValidateEmail)?"\n\nWelcome to {$GLOBALS[site][name]}, $fullname[fname]!\n\n Verify your email by clicking at below link\n\n\n $ma[verifyemailurl]/validate/$ma[secret] \n\n\nWhy verify?\nMany people using {$GLOBALS[site][name]} require a verified email for commenting to prevent spam. Verifying lets you login {$GLOBALS[site][name]} quickly and easily.\n":" below is your information given \n Name : $name \n Phone: $mobile \n Email: $email";
              $ma['msg'].= "\n\n\n Thank You\n {$GLOBALS[site][name]} Team";
              }
               
              $mail = new communicate;
            //  $msa= $mail->mailme($ma['to'],$ma['msg'],$ma['subject'],$email); // Sending to Admin
              $msa= $mail->mailme($email,$ma['msg'],$ma['subject'],$ma['to'],'Y'); // Sending to User
              $arr = explode(" ",$msa);
              //echo $msa;
              if(in_array("failed",$arr))
              {
                $mailreport = false;
              }
              elseif(in_array("Sent",$arr))
              {
                $mailreport = true;
              } 
              
              if(@$ValidateEmail)
              {
              if($mailreport)
              {  
              echo '<script type="text/javascript"> 
                    swal({
                      title: "WOW!",
                      text: "<strong>SignUp Successfully</strong><br /> <strong>Email</strong> sent to <strong>'.$email.'</strong> for <strong>Verification</strong><br /><small>Please check your email</small>",
                      type: "success",
                      html: true
                    }); 
                    </script>';
              }
              else
              {
              echo '<script type="text/javascript"> 
                    swal({
                      title: "SignUp Successfully",
                      text: "<strong>Email sending Failed</strong>",
                      type: "info",
                      html: true
                    }); 
                    </script>';                 
              }
              }
              else
              {
               echo '<script type="text/javascript"> swal("WOW!", "SignUp Successful!", "success"); </script>';
              }
              
              
              
              //echo ($ValidateEmail)? "<div class='alert alert-success' ><strong>SignUp Successfully</strong> &amp; <strong>Email Verification sent to $email.</strong></div>":"<div class='alert alert-success' >SignUp Successfully</div>";             
              
           //   $msg = new mailmsg;
           //   $msgs = $msg->msg('signup');
           //   $msgme = $msg->designmsg("name[[[]]]$name;stud_id[[[]]]$userid;phone[[[]]]$phone;pass[[[]]]$pass;",$msgs);
              
            //  $comunicate = new communicate;
            //  $comunicate->sms($mobile,$msgme['sms']);
            //  $comunicate->mailme($email,$msgme['mail'],$msgme['subject']);
              
                            
            }
             else
             {
                //echo "<div class='alert alert-warning' >Duplicate Email not Allowed</div>";
                echo '<script type="text/javascript"> swal("Oops", "Seems you already register, Kindly Login!", "warning"); </script>'; 
                             
             }                         
            }            
            else
            {
                //echo "<div class='alert alert-warning' >Duplicate Email not Allowed</div>";
                echo '<script type="text/javascript"> Materialize.toast("Seems you already register, Kindly Login!", 5000,"red"); </script>';
            }
        }
        else
        {
            //echo "<div class='alert alert-danger' >Kindly fill your valid data</div>";
            echo '<script type="text/javascript"> swal("Oops", "Kindly fill your valid data correctly", "error"); </script>';
        }
		break;

        case "UPLOADFILE":
		if(isset($_SESSION['user']['id']))
        {
                include('uploadfile.php'); // CV UPLOAD
                logit('upload',"CV");
        }
        else
        {
            include(VIEW.'login.php');
        }
       break; 

case "postjob":
if(isset($_POST['jobtitle']) && isset($_POST['jobdesc']) && $_POST['jobtitle']!="" && $_POST['jobdesc']!=""  && check_csrf($csrf_got))
{
    $formdata['jobtitle'] = htmlspecialchars(trim($_POST['jobtitle']));
    $formdata['jobdesc'] = htmlspecialchars(trim($_POST['jobdesc']));
    $formdata['tags'] = htmlspecialchars(trim($_POST['tags']));
    $formdata['userid'] = $_SESSION['user']['id'];
    $formdata['salary_start'] = htmlspecialchars(trim($_POST['salary_start']));
    $formdata['salary_end'] = htmlspecialchars(trim($_POST['salary_end']));
    $formdata['currency'] = htmlspecialchars(trim($_POST['currency']));
    $formdata['salary_per'] = htmlspecialchars(trim($_POST['salary_per']));
    $formdata['logdate'] = date('Y-m-d H:i:s');
    
    $pj = new TSMYSQL;
    $pj->connect_ts();
    $data = $pj->insert_ts('jobs',$formdata);
    $pj->disconnect_ts();
    if($data['success'])
    {
        echo "<strong>success:</strong> Job Posted!";
        echo "<script> window.location.href='MY-JOBS'; </script>";
        logit('JOB POSTED');
    }
    else
    {
        echo "Sorry, unable to post this Job! Please try later...";
        
    }
    
}
else
{
    echo "Please fill data correctly";
}
break;

case "closenotification":
if(is_numeric($_REQUEST['id']) && isset($url[2]) && check_csrf($_GET['csrf']) && isset($_SESSION['user']['id']))
{
    $id = @$_REQUEST['id'];
    $formdata['status']=@$_REQUEST['status'];
    $up = new TSMYSQL;
    $up->connect_ts();
    $updated = $up->update_ts($url[2],$formdata," id='$id' ");
    $up->disconnect_ts();
    //print_r($updated);
    if($updated['success']==true)
    {
        echo ($formdata['status']>0)?"<strong>success :</strong>&nbsp; Active":"<strong>success :</strong>&nbsp; Deactive";
        echo ($formdata['status']>0)? logit("ACTIVATED - $url[2]"):logit("DEACTIVATED - $url[2]");
    }
    else
    {
        echo "Unable to Changed, Try Again!";
    }   
}
else
{
    echo "NO ACTION TAKEN";
}
break;


case "disablerow":
if(is_numeric($_REQUEST['id']) && isset($url[2]) && check_csrf($_GET['csrf']) && isset($_SESSION['user']['id']))
{
    $id = @$_REQUEST['id'];
    $formdata['status']=@$_REQUEST['status'];
    $up = new TSMYSQL;
    $up->connect_ts();
    $updated = $up->update_ts($url[2],$formdata," id='$id' ");
    $up->disconnect_ts();
    //print_r($updated);
    if($updated['success']==true)
    {
        echo ($formdata['status']>0)?"<strong>success :</strong>&nbsp; Job Activated":"<strong>success :</strong>&nbsp; Job Deactivated";
        echo ($formdata['status']>0)? logit("JOB ACTIVATED"):logit("JOB DEACTIVATED");
    }
    else
    {
        echo "Unable to Changed, Try Again!";
    }   
}
else
{
    echo "NO ACTION TAKEN";
}
break;

case "settings":
if(!is_null($_REQUEST['settings']) && is_numeric($_REQUEST['value']) && check_csrf($_GET['csrf']) && isset($_SESSION['user']['id']))
{
    $userid = $_SESSION['user']['id'];
    $formdata['settings']=addslashes($_REQUEST['settings']);
    
    $formdata['value']=@$_REQUEST['value'];
    $up = new TSMYSQL;
    $up->connect_ts();
    $chk = $up->query_ts("SELECT COUNT(userid) total FROM settings WHERE settings LIKE '$formdata[settings]' AND userid='$userid' ");

    if($chk[0]['total']>0)
    {
        $updated = $up->update_ts('settings',$formdata," userid='$userid' AND settings LIKE '$formdata[settings]' ");
    }
    else
    {
        $formdata['userid']=$userid;
        $updated = $up->insert_ts('settings',$formdata);
    }
    
    $up->disconnect_ts();
    //print_r($updated);
    if($updated['success']==true && $formdata['value']==1)
    {
        echo "<strong>Active</strong>";
        $_SESSION['settings'][$formdata['settings']] = $formdata['value'];
        logit($formdata['settings'],$formdata['value']);
    }
    elseif($updated['success']==true && $formdata['value']==0)
    {
        echo "<strong>Inactivated</strong>";
        $_SESSION['settings'][$formdata['settings']] = $formdata['value'];
        logit($formdata['settings'],$formdata['value']);
    }
    else
    {
        echo "<strong>Please try again after sometime</strong>";
    }   
}
else
{
    echo "NO ACTION TAKEN";
}
break;


case "jobdesc":
if(is_numeric($_REQUEST['id']) && check_csrf($_REQUEST['csrf']) )
{
    include('jobdesc.php');
}    
break;

case "savejob":
if(is_numeric($_REQUEST['id']) && check_csrf($_REQUEST['csrf']) && isset($_SESSION['user']['id']))
{
    $id = @$_REQUEST['id'];
    $formdata['id']=@$_REQUEST['id'];
    $formdata['userid']=$_SESSION['user']['id'];
    $formdata['type']='jobs';
    $formdata['logdate']=date('Y-m-d H:i:s');
    
    $up = new TSMYSQL;
    $up->connect_ts();
    $search = $up->query_ts("SELECT COUNT(id) total,saveid FROM saved WHERE id='$id' AND userid='$formdata[userid]' AND type='$formdata[type]' ");
    $delid = $search[0]['saveid'];
    if($search[0]['total']>0)
    {
        $updated = $up->delete_ts('saved'," saveid='$delid' ");
        $ur = 0;
    }
    else
    {    
        $updated = $up->insert_ts('saved',$formdata);
        $ur = 1;
    }
    
    $up->disconnect_ts();
    
    if($ur==1)
    {
        echo ($updated['success']==true)? " SAVED &nbsp; <i class='mdi mdi-heart'></i>" : "Unable to Process, Try Again!";
        logit("JOB","SAVED ".$id);
    }
    elseif($ur==0)
    {
        echo " REMOVED &nbsp; <i class='mdi mdi-heart-outline'></i>";
        logit("JOB","REMOVED SAVE".$id);
    }
    else
    {
        echo "Unable to Process, Try Again!";
    }   
}
else
{
    echo "Please Login to Save Jobs";
}
break;


case "shortlist":
if(is_numeric($_REQUEST['id']) && check_csrf($_REQUEST['csrf']) && isset($_SESSION['user']['id']))
{
    $id = @$_REQUEST['id'];
    $formdata['status']=1;
    $ur = 1;
    $up = new TSMYSQL;
    $up->connect_ts();
    $search = $up->query_ts("SELECT COUNT(id) total,status,userid FROM applied WHERE id='$id' ");
    $toid = $search[0]['userid'];
    $prevstatus = $search[0]['status'];
    if($prevstatus==1)
    {
        $formdata['status']=0;
        $ur = 0;
    }
    if($search[0]['total']>0)
    {
        $updated = $up->update_ts('applied',$formdata," id='$id' ");
    }
    else
    {
        echo "Please try again!";
    }
    
    $up->disconnect_ts();
    
    if($ur==1)
    {
        echo ($updated['success']==true)? " SHORTLISTED &nbsp; <i class='mdi mdi-content-save-all'></i>" : "Unable to Process, Try Again!";
        logit("APPLICANT","SHORTLISTED ".$toid);
    }
    elseif($ur==0)
    {
        echo " REMOVED &nbsp; <i class='mdi mdi-cancel'></i>";
        logit("APPLICANT","SHORTLIST REMOVED ".$toid);
    }
    else
    {
        echo "Unable to Process, Try Again!";
    }   
}
else
{
    echo "Please refresh the page and try again!";
}
break;

case "applyJOB":

    $validate = new validator;
        
        if(is_numeric($_POST['aphone']) && is_numeric($url[2]) && strlen($_POST['aphone'])>9 && check_csrf($csrf_got) && isset($_SESSION['user']['id']))
        {
            $formdata['mobile'] = addslashes($_POST['aphone']);
            $formdata['userid'] = $_SESSION['user']['id'];     
            $formdata['jobid'] = $url[2];
            $formdata['coverletter'] = htmlentities($_POST['acletter']);
            $formdata['cvid'] = addslashes($_POST['selectUploadFile']);
            $formdata['status'] = 0;
            
            $n = new TSMYSQL;
            $n->connect_ts();
            $check = $n->query_ts("SELECT COUNT('id') total,id  FROM applied WHERE userid='$formdata[userid]' AND jobid='$formdata[jobid]' ");
            
            if($check[0]['total']>0)
            {
             $updated = $n->update_ts('applied',$formdata," id='{$check[0][id]}' ");   
            }
            else
            {
             $updated = $n->insert_ts('applied',$formdata);
            }
            $n->disconnect_ts();
            
            if($updated['success'])
            {
                echo "<strong>success</strong> You have successfully Applied for this Job";
                logit("JOB APPLIED","JOBID ".$url[2]);
            }
            else
            {
                echo "Unable to Apply for this Job, Try Again!";
            }
            
                           
        }
        else
        {
            echo "Please enter all information correctly";
        }

break;

case "getOS":
if($url[2]!="" && strlen($url[2])<=10)
{
    $_SESSION['OS'] = $url[2];   
}
else
{
    $_SESSION['OS'] = "NA";   
}
break;


case "add_user":
        
        $validate = new validator;
        
        if(isset($_POST['name']) && $validate->validate_name($_POST['name']) && isset($_POST['email']) && $validate->validate_email($_POST['email']) && is_numeric($_POST['phone']) && isset($_POST['password']) && check_csrf($csrf_got))
        { 
           $name=ltrim($_POST['name']);
           $email=ltrim($_POST['email']);
           $mobile=ltrim($_POST['phone']);
            
           $name=rtrim($name);
           $email=rtrim($email);
           $mobile=rtrim($mobile);
            
           $name=addslashes($name);
           $email=addslashes($email);
           $mobile=addslashes($mobile);
           $pass=addslashes($_POST['password']);
           $logdate = date("Y-m-d H:i:s");
           
            $result = new TSMYSQL;
            $result->connect_ts();
            $sql = "INSERT INTO `users` (`name`, `email`, `pass`, `mobile`, `logdate`,`validate`) VALUES ('$name', '$email', '$pass', '$mobile', '$logdate','U')";
            $data = $result->query_ts($sql);
            $userid = mysql_insert_id();
            $result->disconnect_ts();

            if($data>0 && mysql_error()=="")
            {
               
            
                echo "<div class='alert alert-warning' >User Added</div>";
            
			}
        else
        {
            echo "<div class='alert alert-danger' >Sorry something went wrong..Please try again</div>";
		}
		
		
		}
		else
        {
            echo "<div class='alert alert-danger' >Kindly fill your valid data</div>";
        }
		break;
		
		case "edit_user":
        
        $validate = new validator;
        
        if(isset($_POST['name']) && $validate->validate_name($_POST['name']) && isset($_POST['email']) && $validate->validate_email($_POST['email']) && is_numeric($_POST['phone']) && isset($_POST['password']) && check_csrf($csrf_got))
        { 
           $name=ltrim($_POST['name']);
           $email=ltrim($_POST['email']);
           $mobile=ltrim($_POST['phone']);
            
           $name=rtrim($name);
           $email=rtrim($email);
           $mobile=rtrim($mobile);
            
           $name=addslashes($name);
           $email=addslashes($email);
           $mobile=addslashes($mobile);
           $pass=addslashes($_POST['password']);
           $logdate = date("Y-m-d H:i:s");
		   $id = $_SESSION['edit_id'];
		   
           
            $result = new TSMYSQL;
            $result->connect_ts();
            $sql = "UPDATE `users` SET `name`='$name', `email`='$email', `pass`='$pass', `mobile`='$mobile' WHERE `id`='$id'";
            $data = $result->query_ts($sql);
            $result->disconnect_ts();
			unset($_SESSION['edit_id']);
            if($data>0 && mysql_error()=="")
            {
               
				
                echo "<div class='alert alert-warning' >User Updated</div>";
            
			}
			else
			{
            echo "<div class='alert alert-danger' >Sorry something went wrong..Please try again</div>";
			}
		
		
		}
		else
        {
            echo "<div class='alert alert-danger' >Kindly fill your valid data</div>";
        }
		break;
		
	   case "del_user":

	echo "<div class='alert alert-warning' >test</div>";
		if(isset($_POST['del_id']))
		{
		
	   $del_id = $_POST['del_id'];
	   $result = new TSMYSQL;
       $result->connect_ts();
	   $sql = "DELETE from `users` WHERE `id`='$del_id'";
       $data = $result->query_ts($sql);
       $result->disconnect_ts();
	   
	   if($data>0 && mysql_error()=="")
            {
                echo "<div class='alert alert-warning' >User Deleted</div>";
			}
			else
			{
            echo "<div class='alert alert-danger' >Sorry something went wrong..Please try again</div>";
			}
	   
		}else{
			
			echo "Sorry something Went wrong.please try again";
		}
	   break;
		
       case "update":

//print_r($url);

       $validate = new validator;
        if(isset($_POST) && COUNT($_POST)>0 && isset($url[2]) && $url[2]!="" && check_csrf($csrf_got))
        { 
           $tbl = $url[2];  
           $logdate = date("Y-m-d H:i:s");
		   $id = $_SESSION['user']['id'];
           $formdata = $_POST;
           unset($formdata['csrf']);
            		   
           if(isset($formdata['dob']))
           {
             $formdata['dob'] = date('Y-m-d',strtotime($formdata['dob']));
           }
           if(isset($formdata['dob']))
           {
             $formdata['sdate'] = date('Y-m-d',strtotime($formdata['sdate']));
           }
           if(isset($formdata['dob']))
           {
             $formdata['edate'] = date('Y-m-d',strtotime($formdata['edate']));
           }

            $result = new TSMYSQL;
            $result->connect_ts();
            
switch($tbl)
{
    case "personal":
        $data = $result->update_ts($tbl,$formdata," id='$id' ");
    break;
    case "company":
     $curl = $formdata['cwebsite'];
     $cemail = $formdata['cemail'];
     $dm['domain'] = str_replace('www.','',parse_url($curl, PHP_URL_HOST));
     $dm['emaildomain'] = substr(strrchr($cemail, "@"), 1);
    // print_r($dm);
    if($dm['domain'] == $dm['emaildomain'])
    {
                $companyname = @$_POST['company_name'];
                $empindusty = @$_POST['industry_type'];
                $check = $result->query_ts("SELECT COUNT(id) total,id FROM $tbl WHERE company_name LIKE '$companyname' AND industry_type LIKE '$empindusty' ");
                $eid = $check[0]['id'];
                if($check[0]['total']>0)
                {
                    $data = $result->update_ts($tbl,$formdata," id='$eid' ");
                }
                else
                {
                     $formdata['userid']=$id;   
                     $data = $result->insert_ts($tbl,$formdata);
                }
    }
    else
    {
        die("Please put your company email id like &nbsp;<em>you</em><strong>@$dm[domain]</strong>");
    }
    
    break;
    default:
        
        $check = $result->query_ts("SELECT COUNT(id) total FROM $tbl WHERE userid='$id' ");
        if($check[0]['total']>0)
        {
            $data = $result->update_ts($tbl,$formdata," userid='$id' ");
        }
        else
        {
            $data = $result->insert_ts($tbl,$formdata);
        }
    
    break;
}
            
$result->disconnect_ts();
            
            if($data['success']=='true' && mysql_error()=="")
            {
               
				echo "<div class='alert alert-success' > $data[message] </div>";
            
			}
			else
			{
                echo "<div class='alert alert-danger' >Sorry something went wrong..Please try again</div>";
			}
		
		
		}
		else
        {
            echo "<div class='alert alert-danger' >Kindly fill your valid data</div>";
        }

            
       break;  
 
       case "recover":
       $ve = new validator;
		if(isset($_POST['email']) && $ve->validate_email($_POST['email']))
        {
            $email = addslashes($_POST['email']);
            $sql = "SELECT CONCAT(firstname,' ',lastname) name,id,pass,email  FROM `personal` WHERE `email` LIKE '$email'";
            
            $result = new TSMYSQL;
            $result->connect_ts();
            $data2 = $result->query_ts($sql);
            $result->disconnect_ts();
            
            if($data2>0)
            {
              $data = $data2[0];  
              
              /////////////////////////////////////////////////////////////////////////////////////////////
              
              $RichEmail = true;               
              
              /////////////////////////////////
              
              $ma['from'] = $GLOBALS['impMail'];
              $ma['subject'] = "Recover your {$GLOBALS[site][name]} Account\n";
              
              //
              $htmlemail = file_get_contents('default/account_recovery_ui.php');
              $htmlemail = str_replace('{name}',$data['firstname'],$htmlemail);
              $htmlemail = str_replace('{sitename}',$GLOBALS['site']['name'],$htmlemail);
              $htmlemail = str_replace('{email}',$data['email'],$htmlemail);
              $htmlemail = str_replace('{password}',$data['pass'],$htmlemail);
              $htmlemail = str_replace('{siteurl}',_BASEPATH_,$htmlemail);
              //
              
              if($RichEmail)
              {
                 $ma['msg'] = $htmlemail;                
              }
              else
              {              
              $ma['msg'] = "Dear $data[firstname],";
              $ma['msg'].= "\n\nWelcome to {$GLOBALS[site][name]}, $data[firstname]!\n\n You have requested Password from us so below is your password :\n\n $data[pass]";
              $ma['msg'].= "\n\n\n Thank You\n {$GLOBALS[site][name]} Team";
              }
               
              $mail = new communicate;
            //  $msa= $mail->mailme($ma['to'],$ma['msg'],$ma['subject'],$email); // Sending to Admin
              $msa= $mail->mailme($data['email'],$ma['msg'],$ma['subject'],$ma['from']); // Sending to User
                
              echo '<script type="text/javascript"> 
                    swal({
                      title: "Great!",
                      text: "We have sent your password to your this email <strong>'.$data['email'].'</strong><br /><br /><small>Please check your email</small>",
                      type: "success",
                      html: true
                    }); 
              
              </script>';
              
              /////////////////////////////////////////////////////////////////////////////////////////////
             }
            else
            {
                echo '<script type="text/javascript"> swal("Oops...", "Your email doesn\'t match to our records, Please SignUp!", "error"); </script>';
            }
                       
        }
        else
        {
            echo "Kindly fill valid email";
        }
        
		break; 
        
        case "register":
          
          if($_REQUEST && $url[2]=="photoupload" )
          {
               if($_POST['photo']!="")
               {
                $imgData = str_replace(' ','+',$_POST['photo']);
                $imgData =  substr($imgData,strpos($imgData,",")+1);
                $imgData = base64_decode($imgData);
                // Path where the image is going to be saved
                $filePath = 'userimg/'.$_SESSION['user']['id'].'.jpg';
                // Write $imgData into the image file
                $file = fopen($filePath, 'w');
                fwrite($file, $imgData);
                fclose($file);
                if(file_exists($filePath))
                {
                    echo "Photo Uploaded successfully!"; 
                }
                else
                {
                    echo "Unable to upload photo, kindly upload valid photo!";
                }
               }
               else
               {
                 echo "kindly upload valid photo!";
               }         
          }
          else
          {
            if(isset($_POST) && isset($url[2]))
            {
               // include('registrationform.php');
            }
            else
            {
                echo "Kindly fill your valid data";
            }
          }
            
            
                    
		break; 
        
        case "update2":
 
        if($url[2]!="")
        {
            // UPDATE or INSERT
            $tbl = $url[2];
            $userid = $_SESSION['user']['id'];
            $formdata = $_POST;
            
            $update = new TSMYSQL;
            $update->connect_ts();
            $check  = $update->query_ts("SELECT COUNT(id) total FROM $tbl WHERE userid='$userid' ");
            if($check[0]['total']>0)
            {
                // UPDATE
               $updated = $update->update_ts($tbl,$formdata," userid='$userid' ");
               
            }
            else
            {
                //INSERT
                $formdata['userid'] = $userid;
                $updated = $update->insert_ts($tbl,$formdata);        
            }
            
            if($updated['success']=='true' && mysql_error()=="")
            {
                // SUCCESS MESSAGE
                echo "Saved successfully";
            }
            else
            {
                // FAILED MESSAGES
                echo "Unable to Save, Try again!";
            }
            
        }        
        break;
        
  
        default:
        $pageurl = VIEW."$page";
        if(file_exists($pageurl))
        {
            include($pageurl);
        }
        else
        {
          include(VIEW.'404.php');
        }
        break;
}
?>