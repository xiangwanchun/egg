<?php
	echo "{\"stat\":\"0\",\"pid\":\"3\",\"msg\":\"\u606d\u559c\u4f60\uff0c\u83b7\u5f97USB\u5c0f\u98ce\u6247\u4e00\u53f0\uff01\",\"type\":\"1\"}";

	return;

include 'init.php';
include 'lib/bonusAPI.php';
include_once('lib/SobeyInterfaceClient.php');

//配置项
$appkey = '123456789';									// 接入平台所颁发的KEY
$appsecret = '123456789';								// 接入平台所颁发的密匙
$apiurl = 'http://113.142.30.203/creditshop/creditshop';		// 平台接口地址
		
	//读取抽奖配置文件
	$row = $database->get(DB_PREFIX."lottery_setting",'*',array("id"=>1));
	$intPercent = $row["percent"];
	$intIsOpen = $row["is_open"];
	$CloseDesc = $row["close_desc"];
	$intBonus = $row["invite_times"];
	$StartTime = $row["start_time"];
	$EndTime = $row["end_time"];

	$strPids = "";
	$strProbaly = "";

	//读取礼品数据
	$datas_prize = $database->select(DB_PREFIX."lottery_prize",'*',array('ORDER'=>'probaly'));
	for($m = 0; $m < count($datas_prize); $m ++)
	{
		$row = $datas_prize[$m];	
		$strPids .= $row["prize_id"];	
		$strProbaly .= $row["probaly"];	
		if($m<(count($datas_prize)-1)){		
			$strPids.="|";
			$strProbaly.="|";
		}
	}
	
	$datas_count = $database->sum(DB_PREFIX."lottery_prize","probaly");	
	if($datas_count != $intPercent){
		$arr = array('stat'=>'-7','msg'=>'礼品概率配置错误');
		echo json_encode($arr);
		return;
	}

	//判断是否开启
	if($intIsOpen==0){
		$arr = array('stat'=>'5','msg'=>$CloseDesc);
		echo json_encode($arr);
		return;
	}
		
	//判断时间条件			
	$intToday = time();

	if($intToday>=strtotime($StartTime)&&$intToday<=strtotime($EndTime)){
	}else{
		$arr = array('stat'=>'5','msg'=>$CloseDesc);
		echo json_encode($arr);
		return;
	}
	
	
	$uid = isset($_SESSION['xymf_uid'])?$_SESSION['xymf_uid']:'';
	if(empty($uid)){
		//用户未登录
		echo "{\"stat\":\"-1\"}";
		return ;
	}
	
	$userInfo = $database->get(DB_PREFIX.'users','*',array('user_name'=>$uid));
	if(!$userInfo){
		//用户未登录
		echo "{\"stat\":\"-1\"}";
		return ;
	}
	
	if($userInfo['score'] == 0){
		//没有抽奖机会
		echo "{\"stat\":\"-2\"}";
		return ;
	}
	
	//抽奖方法
	$bonus = new BonusAPI();
	$result = $bonus->wonAPI($intPercent, $strPids, $strProbaly);
		
	$ckrow = $database->get(DB_PREFIX."lottery_prize","*",array('prize_id'=>$result));
	$PrizeNum = $ckrow["prize_num"];	
	$PrizeName = $ckrow["prize_name"];		
	$msg = $ckrow["prize_details"];
	$prizeType = $ckrow["prize_type"];
	$prizeValue = $ckrow["prize_value"];

	if($PrizeNum == 0){
		$ckrow = $database->get(DB_PREFIX."lottery_prize","*",array('prize_num[!]'=>0,'ORDER'=>'probaly DESC'));
		$result = $ckrow["prize_id"];
		$PrizeNum = $ckrow["prize_num"];	
		$PrizeName = $ckrow["prize_name"];		
		$msg = $ckrow["prize_details"];
		$prizeType = $ckrow["prize_type"];
		$prizeValue = $ckrow["prize_value"];
	}

	if(intval($PrizeNum) > 0){	
		$database->update(DB_PREFIX."lottery_prize",array('prize_num[-]'=>1),array('prize_id'=>$result));
	}
	
	$arr_insert = array('user_name'=>$uid,'bonus_code'=>$userInfo['user_nick'],'link_phone'=>$userInfo['link_phone'],'prize_id'=>$result,'prize_name'=>$PrizeName,'bonus_style'=>0,'won_time'=>date('Y-m-d H:i:s'),'is_send'=>0);
	
	//抽奖中了写入数据库
	$lastid = $database->insert(DB_PREFIX."lottery_win",$arr_insert);
	$database->update(DB_PREFIX.'users',array('score[-]'=>1),array('user_name'=>$uid));
	$_SESSION["last_id"] = $lastid;
	
	switch($prizeType){
		case 1:
			$action = 'addPrize/'.$uid;
			$params = array('type' => 2,'prizeid' => $prizeValue,	'source' => 'game',	'title' => '砸金蛋','objectid' => '1');
			$Client = new SobeyInterfaceClient(array('appkey'=>$appkey,'appsecret'=>$appsecret,'apiurl'=>$apiurl));
			$return = $Client->execute($action, $params, 'POST');
			if($return['code'] == '0000'){
				$database->update(DB_PREFIX.'lottery_win',array('bonus_style'=>1),array('id'=>$lastid));
			}
			break;
		case 2:
			$action = 'addPrize/'.$uid;
			$params = array('type' => 1,'credit' => $prizeValue,'source' => 'game','title' => '砸金蛋','objectid' => '1');
			$Client = new SobeyInterfaceClient(array('appkey'=>$appkey,'appsecret'=>$appsecret,'apiurl'=>$apiurl));
			$return = $Client->execute($action, $params, 'POST');
			if($return['code'] == '0000'){
				$database->update(DB_PREFIX.'lottery_win',array('bonus_style'=>1),array('id'=>$lastid));
			}
			break;
		default:
			break;
	}
	
	$arr = array("stat"=>"0","pid"=>$result,"msg"=>$msg,"type"=>$prizeType);
	echo json_encode($arr);
		
?>
