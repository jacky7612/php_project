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
$Member_type = isset($_POST['Member_type']) ? $_POST['Member_type'] : '';

$Insurance_no = check_special_char($Insurance_no);
$Sales_id = check_special_char($Sales_id);
$Person_id = check_special_char($Person_id);
$Mobile_no = check_special_char($Mobile_no);
$Member_type = check_special_char($Member_type);

	if (($Insurance_no != '') && ($Sales_id != '') && ($Person_id != '') && ($Mobile_no != '') && ($Member_type != '') ) {

		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Insurance_no  = mysqli_real_escape_string($link,$Insurance_no);
			$Sales_id  = mysqli_real_escape_string($link,$Sales_id);
			$Person_id  = mysqli_real_escape_string($link,$Person_id);
			$Mobile_no  = mysqli_real_escape_string($link,$Mobile_no);
			$Member_type  = mysqli_real_escape_string($link,$Member_type);

$Insuranceno = trim(stripslashes($Insurance_no));
$Salesid = trim(stripslashes($Sales_id));
$Personid = trim(stripslashes($Person_id));
$Mobileno = trim(stripslashes($Mobile_no));
$Membertype = trim(stripslashes($Member_type));
			
			$sql = "SELECT * FROM orderinfo where order_no='$Insuranceno' and sales_id='$Salesid' and person_id='$Personid' and mobile_no='$Mobileno' and member_type=$Membertype and order_trash=0 ";
			//if ($Insurance_no != "") {	
			//	$sql = $sql." and order_no='".$Insurance_no."'";
			//}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					$mid=0;
					try {

						//$sql2 = "update `orderinfo` set `order_status`='$Status_code' ,`updatedttime`=NOW() where order_no='$Insurance_no' and sales_id='$Sales_id' and person_id='$Person_id' and mobile_no='$Mobile_no' and member_type=$Member_type and order_trash=0";
						//mysqli_query($link,$sql2) or die(mysqli_error($link));

						//$sql2 = "INSERT INTO `orderlog` (`order_no`,`sales_id`,`person_id`,`mobile_no`,`member_type`, `order_status`, `log_date`) VALUES ('$Insurance_no','$Sales_id','$Person_id','$Mobile_no',$Member_type,'$Status_code',NOW())";
						//mysqli_query($link,$sql2) or die(mysqli_error($link));
						
						//echo "user data change ok!";
						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="https://selfiesign03.com/demo.php?tempid=TM001&tempver=20210601";
						$data["pdfurl"]="https://selfiesign03.com/demo.php?tempid=TM001&tempver=20210601";						
						//https://selfiesign03.com/demo.php?tempid=TM001&tempver=20210601
						//https://selfiesign03.com/svs_redirect.html?ulPjz/DdrEEBTpPguo7Oog==
					} catch (Exception $e) {
						$data["status"]="false";
						$data["code"]="0x0202";
						$data["responseMessage"]="Exception error!";							
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