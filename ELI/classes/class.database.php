<?php

/**
 *  ELI FRAMEWORK
 *  FRAMEWORK CREATED BY AJAY KUMAR (TECHSOUL.IN) // EAGLE LITE - ELI FRAMEWORK
 *  +91 9862542983
 *  techsoul4@gmail.com
 */
CLASS DATABASE{
    
function connect_db($dbtype='mysql',$log='N')
{

   $dbhost = $GLOBALS['config']['dbhost'];
   $dbuser = $GLOBALS['config']['dbuser'];
   $dbpass = $GLOBALS['config']['dbpass'];
   $dbname = $GLOBALS['config']['dbname'];

switch ($dbtype)
{
    case "mysql":
    try {
        $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo ($log=='Y' OR $log=='y')?"Connected successfully":"";
        return $conn; 
        }
    catch(PDOException $e)
        {
        echo "Connection failed: ";
        echo $err->getMessage() . "<br/>";
        file_put_contents('PDOErrors.txt',$err, FILE_APPEND);  // write some details to an error-log outside public_html
        die();  //  terminate connection
        }
    break;
    
    case "sqllite":
        try {

            $con = new PDO('sqlite:'._BASEPATH_.$dbhost.$DB_name,$DB_user,$DB_pass);
            $con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            //$con->exec("SET CHARACTER SET utf8");  //  return all sql requests as UTF-8  
			return $con;	
        }
        catch (PDOException $err) {  
            echo "Database Connection failed.";
            echo $err->getMessage() . "<br/>";
            file_put_contents('PDOErrors.txt',$err, FILE_APPEND);  // write some details to an error-log outside public_html  
            die();  //  terminate connection
        }	
    break;
}    

}   


function disconnect_db()
{
   $conn = self::connect_db();
   $conn = null;
}


// INSERT ROW
function insert_row($table_name, $form_data)
{
    // retrieve the keys of the array (column titles)
    $fields = array_keys($form_data);

    // build the query
    $sql = "INSERT INTO $table_name";
    $sql .= " (`" . implode("`, `", array_keys($form_data)) . "`)";
    $sql .= " VALUES ('" . implode("', '", $form_data) . "') ";
    
    $conn = self::connect_db();
    $dasq = $conn->exec($sql);
    $last_id = $conn->lastInsertId();
    if ($dasq > 0)
    {
        $returnmsg['id'] = $last_id;
        $returnmsg['success'] = true;
        $returnmsg['message'] = 'Successfully Data Added';
        
    } 
    elseif ($dasq != 1)
    {
        $returnmsg['success'] = false;
        $returnmsg['message'] = $dasq->errorInfo();
    } else
    {
        $returnmsg = '';
    }
    
    self::disconnect_db(); // DISCONNECT DB
    
    // run and return the query result resource
    return $returnmsg;
}

// UPDATE ROW
function update_row($table_name, $form_data, $where_clause = '')
{
    
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add key word
            $whereSQL = " WHERE " . $where_clause;
        } else
        {
            $whereSQL = " " . trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE " . $table_name . " SET ";

    // loop and build the column /
    $sets = array();
    foreach ($form_data as $column => $value)
    {
        $sets[] = "`" . $column . "` = '" . $value . "'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;

    // run and return the query result

    $conn = self::connect_db();
    // Prepare statement
    $dasq = $conn->prepare($sql);
    // execute the query
    $dasq->execute();
    $dasqc = $dasq->rowCount();
    // echo a message to say the UPDATE succeeded
    if ($dasqc > 0)
    {
        $returnmsg['success'] = true;
        $returnmsg['message'] = "Successfully Updated";
    } elseif ($dasqc < 1)
    {
        $returnmsg['success'] = false;
        $returnmsg['message'] =$dasq->errorInfo();
    } else
    {
        $returnmsg = '';
    }
    
    self::disconnect_db(); // DISCONNECT DB
    return $returnmsg;
}


// DELETE ROW
function delete_row($table_name, $where_clause = '')
{
    
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add keyword
            $whereSQL = " WHERE " . $where_clause;
        } else
        {
            $whereSQL = " " . trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM " . $table_name . $whereSQL;

    // run and return the query result resource
    $conn = self::connect_db();
	$dasq = $conn->exec($sql);
    if($dasq>0)
    {
        $returnmsg['success'] = true;
        $returnmsg['message'] = "Deleted Successfully";     
    }
    else
    {
        $returnmsg['success'] = false;
        $returnmsg['message'] = "Invalid Delete";
    }
  self::disconnect_db();  
  return  $returnmsg;  
}

function query($sql)
{
 $conn = self::connect_db();
 $stmt = $conn->prepare($sql); 
 $stmt->execute();
 // set the resulting array to associative
 $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
 $rows = $stmt->fetchAll();
 
 $totalrows = COUNT($result);
    if ($totalrows > 0) {
        // output data of each row    
       return $rows;        
        
    } else {
        echo "0 results";
    }
  self::disconnect_db();   
} 

function upload($file,$tofolder,$returnpath='Y')
{
    if($file['error']==0)
    {
        if(!file_exists($tofolder))
        {
            mkdir($tofolder,777,true);
        }
        $ext_a = explode('.',$file['name']);
        $ext = end($ext_a);
        $filename = time().'.'.$ext;
        $uploadedfile = $file['tmp_name'];
        $uploadto= $tofolder.'/'.$filename;
        move_uploaded_file($uploadedfile,$uploadto);
        if(file_exists($uploadto))
        {
            return $uploadto;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return " ERROR ON FILE: ".$file['error'];
    }
}
    

}
?>