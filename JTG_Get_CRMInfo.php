<?php
//include("header_check.php");
include("db_tools.php"); 
include("nas_ip.php");
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
}*/

//$sip = "127.0.0.1";

$Insurance_no = isset($_POST['applyNo']) ? $_POST['applyNo'] : '';
	$Insurance_no = check_special_char($Insurance_no);

$saveType = "DB";
function getSaveType()
{
	global $saveType;
	return $saveType;
}
function setSaveType($Type)
{
	global $saveType;
	$saveType = $Type;
}
function getpidpic($conn,$p_id,$keys){
	try {
		
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql3 = "SELECT pid_pic FROM memberinfo ";
		//echo $sql;
		$pid = trim(stripslashes($p_id));
		if ($pid != "") {	
			$sql3 = $sql3." where person_id='".$pid."' and member_trash = 0 ";
		}
		//echo $sql2;
		$pidpic = "";
		if ($result2 = mysqli_query($conn, $sql3)){
			if (mysqli_num_rows($result2) > 0){
				while($row2 = mysqli_fetch_array($result2)){
					//$pidpic = base64_encode($row2['pid_pic']);
					$pidpic = decrypt($keys,$row2['pid_pic']);
					break;
				}
			}else {
				$pidpic = "";
			}
		}else {
			$pidpic = "";
		}
	} catch (Exception $e) {
		$pidpic="";
	}	
	return $pidpic;	
}
function getpidpic2($conn,$p_id,$keys,$front, $order_no){
	try {
		
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql3 = "SELECT * FROM idphoto ";
		//echo $sql;
		$pid = trim(stripslashes($p_id));
		
		if ($pid != "") {	
			$sql3 = $sql3." where person_id='".$pid."'";
		}
		//echo $sql3;
		if($order_no != "") {
			$sql3 .= " and insurance_id='".$order_no."'";
			
		}
		
		
		$pidpic2 = "";
		//echo $saveType;
		if ($result2 = mysqli_query($conn, $sql3)){
			if (mysqli_num_rows($result2) > 0){
				
				while($row2 = mysqli_fetch_array($result2)){
					
					if($row2['saveType']=='NAS'){
						setSaveType("NAS");
						//$saveType = "NAS";
						if ($front == "0"){
								if ($row2['frontpath'] != null) {
									$fp=fopen($row2['frontpath'], "r");
									$out=fread($fp, filesize($row2['frontpath']));
									fclose($fp);
									//echo decrypt($keys,$out); 
									//echo $row2['frontpath'];
									
									$pidpic2 =  decrypt($keys,$out); //decrypt($keys,$row2['front']);
								}else{
									$pidpic2 = "";
								}
							}
						if ($front == "1"){
							if ($row2['backpath'] != null) {
								$fp=fopen($row2['backpath'], "r");
								$out=fread($fp, filesize($row2['backpath']));
								fclose($fp);
									//echo decrypt($keys,$out); 
								$pidpic2 =  decrypt($keys,$out);
							}else{
								$pidpic2 = "";
							}							
						}
					}
					else
					{
						//echo "DB";
						setSaveType("DB");
						if ($front == "0"){
							if ($row2['front'] != null) {
								$pidpic2 = decrypt($keys,$row2['front']);
							}else{
								$pidpic2 = "";
							}
						}
						if ($front == "1"){
							if ($row2['back'] != null) {
								$pidpic2 = decrypt($keys,$row2['back']);
							}else{
								$pidpic2 = "";
							}						
						}
					}
					
					break;
				}
			}else {
				//有可能是舊的方式,沒有儲存 insurance_id
					$sql3 = "SELECT * FROM idphoto ";
					//echo $sql;					
					if ($pid != "") {	
						$sql3 = $sql3." where person_id='".$pid."'";
					}				
					if ($result2 = mysqli_query($conn, $sql3)){
						if (mysqli_num_rows($result2) > 0){							
							while($row2 = mysqli_fetch_array($result2)){
								if($row2['saveType']=='NAS'){
									setSaveType("NAS");
									//$saveType = "NAS";
									if ($front == "0"){
											if ($row2['frontpath'] != null) {
												$fp=fopen($row2['frontpath'], "r");
												$out=fread($fp, filesize($row2['frontpath']));
												fclose($fp);
												//echo decrypt($keys,$out); 
												//echo $row2['frontpath'];
												
												$pidpic2 =  decrypt($keys,$out); //decrypt($keys,$row2['front']);
											}else{
												$pidpic2 = "";
											}
										}
									if ($front == "1"){
										if ($row2['backpath'] != null) {
											$fp=fopen($row2['backpath'], "r");
											$out=fread($fp, filesize($row2['backpath']));
											fclose($fp);
												//echo decrypt($keys,$out); 
											$pidpic2 =  decrypt($keys,$out);
										}else{
											$pidpic2 = "";
										}							
									}
								}
								else
								{
									//echo "DB";
									setSaveType("DB");
									if ($front == "0"){
										if ($row2['front'] != null) {
											$pidpic2 = decrypt($keys,$row2['front']);
										}else{
											$pidpic2 = "";
										}
									}
									if ($front == "1"){
										if ($row2['back'] != null) {
											$pidpic2 = decrypt($keys,$row2['back']);
										}else{
											$pidpic2 = "";
										}						
									}
								}
								
								break;
								
							}
						}
						else
						{
							$pidpic2 = "";
						}
					}
				
			}
		}else {
			$pidpic2 = "";
		}
	} catch (Exception $e) {
		$pidpic2="";
	}	
	return $pidpic2;	
}
function getuserList($conn,$order_no,$keys){
	try {
		
		$orderno = trim(stripslashes($order_no));

		//$sql2 = "SELECT a.*,b.member_name FROM orderlog a ";
		//$sql2 = $sql2." inner join ( select person_id,member_name from memberinfo) as b ON a.person_id= b.person_id ";
		//if ($orderno != "") {	
		//	$sql2 = $sql2." where a.order_no='".$orderno."' and a.member_type in (1,2,3) and a.order_status in ('D0','D1') order by a.member_type, a.order_status ";
		//}	
		$sql2 = "( SELECT a.*,b.member_name FROM orderlog a inner join ( select person_id,member_name from memberinfo) as b ON a.person_id= b.person_id ";
		$sql2 = $sql2." where a.order_no='".$orderno."' and a.member_type = 1 and a.order_status in ('D0','D1' ,'D2') order by log_date desc limit 1 )";
		$sql2 = $sql2." UNION ( SELECT a.*,b.member_name FROM orderlog a inner join ( select person_id,member_name from memberinfo) as b ON a.person_id= b.person_id ";
		$sql2 = $sql2." where a.order_no='".$orderno."' and a.member_type = 2 and a.order_status in ('D0','D1','D2') order by log_date desc limit 1 )";
		$sql2 = $sql2." UNION ( SELECT a.*,b.member_name FROM orderlog a inner join ( select person_id,member_name from memberinfo) as b ON a.person_id= b.person_id ";
		$sql2 = $sql2." where a.order_no='".$orderno."' and a.member_type = 3 and a.order_status in ('D0','D1','D2') order by log_date desc limit 1 )";

		//echo $sql2;

		$fields1 = array();
		if ($result2 = mysqli_query($conn, $sql2)){

			
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				$order_status="";
				while($row2 = mysqli_fetch_array($result2)){
					$order_status = $row2['order_status'];  
					$orderstatus = str_replace(",", "", $order_status);
					$orderstatus = check_special_char($orderstatus);
					$log_date = date('YmdHis', strtotime($row2['log_date']));
					$person_id = $row2['person_id'];
					$member_name = decrypt($keys,stripslashes($row2['member_name']));
					$identifyResultStatus = "";
					$identifyFinishDate = "";

					switch ($orderstatus) {
						case "D0":
							$identifyResultStatus = "不通過";
							$identifyFinishDate = $log_date;
							break;
						case "D1":
							$identifyResultStatus = "通過";
							$identifyFinishDate = $log_date;
							break;
						default:
							$identifyResultStatus = "";
							$identifyFinishDate = "";
					}
					$member_types = str_replace(",", "", $row2['member_type']);
					$member_types = check_special_char($member_types);
					switch ($member_types) {
						case "1":
							$membertype = "要保人";
							break;
						case "2":
							$membertype = "被保人";
							break;
						case "3":
							$membertype = "法定代理人";
							break;
						default:
							$membertype = "";
					}
					$pid = str_replace(",", "", $person_id);
					$pid = check_special_char($pid);
					$pname = str_replace(",", "", $member_name);
					$pname = check_special_char($pname);

					//$saveType = "DB";
					$iid = getpidpic($conn,$pid,$keys);
					$fid = getpidpic2($conn,$pid,$keys,"0", $order_no);
					$bid = getpidpic2($conn,$pid,$keys,"1", $order_no);
					$data2 = [
						'userId'       			=> $pid,   
						'userName'       		=> $pname, 
						'userType'   			=> $membertype,   
						'userPhoto'      		=> ($iid==false ||$iid == "") ? null:$iid,    //getpidpic($conn,$pid)
						'identifyResultStatus'  => $identifyResultStatus,
						'identifyFinishDate'    => $identifyFinishDate,
						'frontIdPhoto'    		=> ($fid==false || $fid == "") ? null:$fid,
						'backIdPhoto'    		=> ($bid==false || $bid == "") ? null:$bid,
						'saveType'    			=> getSaveType()
					];
					array_push($fields1, $data2);
				}
				//$userList = [ ["userId" => "A123456789","userType" => "要保人","userPhoto" => "fajsdihjproi;rjgkljdsiofjsadljasie;fijwflkjdfoia==","identifyResultStatus" => "通過", "identifyFinishDate" => "20210721121527333" ],["userId" => "A123456789","userType" => "被保人","userPhoto" => "fajsdihjproi;rjgkljdsiofjsadljasie;fijwflkjdfoia==","identifyResultStatus" => "通過", "identifyFinishDate" => "20210721121527333"] ];

			}else{
				$fields1=null;
			}
		}else{

			$fields1=null;
		}
	} catch (Exception $e) {

		$fields1=null;
	
	}	
	return $fields1;
}

function getmeetinglog($conn,$order_no){
	try {
		$orderno = trim(stripslashes($order_no));
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql2 = "SELECT * FROM meetinglog ";
		//echo $sql;
		if ($orderno != "") {	
			$sql2 = $sql2." where insurance_no='".$orderno."' ";
		}
		$userposlist2 = array();
		if ($result2 = mysqli_query($conn, $sql2)){

			
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				//echo 'ok';
				while($row2 = mysqli_fetch_array($result2)){
					if ($row2['proposer_id'] != null) {
						$data1 = [
							'userId'       	=> $row2['proposer_id'],   
							'userType'      => "要保人",   
							'gpsInfo'       => $row2['proposer_gps'],   
							'addrDesc'  	=> $row2['proposer_addr']
						];
						array_push($userposlist2, $data1);
					}
					if ($row2['insured_id'] != null) {
						$data2 = [
							'userId'       	=> $row2['insured_id'],   
							'userType'      => "被保人",   
							'gpsInfo'       => $row2['insured_gps'],   
							'addrDesc'  	=> $row2['insured_addr']
						];
						array_push($userposlist2, $data2);
					}
					if ($row2['legalRep_id'] != null) {
						$data3 = [
							'userId'       	=> $row2['legalRep_id'],   
							'userType'      => "法定代理人",   
							'gpsInfo'       => $row2['legalRep_gps'],   
							'addrDesc'  	=> $row2['legalRep_addr']
						];
						array_push($userposlist2, $data3);
					}
					break;
				}
				//$userposlist2 = [ ["userId" => "A123456789","userType" => "要保人","gpsInfo" => "(24.000,125.0000)","addrDesc" => "台北市 信義區市民大道六段....."],["userId" => "B123456789","userType" => "被保人","gpsInfo" => "(24.000,125.0000)","addrDesc" => "台北市 信義區市民大道六段....."]];
			}else{
				$userposlist2 = null;
			}
		}else{

			$userposlist2=null;
		}
	} catch (Exception $e) {
		$userposlist2=null;
	}	
	return $userposlist2;
}

function getvideolist($conn,$order_no,$sip){
	try {
		$orderno = trim(stripslashes($order_no));
		
		$sql2 = "SELECT * FROM meetinglog ";
		//echo $sql;
		if ($orderno != "") {	
			$sql2 = $sql2." where insurance_no='".$orderno."'";
		}
		$videolist = array();
		if ($result2 = mysqli_query($conn, $sql2)){

			
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				//url = "http://"+sip+"/dis_vdm/"+folder+"/"+id;
				$data2 = array();
				$poslist = array();
				while($row2 = mysqli_fetch_array($result2)){
					$meetingid = str_replace(",", "", $row2['meetingid']);
					if ($row2['bDownload'] == 1) { 
						$poslist = array();
						if ($row2['proposer_id'] != null) {
							$data1 = [
								'userId'       	=> $row2['proposer_id'],   
								'userType'      => "要保人",   
								'gpsInfo'       => $row2['proposer_gps'],   
								'addrDesc'  	=> $row2['proposer_addr']
							];
							array_push($poslist, $data1);
						}
						if ($row2['insured_id'] != null) {
							$data2 = [
								'userId'       	=> $row2['insured_id'],   
								'userType'      => "被保人",   
								'gpsInfo'       => $row2['insured_gps'],   
								'addrDesc'  	=> $row2['insured_addr']
							];
							array_push($poslist, $data2);
						}
						if ($row2['legalRep_id'] != null) {
							$data3 = [
								'userId'       	=> $row2['legalRep_id'],   
								'userType'      => "法定代理人",   
								'gpsInfo'       => $row2['legalRep_gps'],   
								'addrDesc'  	=> $row2['legalRep_addr']
							];
							array_push($poslist, $data3);
						}					
					    if ($row2['bSaved'] == "1") {
							$bSaved = "1";
						}else{
							$bSaved = "0";
						}					
					
					   //$downloadurl = "http://".$sip."/dis_vdm/".date('Ymd', strtotime($row2['starttime']))."/".$row2['filename'];	
					    $downloadurl = "http://".$sip.$row2['filename'];
						$data = [
							'roomNo'       			=> $meetingid,   
							'recordingStartDate'    => substr(date('YmdHisu', strtotime($row2['starttime'])),0,17),   
							'recordingEndDate'      => substr(date('YmdHisu', strtotime($row2['stoptime'])),0,17),
							'recSave'  				=> $bSaved,							
							'url'  					=> $downloadurl,
							'userPositionList'      => $poslist
						];
						array_push($videolist, $data);

					}else{
					   $downloadurl = "";
					   $poslist = array();
						if ($row2['proposer_id'] != null) {
							$data1 = [
								'userId'       	=> $row2['proposer_id'],   
								'userType'      => "要保人",   
								'gpsInfo'       => $row2['proposer_gps'],   
								'addrDesc'  	=> $row2['proposer_addr']
							];
							array_push($poslist, $data1);
						}
						if ($row2['insured_id'] != null) {
							$data2 = [
								'userId'       	=> $row2['insured_id'],   
								'userType'      => "被保人",   
								'gpsInfo'       => $row2['insured_gps'],   
								'addrDesc'  	=> $row2['insured_addr']
							];
							array_push($poslist, $data2);
						}
						if ($row2['legalRep_id'] != null) {
							$data3 = [
								'userId'       	=> $row2['legalRep_id'],   
								'userType'      => "法定代理人",   
								'gpsInfo'       => $row2['legalRep_gps'],   
								'addrDesc'  	=> $row2['legalRep_addr']
							];
							array_push($poslist, $data3);
						}							   
					    if ($row2['bSaved'] == "1") {
							$bSaved = "1";
						}else{
							$bSaved = "0";
						}
						$data = [
							'roomNo'       			=> $meetingid,   
							'recordingStartDate'    => substr(date('YmdHisu', strtotime($row2['starttime'])),0,17),   
							'recordingEndDate'      => substr(date('YmdHisu', strtotime($row2['stoptime'])),0,17),
							'recSave'  				=> $bSaved,
							'url'  					=> $downloadurl,
							'userPositionList'      => $poslist
						];
						array_push($videolist, $data);
					   
					}
				}

				//$videoList = ["roomNo" => "885","recordingStartDate" => "20210721121527333","recordingEndDate" => "20210721131527333","url" => "http://3.37.63.32/vrms/download/1100703xxxx","userPositionList" => $userposlist ];
						
			}else{
				$videolist = null;
			}
		}else{

			$videolist=null;
		}
	} catch (Exception $e) {

		$videolist=null;
	
	}	
	return $videolist;
}

function getpolicyList($conn,$order_no)
{
	try {
		$orderno = trim(stripslashes($order_no));
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql2 = "SELECT * FROM `caseproducts` ";
		//echo $sql;
		if ($orderno != "") {	
			$sql2 = $sql2." where applyNo='".$orderno."' order by policyCode ";
		}
		$fields1 = array();
		if ($result2 = mysqli_query($conn, $sql2)){

			
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				$policyCode="";
				while($row2 = mysqli_fetch_array($result2)){
					$policyCode = $row2['policyCode'];  
					$policyCode2 = str_replace(",", "", $policyCode);

					array_push($fields1, $policyCode2);
				}
			}else{
				$fields1="";
			}
		}else{

			$fields1="";
		}
	} catch (Exception $e) {

		$fields1="";
	
	}	
	return $fields1;
}

function getnumbering($conn,$order_no) {
	try {
		$orderno = trim(stripslashes($order_no));
		//oid,order_no,sales_id,person_id,mobile_no,member_type,order_status,log_date
		$sql2 = "SELECT * FROM `caseinfo` ";
		//echo $sql;
		if ($orderno != "") {	
			$sql2 = $sql2." where applyNo='".$orderno."' ";
		}
		$numbering2="";
		if ($result2 = mysqli_query($conn, $sql2)){

			$fields1 = array();
			if (mysqli_num_rows($result2) > 0){
				//$mid=0;
				
				while($row2 = mysqli_fetch_array($result2)){
					$numbering = $row2['numbering'];  
					$numbering2 = str_replace(",", "", $numbering);

					break;
				}
			}else{
				$numbering2="";
			}
		}else{

			$numbering2="";
		}
	} catch (Exception $e) {

		$numbering2="";
	
	}	
	return $numbering2;	
}
//-----------------start main process -------------------------

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
			$sql = $sql." where  a.order_trash=0 and a.order_status = 'K5' ";
			//echo $sql;
			if ($Insuranceno != "") {	
				$sql = $sql." and a.order_no='".$Insuranceno."'";
			}

			if ($result = mysqli_query($link, $sql)){
				if (mysqli_num_rows($result) > 0){
					//$mid=0;
					//$order_status="";
					$fields2 = array();
					$policyList = array();
					$userList = array();
				    $userposlist = array();
					$videoList = array();
					$numbering = "";
					
					$row = mysqli_fetch_assoc($result);
	
					//$fields2=getStatus($link,$Insurance_no);
					
					$insuredDate = date('Ymd', strtotime($row['inputdttime']));  //"20210720";
					
					//$numbering = "7000010101";
					$numbering = getnumbering($link,$Insuranceno);
					
					$policyList = getpolicyList($link,$Insuranceno);
					//$policyList = ["00001512581", "00001512582", "00001512583"];  //product
					
					$agentId = $row['sales_id']; //"00123456";
					
					$userList = getuserList($link,$Insuranceno,$key);
					//$userList = [ ["userId" => "A123456789","userType" => "要保人","userPhoto" => "fajsdihjproi;rjgkljdsiofjsadljasie;fijwflkjdfoia==","identifyResultStatus" => "通過", "identifyFinishDate" => "20210721121527333" ],["userId" => "A123456789","userType" => "被保人","userPhoto" => "fajsdihjproi;rjgkljdsiofjsadljasie;fijwflkjdfoia==","identifyResultStatus" => "通過", "identifyFinishDate" => "20210721121527333"] ];
					
					//$userposlist = getmeetinglog($link,$Insurance_no);
					//$userposlist = [ ["userId" => "A123456789","userType" => "要保人","gpsInfo" => "(24.000,125.0000)","addrDesc" => "台北市 信義區市民大道六段....."],["userId" => "B123456789","userType" => "被保人","gpsInfo" => "(24.000,125.0000)","addrDesc" => "台北市 信義區市民大道六段....."]];
					
					$videoList = getvideolist($link,$Insuranceno,$sip);
					//$videoList = getvideolist($link,$Insurance_no,$sip,$userposlist);
					//$videoList = ["roomNo" => "885","recordingStartDate" => "20210721121527333","recordingEndDate" => "20210721131527333","url" => "http://3.37.63.32/vrms/download/1100703xxxx","userPositionList" => $userposlist ];

					$fields2 = ["code" => "0", "msg" => "查詢成功","insuredDate"  => $insuredDate, "numbering"  => $numbering, "policyList"  => $policyList, "agentId" => $agentId, "userList" => $userList, "videoList" => $videoList ];
	
					$data = $fields2;					

				}else{
					$data["code"]="-1";
					$data["msg"]="不存在此要保流水序號的資料!";						
				}
			}else {
					$data["code"]="-1";
					$data["msg"]="SQL fail!";					
			}
			mysqli_close($link);
		} catch (Exception $e) {
			$data["code"]="-1";
			$data["msg"]="Exception error!";					
        }
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));
	}else{
		//echo "need mail and password!";
		$data["code"]="-1";
		$data["msg"]="API parameter is required!";
		header('Content-Type: application/json');
		echo (json_encode($data, JSON_UNESCAPED_UNICODE));			
	}
?>