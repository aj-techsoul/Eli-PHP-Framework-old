<?php

class EXPORT
{

    function excel($queryordata)
    {

        $setCounter = 0;

        $setExcelName = "students-". strtotime("now");

        if(is_array($queryordata))
        {
            $setRec = $queryordata;
        }
        else
        {
            $setSql = $queryordata;
            $setRec = mysql_query($setSql);
        }

        $setCounter = mysql_num_fields($setRec);

        for ($i = 0; $i < $setCounter; $i++) {
            $setMainHeader .= mysql_field_name($setRec, $i) . "\t";
        }

        while ($rec = mysql_fetch_row($setRec)) {
            $rowLine = '';
            foreach ($rec as $value) {
                if (!isset($value) || $value == "") {
                    $value = "t";
                } else {
                    //It escape all the special charactor, quotes from the data.
                    $value = strip_tags(str_replace('"', '""', $value));
                    $value = '"' . $value . '"' . "\t";
                }
                $rowLine .= $value;
            }
            $setData .= trim($rowLine) . "\n";
        }
        $setData = str_replace("r", "", $setData);

        if ($setData == "") {
            $setData = "no matching records foundn";
        }

        $setCounter = mysql_num_fields($setRec);


        //This Header is used to make data download instead of display the data
        header("Content-type: application/octet-stream");

        header("Content-Disposition: attachment; filename=" . $setExcelName ."_Report.xls");

        header("Pragma: no-cache");
        header("Expires: 0");

        //It will print all the Table row as Excel file row with selected column name as header.
        echo ucwords($setMainHeader) . "\n" . $setData . "\n";


    }
    
    function largeexport($queryordata)
    {

    //    echo "Processing...";
        
        $setCounter = 0;

        $setExcelName = "LD-ALL-".date('d-m-Y');

        if(is_array($queryordata))
        {
            $setRec = $queryordata;
        }
        else
        {
            $setSql = $queryordata;
            $setRec = mysql_query($setSql);
        }

        /////////////////////////////////////////////////////////////////
        // CSV EXPORT USING PHP OUTPUT
        $filename = $setExcelName.'-export.csv';

        //output the headers for the CSV file
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Expires: 0");
        header("Pragma: public");
        
        //open the file stream
        $fh = @fopen('php://output', 'w');
        
        $headerDisplayed = true;
        
        // ASSIGN MAX SMALL LIMIT
        $smax = 1000;
        // GET NUMBER OF ROWS
        $total_records = mysql_num_rows($setRec);
        // echo "<br />Total Records: $total_records <br />";
        
        if($total_records>$smax)
        {
            // echo "Its more than $smax < $total_records";
            
            
        } 
        else
        {
            // echo "Its small Data $total_records";
        }
        
        $arraydata = mysql_fetch_row($setRec);
        $i=0;
        foreach ($arraydata as $data) {
          $i++;  
            // Add a header row if it hasn't been added yet -- using custom field keys from first array
            if ( !$headerDisplayed ) {
                fputcsv($fh, array_keys($ccsve_generate_value_arr));
                $headerDisplayed = true;
            }
        
            // Put the data from the new multi-dimensional array into the stream
            fputcsv($fh, $data);
        }
        echo $fh;
        // Close the file stream
        fclose($fh);
        /////////////////////////////////////////////////////////////////

    }
    
}

?>