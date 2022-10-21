<?php
//include("header_check.php");
include("db_tools.php");

//$Insurance_no = isset($_POST['Insurance_no']) ? $_POST['Insurance_no'] : '';
//$Sales_id = isset($_POST['Sales_id']) ? $_POST['Sales_id'] : '';
//$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
$Mobile_no = isset($_POST['Mobile_no']) ? $_POST['Mobile_no'] : '';
$SMS_data = isset($_POST['SMS_message']) ? $_POST['SMS_message'] : '';

//$Member_type = isset($_POST['Member_type']) ? $_POST['Member_type'] : '1';

	//if (($Insurance_no != '') && ($Sales_id != '') && ($Person_id != '') && ($Mobile_no != '') ) {
	if (($SMS_data != '') && ($Mobile_no != '') ) {

		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			//$Insurance_no  = mysqli_real_escape_string($link,$Insurance_no);
			//$Sales_id  = mysqli_real_escape_string($link,$Sales_id);
			//$Person_id  = mysqli_real_escape_string($link,$Person_id);
			$Mobile_no  = mysqli_real_escape_string($link,$Mobile_no);
			$SMS_data  = mysqli_real_escape_string($link,$SMS_data);

$Mobileno = trim(stripslashes($Mobile_no));		
$SMS_data = trim(stripslashes($SMS_data));	
			$sql = "SELECT * FROM orderinfo where mobile_no='$Mobile_no' and order_trash=0";
			//if ($Insurance_no != "") {	
			//	$sql = $sql." and order_no='".$Insurance_no."'";
			//}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					$mid=0;
					try {

						//$smsdata = "TGL遠距行動投保APP[一次性驗證碼簡訊],你的驗證碼為:".$user_code;   //

						$uriBase2 = 'http://211.20.185.2/tours/api/sendsms.php';
						//$cmd2 = "curl -X POST 'https://face8.pakka.ai/api/v2/faceCompare' -H 'accept: application/json' -H 'Content-Type: multipart/form-data' -F 'api_key=".$key."' -F 'image_file1=@".$target_file1.";type=image/jpeg' -F 'image_file2=@".$target_file.";type=image/jpeg' -F 'face_token1=".$facetoken1."' -F 'face_token2=".$facetoken2."'";
						//echo $cmd2;
						$fields2 = [
							'phone_no'         => $Mobileno,
							'sms_data'         => $SMS_data
						];
						$fields_string2 = http_build_query($fields2);	
						$ch2 = curl_init();
						curl_setopt($ch2,CURLOPT_URL, $uriBase2);
						curl_setopt($ch2,CURLOPT_POST, true);
						curl_setopt($ch2,CURLOPT_POSTFIELDS, $fields_string2);
						curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true); 
						//execute post
						$result2 = curl_exec($ch2);		
		
						
						//$sql2 = "update `orderinfo` set `verification_code`='$user_code' ,`updatedttime`=NOW() where order_no='$Insurance_no' and sales_id='$Sales_id' and person_id='$Person_id' and mobile_no='$Mobile_no' and member_type=$Member_type and order_trash=0";
						//mysqli_query($link,$sql2) or die(mysqli_error($link));

						//$sql2 = "INSERT INTO `orderlog` (`order_no`,`sales_id`,`person_id`,`mobile_no`,`member_type`, `order_status`, `log_date`) VALUES ('$Insurance_no','$Sales_id','$Person_id','$Mobile_no',$Member_type,'$Status_code',NOW())";
						//mysqli_query($link,$sql2) or die(mysqli_error($link));
						
						//echo "user data change ok!";
						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="簡訊發送完成!";		
						
					} catch (Exception $e) {
						$data["status"]="false";
						$data["code"]="0x0201";
						$data["responseMessage"]="簡訊發送未完成!";							
					}
				}else{
					$data["status"]="false";
					$data["code"]="0x0205";
					$data["responseMessage"]="手機號碼未在資料庫!";						
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