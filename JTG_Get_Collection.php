<?php
//include("header_check.php");
include("db_tools.php");
include("security_tools.php");
$headers =  apache_request_headers();
$token = $headers['Authorization'];
/*
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
*/
$Insurance_no = isset($_POST['Insurance_no']) ? $_POST['Insurance_no'] : '';
//$Member_name = isset($_POST['Member_name']) ? $_POST['Member_name'] : '';
//$Role = isset($_POST['Role']) ? $_POST['Role'] : '1';
	$Insurance_no = check_special_char($Insurance_no);

function getStatus($conn,$order_no,$s_id,$p_id,$m_no,$m_type,$m_name, $keys){
	try {
		//D0: 臉部辨識失敗
		//D1: 臉部辨識成功
		//E0: 活體檢測: 隨機動作辨識失敗
		//E1: 活體檢測: 隨機動作辨識成功
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql2 = "SELECT * FROM orderlog ";
		//echo $sql;
		if ($order_no != "") {
			$sql2 = $sql2." where order_no='".$order_no."' and sales_id='$s_id' and person_id='$p_id' and mobile_no='$m_no' and member_type='$m_type' and order_status in ('D0','D1','E0','E1','D2') ";
		}
		//echo $sql2;
		if ($result2 = mysqli_query($conn, $sql2)){
			$Face_compare = "0";
			$Live_check = "0";
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				$order_status="";

				while($row2 = mysqli_fetch_array($result2)){
					//$mid = $row['mid'];
					$order_status = str_replace(",", "", $row2['order_status']);
					switch ($order_status) {
						case "D1":
							$Face_compare = "1";
							break;
						case "E1":
							$Live_check = "1";
							break;
					}
				}

				switch ($m_type) {
					case "1":
						$Rolekey="proposer";
						break;
					case "2":
						$Rolekey="insured";
						break;
					case "3":
						$Rolekey="legalRepresentative";
						break;
					//default:
					//	$Rolekey="";
				}
				//2022/5/5 for new country code
				$sql3 = "select * from countrylog where Person_id = '$p_id' and Insurance_id='$order_no' ";
				$result3 = mysqli_query($conn, $sql3);
				while($row3 = mysqli_fetch_array($result3)){
					$gps_country_code = $row3['countrycode'];
				}
				if ($Rolekey != "") {
					$fields1 = [
						'Rolekey'       => $Rolekey,  //1,2,3
						'Person_id'     => $p_id,
						'Mobile_no'     => decrypt($keys, stripslashes($m_no)),
						'Member_name'   => decrypt($keys, stripslashes($m_name)),
						'Face_compare'	=> $Face_compare,
						'Live_check'	=> $Live_check,
						'gps_country_code' => $gps_country_code
					];
				}else{
					$fields1="";
				}
				//var_dump($fields1);
				//$data["status"]="true";
				//$data["code"]="0x0200";
				//$data["responseMessage"]="查詢成功";
				//$data["data"]=$fields1;
			}else{
				$fields1="";
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
			$sql = $sql." where  a.order_trash=0 and a.member_type in (1,2,3)";
			//echo $sql;
			if ($Insuranceno != "") {
				$sql = $sql." and a.order_no='".$Insuranceno."'";
			}
			//$sql = $sql." order by a.member_type";
			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					//$mid=0;
					//$order_status="";
					$fields2 = array();
					while($row = mysqli_fetch_array($result)){
						//$mid = $row['mid'];
						$sales_id = str_replace(",", "", $row['sales_id']);
						$person_id = str_replace(",", "", $row['person_id']);
						$mobile_no = str_replace(",", "", $row['mobile_no']);
						$member_type = str_replace(",", "", $row['member_type']);
						$member_name = str_replace(",", "", $row['member_name']);

						$sales_id = check_special_char($sales_id);
						$person_id = check_special_char($person_id);
						$mobile_no = check_special_char($mobile_no);
						$member_type = check_special_char($member_type);
						$member_name = check_special_char($member_name);

						if ($member_name != "") {
							$data2=getStatus($link,$Insuranceno,$sales_id,$person_id,$mobile_no,$member_type,$member_name, $key);
							if ($data2 != "") {
								 array_push($fields2, $data2);
							}
						}
					}
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