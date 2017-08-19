<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=<?php echo C('DEFAULT_CHARSET');?>" /><title><?php echo L('system_name');?></title><link rel="stylesheet" type="text/css" href="./Public/Css/style.css" /><script type="text/javascript" src="./Public/Js/jquery.min.js"></script><script type="text/javascript" src="./Public/Js/jquery.validate.js"></script><script type="text/javascript" src="./Public/Js/jquery.form.js"></script><script type="text/javascript" src="./Public/Js/my.js"></script><!--[if IE 6]><script src="./Public/Js/png.js"  type="text/javascript" ></script><script type="text/javascript">		DD_belatedPNG.fix(' .login_box ');
		</script><![endif]--></head><body onLoad="reload()" id="loginbg" ><form method='post' name="login" id="form1" action="<?php echo U('Login/doLogin');?>"><div class="login_box"><div class="login_title"><?php echo L('system_name');?></div><div class="login_left"><img src="./Public/Images/admin_logo.gif"   border="0" alt="<?php echo L('system_name');?>" ></div><div class="login_right"><div class="msg"><div id="result" class="result none"></div></div><div class="login_form"><ul><li><label><?php echo L('account');?>:</label><input type="text" id="username" class="input-text" name="username" size="16"></li><li><label><?php echo L('password');?>:</label><input type="password"  class="input-text" name="password" size="16"></li><?php if($admin_verify) : ?><li><label><?php echo L('verify');?>:</label><input name="verifyCode" class="input-text"  class="inputbox" id="verifyCode"  size="6" value="" maxlength="4" /><img src="<?php echo U('Home/Index/verify');?>" onclick="javascript:resetVerifyCode();" class="checkcode" align="absmiddle"  title="<?php echo L('resetVerifyCode');?>" id="verifyImage"/></li><?php endif;?><li><label></label><input type="hidden" name="ajax" value="1"><input type="submit" value="<?php echo L('login');?>" class="button"><input type="reset" name="reset" value="<?php echo L('reset');?>" class="button" id="reset" /></li></ul></div></div></div></form><script language="JavaScript">jQuery(document).ready(function($){
	$('#form1').ajaxForm({
		beforeSend:function(){
			$('#result').html('<img src="./Public/Images/msg_loading.gif">').show();;
		 },
		success:       complete,  // post-submit callback
		dataType: 'json'
	});


});
function complete(data){
        if (data.status==1)
        {
		 $('#result').html(data.info).show();
		 //art.dialog.tips('<?php echo L("logined_ok");?>',2);
		 setTimeout(function(){	window.location.href = '<?php echo U("Index/index");?>';},1000);
        }else{
            $('#result').html(data.info).show();
        }
}
function reload(){
	document.login.username.focus();
	if(self!=top){
	 window.top.location.href = '<?php echo U("Login/index");?>';
	}
	resetVerifyCode();
}

</script><style>body {background:#02609b url(../Images/login_bg.gif)   repeat-x;}
html{background:#02609b ;}</style></body></html>