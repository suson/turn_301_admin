<div class="datatable">
    <div class="tip">
        <div class="left">
            <a href="<?php echo base_url('admin/index') ?>">首页</a>&gt; 
            <span>修改密码</span>

        </div>
        <div class="right">
        </div>
    </div>
    <div class="head">
        <div class="left">

        </div>
        <div class="right">
            
        </div>
    </div>
    <div class="head">
        <div class="">
        <form name="updateuser" class="mleft20">
            <p>
                <label for="oldpass">旧密码</label>
                    <input type="password" id="oldpass" name="oldpass" /><small><span class="red">*</span>
                    </small>
            </p>
            <p>
                <label for="newpass">新密码</label>
                    <input type="password" id="newpass" name="newpass" /><small><span class="red">*</span>
                        <span>(6-20)密码为字母与数字、特殊字符等组合</span></small>
            </p>
            <p>
                <label for="newppass">再次输入新密码</label>
                    <input type="password" id="newppass" name="newppass" /><small><span class="red">*</span>
                        <span  class="muted">两次密码必须一致</span></small>
            </p>
            <p>
                    <button type="submit" class="button" id="updateuser">提交</button>
            </p>
        </form>
        </div>
    </div>
    <div class="foot">

    </div>
</div>
<!-- 接framer的div -->
</div>
<!-- 接framer的div -->
<script type="text/javascript">
    $(document).ready(function(){
        $("#updateuser").bind("click",function(){
            var oldpass = $.trim($("#oldpass").val());
            var newpass = $.trim($("#newpass").val());
            var newppass = $.trim($("#newppass").val());
						
            if(oldpass == ''){
                alert('旧密码不能为空！');
                return false;
            }
            if(newpass == ''){
                alert('新密码不能为空！');
                return false;
            }
            if(newppass != newpass){
                alert('再次输入新密码不一致');
                return false;
            }
            if(newpass.length < 6 || newpass.length > 20){
                alert("新密码长度不符合！");
                return false;
            }
            var url = "<?php echo base_url('admin/updatepass') ?>";
            var data = $("form[name='updateuser']").serialize();
						
            $.ajax({
                type : "post",
                url : url,
                data : data,
                dataType : 'json',
                success : function(res){
                    if(res.success){
                        alert(res.message);
                        var url = "<?php echo base_url('admin/index') ?>";
                        window.location.href = url;
                    }else{
                        alert(res.message);
                    }
                }
            })
            return false
        })
    })
</script>