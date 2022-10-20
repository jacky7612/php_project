<?php
//include("header_check.php");
include("db_tools.php"); 
//include("resize-class.php");
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

	$Sales_id = isset($_POST['Sales_id']) ? $_POST['Sales_id'] : '';
	$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
	$Mobile_no = isset($_POST['Mobile_no']) ? $_POST['Mobile_no'] : '';
	$Sales_name = isset($_POST['Sales_name']) ? $_POST['Sales_name'] : '';
	$FCM_Token = isset($_POST['FCM_Token']) ? $_POST['FCM_Token'] : '';

	$Sales_id = check_special_char($Sales_id);
	$Person_id = check_special_char($Person_id);
	$Mobile_no = check_special_char($Mobile_no);
	$Sales_name = check_special_char($Sales_name);
	$FCM_Token = check_special_char($FCM_Token);

	if (($Sales_id != '') && ($Person_id != '') && ($Mobile_no != '') && ($Sales_name != '') ) {


		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Sales_id  = mysqli_real_escape_string($link,$Sales_id);
			$Person_id  = mysqli_real_escape_string($link,$Person_id);
			$Mobile_no  = mysqli_real_escape_string($link,$Mobile_no);
			$Sales_name  = mysqli_real_escape_string($link,$Sales_name);
			$FCM_Token  = mysqli_real_escape_string($link,$FCM_Token);

		$Salesid = trim(stripslashes($Sales_id));
		$Personid = trim(stripslashes($Person_id));
		$Mobileno = trim(stripslashes($Mobile_no));
		$Salesname = trim(stripslashes($Sales_name));
		$FCMToken = trim(stripslashes($FCM_Token));

		//$Personid = encrypt($key,($Personid));
		$Mobileno = addslashes(encrypt($key,($Mobileno)));
		$Salesname = addslashes(encrypt($key,($Salesname)));
			
			$sql = "SELECT * FROM salesinfo where sales_trash=0 ";
			if ($Sales_id != "") {	
				$sql = $sql." and Sales_id='".$Salesid."'";
			}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) == 0){
					//$mid=0;
					try {

						$sql2 = "INSERT INTO `salesinfo` (`person_id`,`mobile_no`,`sales_id`, `sales_name`, `notificationToken`,`sales_trash`, `inputdttime`) VALUES ('$Personid','$Mobileno','$Salesid','$Salesname','$FCMToken',0,NOW())";
						mysqli_query($link,$sql2) or die(mysqli_error($link));
						
						//echo "user data change ok!";
						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="業務資料建檔成功!";		
						
					} catch (Exception $e) {
						$data["status"]="false";
						$data["code"]="0x0202";
						$data["responseMessage"]="Exception error!";							
					}
				}else{
					try {

						$sql2 = "update `salesinfo` set `person_id`='$Personid',`mobile_no`='$Mobileno', `sales_name`='$Salesname'";
						if ($FCMToken != "") {
							$sql2 = $sql2.", `notificationToken`='$FCMToken'";
						}
						$sql2 = $sql2.", `updatedttime`=NOW() where `sales_id`='$Salesid' and  sales_trash=0 ";
						mysqli_query($link,$sql2) or die(mysqli_error($link));

						$data["status"]="false";
						$data["code"]="0x0201";
						$data["responseMessage"]="已經有相同業務資料,業務資料更新成功!";	
						
					} catch (Exception $e) {
						$data["status"]="false";
						$data["code"]="0x0202";
						$data["responseMessage"]="Exception error!";							
					}						
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