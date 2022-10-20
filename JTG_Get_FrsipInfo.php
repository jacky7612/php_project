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

$Role = isset($_POST['Role']) ? $_POST['Role'] : '1';//0:業務員  1:要保人 2:被保人 3: 法定代理人
$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';

$Role = check_special_char($Role);
$Person_id = check_special_char($Person_id);

/*
proposer：要保人
insured：被保人  
legalRepresentative：法定代理人
agentOne:業務
*/


function CallAPI($method, $url, $data = false, $header = null)
{
		$url = trim(stripslashes($url));
		$method2 = trim(stripslashes($method));

    $curl = curl_init();

    switch ($method2)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			
			//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			if($header != null)
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//echo $url;			
            break;
        case "GET":
			
			//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			if($header != null)
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);			
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
//echo $result;
    curl_close($curl);

    return $result;
}

	if (($Role != '') && ($Person_id != '')) {

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

			$Role  = mysqli_real_escape_string($link,$Role);
			$Person_id  = mysqli_real_escape_string($link,$Person_id);

$Role = trim(stripslashes($Role));
$Personid = trim(stripslashes($Person_id));

			if($Role != "0")
			{
				$data["status"]="false";
				$data["code"]="0x0204";
				$data["responseMessage"]="無此權限";
				header('Content-Type: application/json');
				echo (json_encode($data, JSON_UNESCAPED_UNICODE));					
				exit;
			}
			
			$sql = "SELECT * FROM salesinfo where sales_trash=0 ";
			if ($Person_id != "") {	
				$sql = $sql." and person_id='".$Personid."'";
			}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					$frsipstatus = 0;							
					$sql = "select * from vmrule where 1";
					$result = mysqli_query($link, $sql);
					while($row = mysqli_fetch_array($result)){
						$frsipstatus = $row['frsipstatus'];
					}

				}
				else
				{
					$data["status"]="false";
					$data["code"]="0x0205";
					$data["responseMessage"]="無此業務員!";
					header('Content-Type: application/json');
					echo (json_encode($data, JSON_UNESCAPED_UNICODE));	
					exit;
				}
			}
			else
			{
				//sql failed
					
					$data["status"]="false";
					$data["code"]="0x0202";
					$data["responseMessage"]="SQL Failed!";
					header('Content-Type: application/json');
					echo (json_encode($data, JSON_UNESCAPED_UNICODE));	
					exit;
			}	
			$data["status"]="true";
			$data["code"]="0x0200";
			$data["frsipstatus"]=$frsipstatus;
			header('Content-Type: application/json');
			echo (json_encode($data, JSON_UNESCAPED_UNICODE));			
		}
		catch (Exception $e) {
			$data["status"]="false";
			$data["code"]="0x0204";
			$data["responseMessage"]="Exception error!";				
        }
	}
	else
	{
		$data["status"]="false";
		$data["code"]="0x0203";
		$data["responseMessage"]="API parameter is required!";
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));	
		exit;
	}
?>