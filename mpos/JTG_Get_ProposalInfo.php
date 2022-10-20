<?php
	include "comm.php";
	include "../db_tools.php";	
	$App_type = isset($_POST['App_type']) ? $_POST['App_type'] : '';
	$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
//	$idcard = isset($_POST['idcard']) ? $_POST['idcard'] : '';
	$Apply_no = isset($_POST['apply_no']) ? $_POST['apply_no'] : '';
	$agentCode = isset($_POST['agentCode']) ? $_POST['agentCode'] : '';
	$token = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';
	
	//$Sso_token = "Vfa4BO83/86F9/KEiKsQ0EHbpiIUruFn0/kiwNguXXGY4zea11svxYSjoYP4iURR";
	//$App_type = "0";//業務員
	if($App_type == '0')
		$appId = "Q3RRdLWTwYo8fVtP"; //此 API 為業務呼叫
	if($App_type == '1')
		$appId = "HKgWyfYQv30ZE6AM"; //此 API 為客戶呼叫
	//$Apply_no = "7300000022SN001";
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
				
				curl_setopt($curl, CURLOPT_HEADER,0);
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

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

		$result = curl_exec($curl);
	//echo $result;
		curl_close($curl);

		return $result;
	}

	if (($Person_id != '' && $Apply_no != '')) {
		
		//check 帳號/密碼
	
		//$host = 'localhost';
		//$host = '10.67.70.153';		
		//$user = 'tglmember_user';
		//$passwd = 'tglmember210718';
		//$database = 'tglmemberdb';

		try {
		
		$link = mysqli_connect($host, $user, $passwd, $database);
		mysqli_query($link,"SET NAMES 'utf8'");
			
		$Person_id  = mysqli_real_escape_string($link,$Person_id);
		$App_type  = mysqli_real_escape_string($link,$App_type);	
		$Apply_no  = mysqli_real_escape_string($link,$Apply_no);
		$agentCode  = mysqli_real_escape_string($link,$agentCode);
		$token  = mysqli_real_escape_string($link,$token);
		$Person_id2 = trim(stripslashes($Person_id)); 
		$App_type2 = trim(stripslashes($App_type));
		$Apply_no2 = trim(stripslashes($Apply_no));
		$agentCode2 = trim(stripslashes($agentCode));
		$token2 = trim(stripslashes($token));


			//$sql = "SELECT * FROM memberinfo where member_trash=0 ";
			//if ($Person_id != "") {	
				//$sql = $sql." and person_id='".$Person_id."'";
			//}

			//if ($result = mysqli_query($link, $sql)){
			if(1){
				
					$data = array();
					if($token2 != '')
					{
						//exit;
						//LDI-003
						$url = $g_mpost_url. "ldi/proposal/info";
						
						$data['appId']= $appId ;					
						$data['idcard']= $Person_id2 ;
						$data['applyNo']= $Apply_no2 ;
						$data['agentCode']= $agentCode2 ;
						$jsondata = json_encode($data);
						//echo $jsondata;
						$out = CallAPI("POST", $url, $jsondata, $token2);
						//echo "PDF:".$out;
						echo $out;
						exit;
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