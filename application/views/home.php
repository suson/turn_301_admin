<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=7" />
  <title>后台管理中心!</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
	<link href="<?php echo base_url() ?>public/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo base_url() ?>public/css/bootstrap-responsive.min.css" rel="stylesheet">
  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="<?php echo base_url() ?>public/js/html5shiv.js"></script>
  <![endif]-->
  
	<script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery-1.6.4.min.js"></script>
</head>

<body>
<div class="container-fluid" style="margin:0 auto;width:500px;margin-top:10%;background-color: #f5f5f5">
	<div class="row-fluid">
		<div class="span12">
			<h3 class="text-info text-center">
				后台管理中心
			</h3>
			<div class="tabbable" id="tabs-830279">
				<ul class="nav nav-tabs">
					<li class="active" id="a_login">
						<a  href="javascript:void(0)" class="login" data-toggle="tab">登陆</a>
					</li>
					<li id="a_reg" >
						<a  href="javascript:void(0)" class="reg" data-toggle="tab">注册</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="login">
						<form class="form-horizontal" name="login">
				<div class="control-group">
					 <label class="control-label" for="loginusername">用户名</label>
					<div class="controls">
						<input type="text" id="loginusername" name="username" value="<?php echo $loginuser ?>">
					</div>
				</div>
				<div class="control-group">
					 <label class="control-label" for="loginpassword">密码</label>
					<div class="controls">
						<input type="password" id="loginpassword" name="password">
					</div>
				</div>
				<div class="control-group ">
					<div class="controls">
						 <label class="checkbox"><input type="checkbox" name="reme" <?php echo $checked ?>>记住我</label> <button type="submit" id="loginbutton" class="btn btn-primary" data-loading-text="登陆中...">登陆</button>
					</div>
				</div>
			</form>
					</div>
					<div class="tab-pane" id="reg">
						<form name="reg">
				<div class="control-group">
					 <label class="control-label" for="username">用户名</label>
					<div class="controls">
						<input type="text" id="username" name="username" /><small><span class="text-error">*</span>
<span class="muted">(4-20)用户名为字母与数字组合,不能带有特殊字符</span></small>
					</div>
				</div>
				<div class="control-group">
					 <label class="control-label" for="password">密码</label>
					<div class="controls">
						<input type="password" id="password" name="password" /><small><span class="text-error ">*</span>
<span  class="muted">(6-20)密码为字母与数字、特殊字符等组合</span></small>
					</div>
				</div>
				<div class="control-group">
					 <label class="control-label" for="ppassword">再次输入密码</label>
					<div class="controls">
						<input type="password" id="ppassword" name="ppassword" /><small><span class="text-error">*</span>
<span  class="muted">两次密码必须一致</span></small>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						 <button type="submit" id="regbutton" class="btn btn-primary" data-loading-text="注册中...">注册</button>
					</div>
				</div>
			</form>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>
<div>
	<script type="text/javascript">
		$(document).ready(function(){
			var current = "<?php echo $current ?>";
			$("."+current).trigger("click");
                        $("#a_login").bind("click",function(){
                            $(".nav-tabs li").removeClass("active");
                            $(this).addClass("active");
                            $(".tab-pane").removeClass("active");
                            $("#login").addClass("active");
                        });
                        $("#a_reg").bind("click",function(){
                            $(".nav-tabs li").removeClass("active");
                            $(this).addClass("active");
                            $(".tab-pane").removeClass("active");
                            $("#reg").addClass("active");
                        })
			$("#loginbutton").bind("click",function(){
				var username = $.trim($("#loginusername").val());
				var password = $.trim($("#loginpassword").val());
				if(username == ''){
					alert("用户名不可为空！");
					return false;
				}
				if(password == ''){
					alert("密码不可为空！");
					return false;
				}

				var url = "<?php echo base_url('home/index') ?>";
				var data = $("form[name='login']").serialize();
				$.ajax({
					type : "post",
					url : url,
					data : data,
					dataType : 'json',
					success : function(res){
						if(res.success){
							var url = "<?php echo base_url('admin/index') ?>";
							window.location.href = url;
						}else{
							alert(res.message);
						}
					}
				})
				return false;
			});

			$("#regbutton").bind("click",function(){
				var username = $.trim($("#username").val());
				var password = $.trim($("#password").val());
				var ppassword = $.trim($("#ppassword").val());
				if(username == ''){
					alert("用户名不可为空！");
					return false;
				}
				if(password == ''){
					alert("密码不可为空！");
					return false;
				}
				if(ppassword == ''){
					alert("再次输入密码不可为空！");
					return false;
				}
				if(password != ppassword){
					alert("再次输入密码不一致！");
					return false;
				}
				if(username.length < 4 || username.length > 20){
					alert("用户名长度不符合！");
					return false;
				}
				if(ppassword.length < 6 || password.length > 20){
					alert("密码长度不符合！");
					return false;
				}
				var url = "<?php echo base_url('home/reg') ?>";
				var data = $("form[name='reg']").serialize();
				$.ajax({
					type : "post",
					url : url,
					data : data,
					dataType : 'json',
					success : function(res){
						if(res.success){
							alert(res.message);
							var url = "<?php echo base_url('home/index') ?>";
							window.location.href = url;
						}else{
							alert(res.message);
						}
					}
				})
				return false
			})
		});

	</script>
</div>
</body>
</html>
