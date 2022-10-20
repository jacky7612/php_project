<?php
	include "comm.php";
	include "../db_tools.php";	
	//$token = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';		
	$Person_id = isset($_POST['Person_id']) ? $_POST['Person_id'] : '';
	$pdfname = isset($_POST['pdfname']) ? $_POST['pdfname'] : '';
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

	if (($Person_id != '') && ($pdfname != '')) {
		
		//check 帳號/密碼
	
		//$host = 'localhost';
		//$host = '10.67.70.153';	
		//$user = 'tglmember_user';
		//$passwd = 'tglmember210718';
		//$database = 'tglmemberdb';

		try {
		
		$link = mysqli_connect($host, $user, $passwd, $database);
		mysqli_query($link,"SET NAMES 'utf8'");
			
		$pdfname  = mysqli_real_escape_string($link,$pdfname);
		$App_type  = mysqli_real_escape_string($link,$App_type);	

		$pdfname2 = trim(stripslashes($pdfname));
		$App_type2 = trim(stripslashes($App_type));
							
			//$sql = "SELECT * FROM memberinfo where member_trash=0 ";
			//if ($Person_id != "") {	
				////$sql = $sql." and person_id='".$Person_id."'";
			//}

			if (1){//$result = mysqli_query($link, $sql)){
					$data = array();
					if(1)
					{
						unlink("/var/www/html/member/api/mpos/tmp/".$pdfname2);
						
						$out = shell_exec("ifconfig");
						//echo $out;
						if(strstr($out, $g_app1_ip1))
						{
							$delIP = $g_app1_ip2;
						}
						else
						if(strstr($out, $g_app1_ip2))
						{
							$delIP = $g_app1_ip1;
						}						
						else
							$delIP = $g_app1_ip1;
						
						//echo $delIP;
						
						if($delIP!='')
						{
							
							$uriBase = "http://".$delIP."/member/api/mpos/delpdf.php";//
							 $fields = [
								'pdfname'         => $pdfname2,
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
						$data = array();
						$data["status"]="true";
						$data["code"]="0x0200";						
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