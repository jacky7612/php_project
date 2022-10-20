<?php
	include "comm.php";
	include "../db_tools.php";	
	$token = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';		
	$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
	$Apply_no = isset($_POST['Apply_no']) ? $_POST['Apply_no'] : '';
	$App_type = isset($_POST['App_type']) ? $_POST['App_type'] : '';
	//$Apply_no = "7300000095SN001";
//	$Sso_token = "Vfa4BO83/86F9/KEiKsQ0EHbpiIUruFn0/kiwNguXXGY4zea11svxYSjoYP4iURR";
	//$Sso_token = "u0K2w1L0roUR8p1k3UJgZtlRbR6DD9BZHyXkDNvCALSY4zea11svxYSjoYP4iURR";
	////$Person_id="F268362825";
	//$App_type="0";
	//$token = "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJGMjY4MzYyODI1IiwicGFyYW1zIjoie1wiYWdlbnROYW1lXCI6XCJY6IKy6JCNXCIsXCJhZ2VudE1vYmlsZVwiOlwiMDk4NzY1NDMyMVwiLFwiYWdlbnRJZENhcmRcIjpcIkYyNjgzNjI4MjVcIixcInNlY051bVwiOlwiMDEwMjMwMjc5NFwiLFwiYXBwbHlOb1wiOlwiNzMwMDAwMDA5NVNOMDAxXCIsXCJjdXN0b21lck1vYmlsZVwiOm51bGwsXCJjdXN0b21lcklkQ2FyZFwiOm51bGx9IiwiZXhwIjoxNjI3ODE2Nzc1LCJpYXQiOjE2Mjc4MDU5NzV9.uhYx_st0wHfpcooHLyeOTpZeMbUAvHnKx7RKKVKppFFjnQ3S-oMqk9cf_DMoiXmCgiIgKAW2UpEYRVY6alxJIg";
	//$App_type = "0";//業務員
	//$Apply_no="7300000022SN001";
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

	if (($Person_id != '') && ($Apply_no != '')) {
		
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
		$App_type  = mysqli_real_escape_string($link,$App_type);	
		$token  = mysqli_real_escape_string($link,$token);
		$Apply_no2 = trim(stripslashes($Apply_no));
		$App_type2 = trim(stripslashes($App_type));
		$token2 = trim(stripslashes($token));
			//$sql = "SELECT * FROM memberinfo where member_trash=0 ";
			//if ($Person_id != "") {	
				////$sql = $sql." and person_id='".$Person_id."'";
			//}

			if (1){//$result = mysqli_query($link, $sql)){
					$data = array();
					if($token2 != '')
					{
						//exit;
						//LDI-005
						$url = $g_mpost_url. "ldi/proposal/pdf";
						
						
						$data['applyNo']= $Apply_no2;
						$data['appId']= $appId ;
					
						$jsondata = json_encode($data);
						$out = CallAPI("POST", $url, $jsondata, $token2);
						//$id1 = uniqid();
						$filename1 = "/var/www/html/member/api/mpos/tmp/".$Apply_no2.".pdf";
						$file1 = fopen($filename1,"w");
						fwrite($file1,$out);
						fclose($file1);
						
						//echo "PDF:".$out;
						//$data = array();
						$data["status"]="true";
						$data["code"]="0x0200";						
						$data["pdfurl"]="https://disuat.transglobe.com.tw:1443/member/api/mpos/tmp/".$Apply_no2.".pdf";
						$data["pdf"]=$Apply_no2.".pdf";
						//copy file to another one
						$localIP = "10.67.70.151";
						
						if($localIP == $g_app1_ip1)
								$copyIP = $g_app1_ip2;
							else if($localIP == $g_app1_ip2)
								$copyIP = $g_app1_ip1;
							else 
								$copyIP = '';							
						
						//echo $copyIP;
						if($copyIP!='')
						{
							
								$uriBase = "http://".$copyIP."/member/api/mpos/copypdf.php";//測試機
							 $fields = [
								'pdf'         => $out,
								'Apply_no'	  => $Apply_no2
								];
							
							$fields_string = http_build_query($fields);	
							$ch = curl_init();
							curl_setopt($ch,CURLOPT_URL, $uriBase);
							curl_setopt($ch,CURLOPT_POST, true);
							curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
							curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
							//execute post
							$result = curl_exec($ch);	
						}							
						
						
					//echo "PDF:".$out;
						//$data = array();
						//$data["status"]="true";
						//$data["code"]="0x0200";						
						//$data["pdf"]=$out;
						header('Content-Type: application/json');
						echo (json_encode($data, JSON_UNESCAPED_UNICODE));
						//echo $out;
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