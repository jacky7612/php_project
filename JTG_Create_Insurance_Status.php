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

$Insurance_no = isset($_POST['Insurance_no']) ? $_POST['Insurance_no'] : '';
$Sales_id = isset($_POST['Sales_id']) ? $_POST['Sales_id'] : '';
$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
$Mobile_no = isset($_POST['Mobile_no']) ? $_POST['Mobile_no'] : '';
$Member_type = isset($_POST['Member_type']) ? $_POST['Member_type'] : '1';
$Status_code = isset($_POST['Status_code']) ? $_POST['Status_code'] : '';

$Insurance_no = check_special_char($Insurance_no);
$Sales_id = check_special_char($Sales_id);
$Person_id = check_special_char($Person_id);
$Mobile_no = check_special_char($Mobile_no);
$Member_type = check_special_char($Member_type);
$Status_code = check_special_char($Status_code);

	if (($Insurance_no != '') && ($Sales_id != '') && ($Person_id != '') && ($Mobile_no != '') ) {

		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Insurance_no  = mysqli_real_escape_string($link,$Insurance_no);
			$Sales_id  = mysqli_real_escape_string($link,$Sales_id);
			$Person_id  = mysqli_real_escape_string($link,$Person_id);
			$Mobile_no  = mysqli_real_escape_string($link,$Mobile_no);
			$Member_type  = mysqli_real_escape_string($link,$Member_type);
			$Status_code  = mysqli_real_escape_string($link,$Status_code);

$Insuranceno = trim(stripslashes($Insurance_no));
$Salesid = trim(stripslashes($Sales_id));
$Personid = trim(stripslashes($Person_id));
$Mobileno = trim(stripslashes($Mobile_no));
$Membertype = trim(stripslashes($Member_type));
$Statuscode = trim(stripslashes($Status_code));

		//$Personid = encrypt($key,($Personid));
		$Mobileno = addslashes(encrypt($key,($Mobileno)));
		
			
			$sql = "SELECT * FROM orderinfo where order_trash=0 ";
			if ($Insurance_no != "") {	
				$sql = $sql." and order_no='".$Insuranceno."'";
			}
			if ($Sales_id != "") {	
				$sql = $sql." and sales_id='".$Salesid."'";
			}
			if ($Person_id != "") {	
				$sql = $sql." and person_id='".$Personid."'";
			}
			if ($Mobile_no != "") {	
				$sql = $sql." and mobile_no='".$Mobileno."'";
			}
			if ($Member_type != "") {	
				$sql = $sql." and member_type='".$Membertype."'";
			}
			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) == 0){
					$mid=0;
					try {

						$sql2 = "INSERT INTO `orderinfo` (`order_no`,`sales_id`,`person_id`,`mobile_no`,`member_type`, `order_status`, `order_trash`, `inputdttime`) VALUES ('$Insuranceno','$Salesid','$Personid','$Mobileno',$Membertype,'$Statuscode', 0,NOW())";
						mysqli_query($link,$sql2) or die(mysqli_error($link));

						$sql2 = "INSERT INTO `orderlog` (`order_no`,`sales_id`,`person_id`,`mobile_no`,`member_type`, `order_status`, `log_date`) VALUES ('$Insuranceno','$Salesid','$Personid','$Mobileno',$Membertype,'$Statuscode',NOW())";
						mysqli_query($link,$sql2) or die(mysqli_error($link));
						
						//echo "user data change ok!";
						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="新增資料完成!";		
						
					} catch (Exception $e) {
						$data["status"]="false";
						$data["code"]="0x0202";
						$data["responseMessage"]="Exception error!";							
					}
				}else{
					$data["status"]="false";
					$data["code"]="0x0201";
					$data["responseMessage"]="已經有相同要保流水序號的資料!";						
				}
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
		//echo "need mail and password!";
		$data["status"]="false";
		$data["code"]="0x0203";
		$data["responseMessage"]="API parameter is required!";
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));			
	}
?>