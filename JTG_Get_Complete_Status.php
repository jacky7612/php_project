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
//$Member_name = isset($_POST['Member_name']) ? $_POST['Member_name'] : '';
//$Role = isset($_POST['Role']) ? $_POST['Role'] : '1';
	$Insurance_no = check_special_char($Insurance_no);

function getStatus($conn,$order_no){
	try {
		//D0: 臉部辨識失敗
		//D1: 臉部辨識成功
		//E0: 活體檢測: 隨機動作辨識失敗
		//E1: 活體檢測: 隨機動作辨識成功
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql2 = "SELECT * FROM orderlog ";
		//echo $sql;
		if ($order_no != "") {	
			$sql2 = $sql2." where order_no='".$order_no."' and order_status in ('F1','H1','J1','K5') ";
		}
		//echo $sql2;
		if ($result2 = mysqli_query($conn, $sql2)){
			$identity = "0";
			$proposal = "0";
			$video = "0";
			$undersign = "0";
			$identitydate = "null";
			$proposaldate = "null";
			$videodate = "null";
			$undersigndate = "null";
			$fields1 = array();
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				$order_status="";
				while($row2 = mysqli_fetch_array($result2)){
					$order_status = $row2['order_status'];  
					$orderstatus = str_replace(",", "", $order_status);
					$log_date = str_replace(",", "", $row2['log_date']);
					switch ($orderstatus) {
						case "F1":
							$identity = "1";
							$identitydate = $log_date;
							break;
						case "H1":
							$proposal = "1";
							$proposaldate = $log_date;
							break;
						case "J1":
							$video = "1";
							$videodate = $log_date;
							break;
						case "K5":
							$undersign = "1";
							$undersigndate = $log_date;
							break;
					}
				}

				$data1 = [
					'status'       	=> $identity,   
					'finishedDate'  => $identitydate
				];
				$data2 = [
					'status'        => $proposal,
					'finishedDate'  => $proposaldate
				];
				$data3 = [
					'status'        => $video,
					'finishedDate'  => $videodate
				];
				$data4 = [
					'status'        => $undersign, 
					'finishedDate'  => $undersigndate
				];
				$fields1 =[ "identity"  => $data1, "proposal"  => $data2, "video"  => $data3,"undersign" => $data4 ];
				//var_dump($fields1);

				//{
				//	"success": true,
				//	"code": null,
				//	"message": null,
				//	"data": {
				//		"identity": {	///身份採證 F1
				//			"status": "1",
				//			"finishedDate": "2021-07-15 21:24:30"
				//		},
				//		"proposal": {	///要保書資料確認	H1
				//			"status": "1",
				//			"finishedDate": "2021-07-15 21:24:30"
				//		},
				//		"video": {	///視訊影音投保    J1
				//			"status": "0",
				//			"finishedDate": null
				//		},
				//		"undersign": {	///電子影音簽名	   K5
				//			"status": "0",
				//			"finishedDate": null
				//		}
				//	}
				//}
							
			}else{

				$data1 = [
					'status'       	=> $identity,   
					'finishedDate'  => $identitydate
				];
				$data2 = [
					'status'        => $proposal,
					'finishedDate'  => $proposaldate
				];
				$data3 = [
					'status'        => $video,
					'finishedDate'  => $videodate
				];
				$data4 = [
					'status'        => $undersign, 
					'finishedDate'  => $undersigndate
				];
				$fields1 =[ "identity"  => $data1, "proposal"  => $data2, "video"  => $data3,"undersign" => $data4 ];
				//var_dump($fields1);
				//var_dump($fields1);
				//$data["status"]="false";
				//$data["code"]="0x0205";
				//$data["responseMessage"]="尚未做臉部辨識與活體檢測";	
				//$data["data"]=$fields1;										
			}
		}else{
			//$data["status"]="false";
			//$data["code"]="0x0204";
			//$data["responseMessage"]="SQL fail!";
			$fields1="";
		}
	} catch (Exception $e) {
		//$data["status"]="false";
		//$data["code"]="0x0202";
		//$data["responseMessage"]="Exception error!";
		$fields1="";
	
	}	
	return $fields1;
}

	if (($Insurance_no != '')) {

		try {
			$link = mysqli_connect($host, $user, $passwd, $database);
			mysqli_query($link,"SET NAMES 'utf8'");

			$Insurance_no  = mysqli_real_escape_string($link,$Insurance_no);
			//$Member_name  = mysqli_real_escape_string($link,$Member_name);
			//$Role  = mysqli_real_escape_string($link,$Role);

$Insuranceno = trim(stripslashes($Insurance_no));

			$sql = "SELECT a.*,b.member_name FROM orderinfo a ";
			$sql = $sql." inner join ( select person_id,member_name from memberinfo) as b ON a.person_id= b.person_id ";
			$sql = $sql." where  a.order_trash=0 ";
			//echo $sql;
			if ($Insuranceno != "") {	
				$sql = $sql." and a.order_no='".$Insuranceno."'";
			}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					//$mid=0;
					//$order_status="";
					$fields2 = array();

					$fields2=getStatus($link,$Insuranceno);
					$data["status"]="true";
					$data["code"]="0x0200";
					$data["responseMessage"]="查詢成功";	
					$data["data"]=$fields2;			

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