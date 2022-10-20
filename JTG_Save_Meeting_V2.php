<?php
include "db_tools.php";
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

$Insurance_no = isset($_POST['Insurance_no']) ? $_POST['Insurance_no'] : '';
$Role = isset($_POST['Role']) ? $_POST['Role'] : '';
$bSaved = isset($_POST['bSaved']) ? $_POST['bSaved'] : '';
$Meeting_id = isset($_POST['Meeting_id']) ? $_POST['Meeting_id'] : '';

$Insurance_no = check_special_char($Insurance_no);
$Role = check_special_char($Role);
$bSaved = check_special_char($bSaved);
$Meeting_id = check_special_char($Meeting_id);

	if (($Insurance_no != '')  && ($Role != '')&& ($bSaved != '') ) {

		//check 帳號/密碼
		//$host = 'localhost';
		//$user = 'tglmember_user';
		//$passwd = 'tglmember210718';
		//$database = 'tglmemberdb';
		
		//echo $sql;
		//exit;
		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Insurance_no  = mysqli_real_escape_string($link,$Insurance_no);
			$Role  = mysqli_real_escape_string($link,$Role);
			$bSaved  = mysqli_real_escape_string($link,$bSaved);
			$Meeting_id  = mysqli_real_escape_string($link,$Meeting_id);
			
			$Insurance_no = trim(stripslashes($Insurance_no));
			$Role = trim(stripslashes($Role));
			$bSaved = trim(stripslashes($bSaved));
			$Meeting_id = trim(stripslashes($Meeting_id));
			
			$sql = "SELECT * FROM orderinfo where  order_trash=0 ";
			if ($Insurance_no != "") {	
				$sql = $sql." and order_no='".$Insurance_no."' LIMIT 1";
			}
			$data=array();
			
			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					//$mid=0;
					$order_status="";
					try {
						if($Role == "0")//業務離開
						{
							//save file or not?
							
							if($bSaved == "0")//因為資料庫預設是1
							{
								$sql = "update meetinglog SET bSaved = 0 where insurance_no='".$Insurance_no."' and meetingid='".$Meeting_id."' ";
								$ret = mysqli_query($link, $sql)or die(mysqli_error($link));
							}
							else if($bSaved == "1")
							{
								$sql = "update meetinglog SET bSaved = 1 where insurance_no='".$Insurance_no."' and meetingid='".$Meeting_id."' ";
								$ret = mysqli_query($link, $sql)or die(mysqli_error($link));									
							}
							else
							{
								$data["status"]="false";
								$data["code"]="0x0205";
								$data["responseMessage"]=="bSave is wrong value";	
								header('Content-Type: application/json');
								echo (json_encode($data, JSON_UNESCAPED_UNICODE));
								exit;
								
							}
					
						}
			
						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="OK";	

						
					} catch (Exception $e) {
						//$this->_response(null, 401, $e->getMessage());
						//echo $e->getMessage();
						$data["status"]="false";
						$data["code"]="0x0202";
						$data["responseMessage"]="系統異常";							
					}
				}else{
					$data["status"]="false";
					$data["code"]="0x0201";
					$data["responseMessage"]="不存在此要保流水序號的資料!";						
				}
			}else {
				$data["status"]="false";
				$data["code"]="0x0204";
				$data["responseMessage"]="SQL fail!";					
			}
			mysqli_close($link);
		} catch (Exception $e) {
            //$this->_response(null, 401, $e->getMessage());
			//echo $e->getMessage();
			$data["status"]="false";
			$data["code"]="0x0202";
			$data["responseMessage"]="系統異常";					
        }
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));
	}else{
		//echo "need mail and password!";
		$data["status"]="false";
		$data["code"]="0x0203";
		$data["responseMessage"]="API parameter is required!";
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));			
	}
		
?>