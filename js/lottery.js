
var resultPid = 0;
var resultMsg = "";

var bCode = "";
var bUser = "";
var bPhone = "";
var bEmail = "";
var bQQ = "";
var bAddress = "";

var is_type = 1;

var is_lottery = false;


document.addEventListener("DOMContentLoaded", function () {

    var shape = document.getElementById("shape");
	
    var hitObj = {
        handleEvent: function (evt) {
            //if (!isWeixin) {
            //    myalert("提示信息", "请用微信打开参与本活动！");
            //    return;
            //}
			$("#logbox").slideUp(500);
			if(is_lottery==true){
				return;
			}
			var audio = new Audio();
			audio.src = "skin/images/smashegg.mp3";
			
            if("SPAN" == evt.target.tagName) {
				is_lottery = true;
				$.ajax({
					url: "bonus.php",
						type: "POST",
						dataType: "json",
						async: true,
						success: function(obj) {
							switch (obj.stat) {
								case '-1'://未登录
									//$("#logbox").slideDown(500);
									myalert("温馨提示", "用户信息读取失败,请刷新页面!!");
									is_lottery = false;
									break;	
								case '-2'://机会用完了					
									$("#divNoChance").show();
									$("#shape").hide();
									bCode = "";
									is_lottery = false;
									break;
								case '-5'://活动已结束
									$("#divTips").show();
									$("#shape").hide();
									is_lottery = false;
								case '0': //正常情况                   
									resultPid = obj.pId;
									resultMsg = obj.msg;
									is_type = obj.type;
									$("#hit").addClass("on").css({left: evt.pageX + "px",top: evt.pageY + "px"});
									evt.target.classList.toggle("on");
									evt.target.classList.toggle("luck");									
									//audio.play();
									
									
									setTimeout(function(){
										if(is_type == 3){//没有中奖
											ckInfo();
											$('#divNoWin').show();
											$("#shape").hide();
										}else{//中奖
											ckInfo();
											$('#winContent').html(resultMsg);
											$('#divWin').show();
											$("#shape").hide();
										}
										is_lottery = false;
										$('.plane .on').removeClass('on').removeClass('luck');
										$("#hit").removeClass("on");
									},1000)
									
									/*
									setTimeout(function() {
										myalert("温馨提示",resultMsg,function () {
											is_lottery = false;
											$('.plane .on').removeClass('on').removeClass('luck');$("#hit").removeClass("on");
											if(is_type==2){
												$("#logbox").slideDown(500);
											}
										});
										ckInfo();										
									},1000)
									*/
									break;
								default:
									is_lottery = false;
									myalert('温馨提示',obj.msg);
									break;
							}							
						}
				});
				
			}
        }
    };
    shape.addEventListener("click", hitObj, false);

}, false);


//关闭窗口
function closeDiv(){
	$("#divTips").hide();
	$("#divNoChance").hide();
	$("#divNoWin").hide();
	$("#divWin").hide();
	
	$("#shape").show();
}


