<?php
include 'init.php';
$act = @$_GET["action"];

	//检查登录状态
	if($act == "check"){
		echo "{\"stat\":\"0\",\"score\":\"".$userInfo['score']."\"}";
		return;
		$uid = $_GET['uid'];
		
		if(empty($uid)){
			//用户未登录
			echo "{\"stat\":\"-1\"}";
			return ;
		}
		
		$userInfo = $database->get(DB_PREFIX.'users','*',array('user_name'=>$uid));
				
		echo "{\"stat\":\"0\",\"score\":\"".$userInfo['score']."\"}";
	}

	//ajax  完善资料
	if($act == "complete"){
		
		$last_id = @$_SESSION['last_id'];		
		if($last_id==""||$last_id==null){
			//没有需要完善的资料
			echo "{\"stat\":\"-2\"}";
			return;
		}
		
		$username = @$_SESSION['xymf_openid'];		
		if(empty($username)){
			//用户未登录
			echo "{\"stat\":\"-1\"}";
			return ;
		}		
		$appSet = $database->get(DB_PREFIX.'lottery_setting','*',array('id'=>1));		
		$api_url = $appSet['api_url'].$username;
		$ret_api = get_url_contents($api_url);	
		$ret_Arr = json_decode($ret_api);
		if($ret_Arr->Code != 10000){
			//用户未绑定
			echo "{\"stat\":\"-1\"}";
			return ;
		}
		
		$linkuser = @$_POST["link_user"];
		$linkphone = @$_POST["link_phone"];
		$linkaddress = @$_POST["link_address"];
		
		$database->update(DB_PREFIX."lottery_win",array('link_user'=>$linkuser,'link_phone'=>$linkphone,'link_address'=>$linkaddress),array('id'=>$last_id));
		
		echo "{\"stat\":\"0\"}";
		return;
	}
	
	
	if($act == "share"){
		
			
			$username = $_SESSION['xymf_openid'];
			
			if(empty($username)){
				//用户未登录
				echo "{\"stat\":\"-1\"}";
				return ;
			}
		
			$appSet = $database->get(DB_PREFIX.'lottery_setting','*',array('id'=>1));
			
			$api_url = $appSet['api_url'].$username;
			$ret_api = get_url_contents($api_url);	
			$ret_Arr = json_decode($ret_api);
			if($ret_Arr->Code != 10000){
				//用户未绑定
				echo "{\"stat\":\"-1\"}";
				return ;
			}
			$uid = $ret_Arr->Data;
			
			$info = $database->get(DB_PREFIX."users",'*',array('user_name'=>$uid));
			if(!$info){
				$database->insert(DB_PREFIX."users",array('user_name'=>$uid,'add_time'=>date('Y-m-d H:i:s'),'user_ip'=>get_client_ip()));
			}			
			
			echo "{\"stat\":\"0\"}";
			
		
	}

?>