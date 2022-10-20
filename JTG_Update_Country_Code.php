<?php
//include("header_check.php");
include("db_tools.php");
include("security_tools.php");
$headers =  apache_request_headers();
$token = $headers['Authorization'];
if(check_header($key, $token)==true)
{
	;//echo "valid token";
	
}
else
{
	;//echo "error token";
	$data = array();
	$data["status"]="false";
	$data["code"]="0x0209";
	$data["responseMessage"]="Invalid token!";	
	header('Content-Type: application/json');
	echo (json_encode($data, JSON_UNESCAPED_UNICODE));		
	exit;							
}

$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
$Insurance_id = isset($_POST['Insurance_id']) ? $_POST['Insurance_id'] : '';
$Country_code = isset($_POST['Country_code']) ? $_POST['Country_code'] : '';

$Person_id = check_special_char($Person_id);
$Insurance_id = check_special_char($Insurance_id);
$Country_code = check_special_char($Country_code);

	if (($Person_id != '')  && ($Insurance_id != '')  && ($Country_code != '')) {

		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Person_id  = mysqli_real_escape_string($link,$Person_id);
			$Person_id = trim(stripslashes($Person_id));
			$Insurance_id  = mysqli_real_escape_string($link,$Insurance_id);
			$Insurance_id = trim(stripslashes($Insurance_id));
			$Country_code  = mysqli_real_escape_string($link,$Country_code);
			$Country_code = trim(stripslashes($Country_code));

			$sql = "SELECT * from countrylog where Person_id='$Person_id' and Insurance_id= '$Insurance_id' ";
			$result = mysqli_query($link, $sql);
			if (mysqli_num_rows($result) > 0){
				
				$sql = "UPDATE countrylog SET countrycode='$Country_code' where Person_id='$Person_id' and Insurance_id= '$Insurance_id' ";
			}
			else
			{
				$sql = "Insert into countrylog (Person_id, Insurance_id, countrycode, updatetime ) VALUES ('$Person_id', '$Insurance_id', '$Country_code', NOW() )  ";
			}

			if ($result = mysqli_query($link, $sql)){
					$data["status"]="true";
					$data["code"]="0x0200";
					$data["responseMessage"]="更新成功!";
			}else {
				$data["status"]="false";
				$data["code"]="0x0204";
				$data["responseMessage"]="SQL fail!";					
			}
			mysqli_close($link);
		} catch (Exception $e) {
			$data["status"]="false";
			$data["code"]="0x0202";
			$data["responseMessage"]="Exception error!";				
        }
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));		
	}else{
		//echo "參數錯誤 !";
		$data["status"]="false";
		$data["code"]="0x0203";
		$data["responseMessage"]="API parameter is required!";
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));		
	}
?>