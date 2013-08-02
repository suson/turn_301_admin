
<div class="datatable">
    <div class="tip">
        <div class="left">
            <a href="<?php echo base_url('admin/index') ?>">首页</a>&gt; 
            <span>用户管理</span>

        </div>
        <div class="right">
        </div>
    </div>
    <div class="head">
        <div class="left">

        </div>
        <div class="left">
            <form class="form-search" action="<?php echo base_url('admin/users') ?>" method="post">
                <div class="input-append">
                    <input type="text" class="span10 search-query" name="search" placeholder="请输入用户名">
                    <button type="submit" class="button">搜索</button>
                </div>

            </form>
        </div>
    </div>
    <div class="body">
        <table width="200" border="0" class="table bordered zebra">
            <thead><tr>
                    <th>
                        <label class="all checkbox">
                            <input type="checkbox" class="all" value="">全选
                        </label>
                    </th>
                    <th>
                        用户名
                    </th>
                    <th>
                        用户等级
                    </th>
                    <th>
                        注册日期
                    </th>
                    <th>
                        上次登陆IP
                    </th>
                    <th>
                        上次登陆时间
                    </th>
                    <th>
                        账户余额
                    </th>
                </tr>
            </thead>
            <tbody class="tbody">
                <?php foreach ($list as $v): ?>
                    <tr>
                        <td>
                            <label class="all checkbox">
                                <input type="checkbox" name="ids[]" value="<?php echo $v['id'] ?>">
                                <?php echo $v['id'] ?>
                            </label>
                        </td>
                        <td>
                            <span class="text-success"><?php echo $v['username'] ?></span>
                        </td>
                        <td>
                            <?php echo $this->common->get_role_name($v['role']) ?>
                        </td>
                        <td>
                            <?php echo date('Y年m月d日', strtotime($v['regtime'])) ?>
                        </td>
                        <td>
                            <?php if ($v['lastloginip'] != ''): ?>
                                <?php echo $v['lastloginip'] ?>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>

                        </td>
                        <td>
                            <?php if ($v['lastlogintime'] == '0000-00-00 00:00:00'): ?>
                                &nbsp;
                            <?php else: ?>
                                <?php echo $v['lastlogintime'] ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($v['role'] == 1): ?><?php echo $v['overage'] ?>元<?php else: ?>&nbsp;<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody></table>
    </div>
    <div class="foot">
        <div class="left">
            <span class="">批量操作（对已选用户）：</span>
            <span ><a href="javascript:void(0);" onclick="javascript:setrole('some', 1);" >设置为高级用户</a></span>
            <span class="mleft20"><a href="javascript:void(0);" onclick="javascript:setrole('some', 0);" >设置为普通用户</a></span>
            <?php if ($roleid == 2): ?><span class="mleft20"><a href="#showoverage" rel="popup" >充值</a></span><?php endif; ?>
            <span class="mleft20"><a href="javascript:void(0);" onclick="javascript:resetpass();" >重置密码</a></span>
        </div>
        <div class="right">

            <div class="pager">
                <?php echo $page ?>
            </div>

        </div>
    </div>
</div>





<!-- 接framer的div -->
</div>
<!-- 接framer的div -->
<div class="popup" id="showoverage" style="display:none;z-index: 101;">
    <div class="head">
        充值
        <del>×</del>
    </div>
    <div class="body">
        <form>
            <p>
                充值金额：<input name="overage" id="overage" type="text"> <span class="red">*</span>请输入1或1以上的数值
            </p>
            <p>
                <input name="" id="saveoverage" type="submit" value="确定" class="button blue">
            </p>
        </form>
    </div>
</div>


<script type="text/javascript">
                $(document).ready(function() {

                    $(".tbody tr:even").css("background", "rgb(237, 248, 243)").attr('tag',1);
                    $(".tbody tr:odd").css("background", "rgb(248, 248, 248)").attr('tag',2);
                    $(".tbody tr").mouseover(function() {
                        $(this).css("background", "rgb(164, 221, 195)");
                    }).mouseout(function() {
                        if($(this).attr('tag') == 1){
                            $(this).css("background", "rgb(237, 248, 243)");
                        }else{
                            $(this).css("background", "rgb(248, 248, 248)");
                        }
                    }); 
                    $(":checkbox.all").bind("change", function() {
                        var flag = this.checked;
                        $(".all:checkbox").each(function() {
                            this.checked = flag;
                        })
                        $("[name='ids[]']:checkbox").each(function() {
                            this.checked = flag;
                        });

                    })
                    $("#saveoverage").bind("click", function() {
                        var overage = $.trim($("#overage").val());
                        if (overage == '' || isNaN(overage) || overage < 1) {
                            alert('请输入1或1以上的数值！');
                            return false;
                        }

                        var id = [];
                        $("[name='ids[]']:checked").each(function() {
                            id.push($(this).val());
                        });
                        if (id == '') {
                            alert('请至少选择一个选项！');
                            return false;
                        }
                        var data = "id=" + id + "&price=" + overage;
                        var url = "<?php echo base_url('admin/users/setoverage') ?>";
                        //alert(id);return false;
                        $.ajax({
                            type: "post",
                            url: url,
                            data: data,
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    var url = "<?php echo current_url() ?>";

                                    window.location.href = url;
                                } else {
                                    alert(res.message);
                                }
                            }
                        })
                        return false;
                    }
                    )
                });

                function resetpass() {
                    var id = [];
                    $("[name='ids[]']:checked").each(function() {
                        id.push($(this).val());
                    });
                    if (id == '') {
                        alert('请至少选择一个选项！');
                        return false;
                    }
                    var data = "id=" + id;
                    var url = "<?php echo base_url('admin/users/resetpass') ?>";
                    $.ajax({
                        type: "post",
                        url: url,
                        data: data,
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                var url = "<?php echo current_url() ?>";
                                alert(res.message);
                                window.location.href = url;
                            } else {
                                alert(res.message);
                            }
                        }
                    })
                }

                function setrole(id, role) {
                    var url = "<?php echo base_url('admin/users/setrole') ?>";
                    if (id === 'some') {
                        id = [];
                        $("[name='ids[]']:checked").each(function() {
                            id.push($(this).val());
                        });

                    }
                    if (id == '') {
                        alert('请至少选择一个选项！');
                        return false;
                    }
                    var data = "id=" + id + "&role=" + role;
                    //alert(id);return false;
                    $.ajax({
                        type: "post",
                        url: url,
                        data: data,
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                var url = "<?php echo base_url('admin/users') ?>";
                                window.location.href = url;
                            } else {
                                alert(res.message);
                            }
                        }
                    })
                }

</script>