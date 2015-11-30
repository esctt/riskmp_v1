<?php
$output_dir = "/var/www/riskmp.com/public_html/assets/images/uploads/";
if(isset($_FILES["file"]))
{
	$ret = array();

	$error =$_FILES["file"]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData() 
	if(!is_array($_FILES["file"]["name"])) //single file
	{
 	 	$fileName = $_FILES["file"]["name"];
 	 	// $fileName = number_format(microtime(true)*1000,0,'.','') . '_' . $_FILES["file"]["name"];
 		move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir.$fileName);
    	die($fileName);
	}
	else  //Multiple files, file[]
	{
	  $fileCount = count($_FILES["file"]["name"]);
	  for($i=0; $i < $fileCount; $i++)
	  {
	  	$fileName = $_FILES["file"]["name"][$i];
		move_uploaded_file($_FILES["file"]["tmp_name"][$i],$output_dir.$fileName);
	  	$ret[$i]['file']= $fileName;
	  }
	
	}
    echo json_encode($ret);
 }
 ?>