<?php
//include("header_check.php");
include("db_tools.php");
include("resize-class.php");
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

$Person_id = isset($_POST["Person_id"])?$_POST["Person_id"]:'';
$front = isset($_POST["front"])?$_POST["front"]:'';//0: front, 1: back
$picId = isset($_POST["Pid_PicID"])?$_POST["Pid_PicID"]:'';

$Person_id = check_special_char($Person_id);
$front = check_special_char($front);


	if (($Person_id != '' && strlen($Person_id)>1) ) {
		$image = addslashes(encrypt($key, $picId)); //SQL Injection defence!
			//$image = ($picId); //SQL Injection defence!
		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Person_id  = mysqli_real_escape_string($link,$Person_id);
			$front  = mysqli_real_escape_string($link,$front);
			//$Mobile_no  = mysqli_real_escape_string($link,$Mobile_no);
			//$Member_name  = mysqli_real_escape_string($link,$Member_name);
$Personid = trim(stripslashes($Person_id));
			
			$sql = "SELECT * FROM memberinfo where member_trash=0 ";
			if ($Personid != "") {	
				$sql = $sql." and person_id='".$Personid."'";
			}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					$mid=0;
					while($row = mysqli_fetch_array($result)){
						$mid = $row['mid'];
						//$membername = $row['member_name'];
					}	
					$mid = (int)str_replace(",", "", $mid);					
					try {
						$sql = "SELECT * from `idphoto` where person_id = '".$Personid."'";
						$ret = mysqli_query($link, $sql);
						if (mysqli_num_rows($ret) > 0)
						{
							if($front=="0")
								$sql2 = "UPDATE  `idphoto` set `front` = '{$image}', `updatedtime` = NOW() where `person_id`='".$Personid."'";
							else
								$sql2 = "UPDATE  `idphoto` set `back` = '{$image}', `updatedtime` = NOW() where `person_id`='".$Personid."'";
								
						}
						else
						{
							if($front=="0")
								$sql2 = "INSERT INTO  `idphoto` ( `person_id`, `front` , `updatedtime`) VALUES ('$Personid', '{$image}', NOW()) ";
							else
								$sql2 = "INSERT INTO  `idphoto` ( `person_id`, `back` , `updatedtime`)  VALUES ('$Personid', '{$image}', NOW()) ";
						}

						mysqli_query($link,$sql2) or die(mysqli_error($link));
						
						//echo "user data change ok!";
						$data["status"]="true";
						$data["code"]="0x0200";
						$data["responseMessage"]="身分證上傳成功!";		
						
					} catch (Exception $e) {
						$data["status"]="false";
						$data["code"]="0x0202";
						$data["responseMessage"]="Exception error!";							
					}
				}else{
					$data["status"]="false";
					$data["code"]="0x0201";
					$data["responseMessage"]="無相同身份證資料,無法更新!".$Person_id;						
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