<?php
	include "comm.php";
	include "../db_tools.php";	
	$token = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';	
	$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
	$Apply_no = isset($_POST['apply_no']) ? $_POST['apply_no'] : '';
	$App_type = isset($_POST['App_type']) ? $_POST['App_type'] : '';
	if($App_type == '0')
		$appId = "Q3RRdLWTwYo8fVtP"; //此 API 為業務呼叫
	if($App_type == '1')
		$appId = "HKgWyfYQv30ZE6AM"; //此 API 為客戶呼叫
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
				{
					//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
					curl_setopt($curl, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Authorization: Bearer ' . $header
						));				
				}
				else
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	//echo $url;			
				break;
			case "GET":
				
				//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				if($header != null)
					curl_setopt($curl, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Authorization: Bearer ' . $header
						));			
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

	if (($Apply_no != '')) {
		
		//check 帳號/密碼
		//$host = 'localhost';
		//$host = '10.67.70.153';	
		//$user = 'tglmember_user';
		//$passwd = 'tglmember210718';
		//$database = 'tglmemberdb';
		try {
		$link = mysqli_connect($host, $user, $passwd, $database);
		mysqli_query($link,"SET NAMES 'utf8'");
			
		$Apply_no  = mysqli_real_escape_string($link,$Apply_no);
		$app_type  = mysqli_real_escape_string($link,$app_type);	
		$token  = mysqli_real_escape_string($link,$token);
		$Apply_no2 = trim(stripslashes($Apply_no));
		$app_type2 = trim(stripslashes($app_type));
		$token2 = trim(stripslashes($token));

		//echo $sql;
		//exit;


			///$sql = "SELECT * FROM memberinfo where member_trash=0 ";
			//if ($Person_id != "") {	
				//$sql = $sql." and person_id='".$Person_id."'";
			//}

			if (1){//$result = mysqli_query($link, $sql)){
					if($token2 != '')
					{
						//LDI-008
						$url = $g_mpost_url. "ldi/proposal/send-sign";
						$data = array();
						$data['applyNo']= $Apply_no2;
						$data['appId']= $appId; //此 API 為業務呼叫
						
						$jsondata = json_encode($data);
						$out = CallAPI("POST", $url, $jsondata, $token2);
						//
						$ret = json_decode($out, true);
						echo $out;
						exit;
						//Insert status to DB
						if($ret['success']== true)
						{
							
							//$ret['code']
							//$ret['data']
						}
					}
					else
					{
						$data["status"]="false";
						$data["code"]="0x0204";
						$data["responseMessage"]="token fail";							
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