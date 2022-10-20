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


//$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
$Person_id = "{$_REQUEST["Person_id"]}";
$Person_id = check_special_char($Person_id);

	if (($Person_id != '')) {

		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Person_id  = mysqli_real_escape_string($link,$Person_id);
			//$member_pwd  = mysqli_real_escape_string($link,$member_pwd);
			//$shopping_area  = mysqli_real_escape_string($link,$shopping_area);
			//$store_type  = mysqli_real_escape_string($link,$store_type);
$Personid = trim(stripslashes($Person_id));
			
			$sql = "SELECT * FROM memberinfo where member_trash=0 ";
			if ($Personid != "") {	
				$sql = $sql." and person_id='".$Personid."'";
			}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					// login ok
					// user id 取得
					$mid=0;
					while($row = mysqli_fetch_array($result)){
						$mid = $row['mid'];
						//$pid_pic = $row['pid_pic'];
						//$base64_f1 = base64_encode($pid_pic);

						if (!is_null($row['signature_pic'])) {
							$signature_pic = $row['signature_pic'];
						}else{
							$signature_pic = "";
						}
					}

					//$data1 = file_get_contents($target_file);
					//$base64_f2 = base64_encode($image);
					if ($signature_pic != "") {
						
						//$base64_f3 = base64_encode($signature_pic);
						$base64_f3 = decrypt($key,$signature_pic);

						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="身分證號存在,有簽名圖檔!";
						$data["signaturePicture"]=$base64_f3;
					}else{
	
						$data["status"]="false";
						$data["code"]="0x0201";
						$data["responseMessage"]="身分證號存在,無簽名圖檔!";
						$data["confidence"]=$confidence;
					}

				}else{
					$data["status"]="false";
					$data["code"]="0x0205";
					$data["responseMessage"]="身分證資料錯誤!";						
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
		//echo "參數錯誤 !";
		$data["status"]="false";
		$data["code"]="0x0203";
		$data["responseMessage"]="API parameter is required!";
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));		
	}
?>