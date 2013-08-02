
<div class="datatable">
    <div class="tip">
        <div class="left">
            <a href="<?php echo base_url('admin/index') ?>">首页</a>&gt; 
            <span>网址管理</span>

        </div>
        <div class="right">
        </div>
    </div>
    <div class="head">
        <div class="left">
            <span class=""><a class="button blue" rel="popup" id="a_addurl" href="#url_add_dialog">添加网址</a></span>
            <span class="mleft20">当前在线：<span class="green"><?php echo $currentcount ?></span></span>
            <span class="mleft20">今日在线：<span class="green"><?php echo $todaycount ?></span></span>
			<span class="mleft20">今日开通：<span class="green"><?php echo $todayip ?>IP</span></span>
            <span class="mleft20">网站总数：<span class="green"><?php echo $count ?></span></span>
            <span class="mleft20"><a class="button blue" onclick="importdata();" href="javascript:void(0)">批量添加网址</a></span>
        </div>
        <div class="right">
            <form class="form-search" action="<?php echo base_url('admin/url') ?>" method="post">
                <div class="input-append">
                    <input type="text" class="span10 search-query" name="search" placeholder="请输入网址/网站名称">
                    <button type="submit" class="button blue">搜索</button>
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
                        网址编号
                    </th>
                    <th>
                        网站名称
                    </th>
                    <th>
                        我的网址
                    </th>
                    <th>
                        当前状态
                    </th>

                    <th>
                        今日/累计分享(<?php echo $today_all_sum; ?>)
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
            </thead>
            <tbody class="tbody">
                <?php foreach ($list as $v): ?>
                    <tr>
                        <td>
                            <label class="all checkbox">
                                <input type="checkbox" name="ids[]" value="<?php echo $v['id'] ?>">
                                <?php if ($v['meal'] == 0): ?>
                                    <span class="muted">免费</span>
                                <?php else: ?>
                                    <span style="color:rgb(179, 75, 75);"><?php echo $v['meal'] ?>IP</span>
                                <?php endif; ?>
                            </label>
                        </td>
                        <td>
                            <span class="text-success"><?php echo $v['urlno'] ?></span>
                        </td>
                        <td>
                            <?php if ($v['urlname'] != ''): ?>
                                <span title="<?php echo $v['urlname'] ?>"><?php echo $this->common->cutstr($v['urlname'], 25) ?></span>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td>
                            <span title="<?php echo $v['urlpath'] ?>"><?php echo $this->common->cutstr($v['urlpath'], 20) ?></span><small><a href="javascript:edit(<?php echo $v['id'] ?>,'<?php echo $v["urlname"] ?>','<?php echo $v["urlpath"] ?>','<?php echo $v['frompath'] ?>')">修改</a></small>
                        </td>
                        <td>
                            <?php if ($v['status'] == 1001 || strtotime($v['endtime']) <= time() ): ?>
                                <span class="muted">离线</span>
                            <?php elseif ($v['status'] == 1011): ?>
                                <span class="muted">停止</span><small><a href="javascript:share(<?php echo $v['id'] ?>,'start')">开始分享</a></small>
                            <?php else: ?>
                                <span class="green">在线</span><small><a href="javascript:share(<?php echo $v['id'] ?>,'stop')">停止分享</a></small>
                            <?php endif; ?>

                        </td>
                        <td>
                            <?php echo $v['todaytimes'] . "/" . $v['sumtimes'] ?>
                        </td>
                        <td>
                            <?php if ($v['status'] == 1001 && strtotime($v['endtime']) > time()): ?>
                                <span class="text-info">
                                    <?php if ($v['uid'] == $uid): ?>
                                        <a href="javascript:openurl(<?php echo $v['id'] ?>,'<?php echo $v["urlname"] ?>','<?php echo $v["urlno"] ?>');">立即开通</a>
                                    <?php else: ?>
                                        <?php echo $this->common->get_username($v['uid']) ?>
                                    <?php endif; ?>
                                </span>
                            <?php elseif (strtotime($v['endtime']) <= time()): ?>
                                <span class="text-error">已过期</span>
                                <span class="text-info">
                                    <?php if ($v['uid'] == $uid): ?>
                                        <a href="javascript:openurl(<?php echo $v['id'] ?>,'<?php echo $v["urlname"] ?>','<?php echo $v["urlno"] ?>');">重新开通</a>
                                    <?php else: ?>
                                        （<?php echo $this->common->get_username($v['uid']) ?>）
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-success">剩<?php echo round((strtotime($v['endtime']) - time()) / (24 * 60 * 60), 2) ?>天
                                    <?php if ($v['uid'] != $uid): ?>
                                        （<?php echo $this->common->get_username($v['uid']) ?>）
                                    <?php endif; ?>
                                    <?php if ($v['uid'] == $uid && (ceil((strtotime($v['endtime']) - time()) / (24 * 60 * 60))) > 1): ?>
                                        <a href="javascript:closemeal(<?php echo $v['id'] ?>);">退订</a>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody></table>
    </div>
    <div class="foot">
        <div class="left">
            批量操作（对已选网址）：
            <span ><a href="javascript:void(0);" onclick="javascript:share('some', 'start');" >开启分享</a></span>
            <span class="mleft20"><a href="javascript:void(0);" onclick="javascript:share('some', 'stop');" >停止分享</a></span>
            <span class="mleft20"><a href="javascript:void(0);" onclick="javascript:del('some');" >删除网址</a></span>
            <span class="mleft20"><a href="#url_meal_dialog" rel="popup" >开通套餐</a></span>
            <span class="mleft20"><a href="javascript:void(0);" onclick="closemeal('some');" >退订</a></span>
        </div>
        <div class="right">

            <div class="pager">
                <?php echo $page ?>
            </div>

        </div>
    </div>
</div>

<!-- dialog -->
<div class="popup" id="url_meal_dialog" style="display:none;z-index: 101;">
    <div class="head">
        开通套餐
        <del>×</del>
    </div>
    <div class="body">
        <form name="url_meal">
            <p>
                <label for="meal" >请输入代挂流量：</label>
                <input type="text" class="span3" id="meal" name="meal">&nbsp;（请填写1或1以上整数）<span class="urlpatherror text-success"></span>
            </p>
            <p>
                <label for="servertimes" >请输入服务时间：</label>
                <input type="text" class="span2" id="servertimes" name="servertimes">&nbsp;天（范围1-365天）<span class="urlnameerror text-success"></span>
            </p>
            <p>
                <button type="submit" class="button blue" id="savemeal">确认开通</button>
            </p>
            <input type="hidden" name="urlid" id="id_for_meal" value="0">
        </form>

    </div>
</div>
<div class="popup" id="url_import_dialog" style="display:none;z-index: 101;">
    <div class="head">
        导入网址
        <del>×</del>
    </div>
    <div class="body" id="url_import_dialog_body">

    </div>
</div>
<div class="popup" id="url_add_dialog" style="display:none;z-index: 101;">
    <div class="head">
        <span class="addtag">添加</span>网址
        <del>×</del>
    </div>
    <div class="body">
        <form class="form-horizontal" name="url">


            <p>
                <label class="control-label" for="urlpath" contenteditable="true">我的网址:</label>
                <input name="urlpath" id="urlpath" type="text"><span class="urlpatherror"></span>
            </p>
            <p>
                <label class="control-label" for="urlpath" contenteditable="true">网站名称：</label>
                <input name="urlname" id="urlname" type="text"><span class="urlnameerror"></span>
            </p>
            <p>
                <label class="controls checkbox">
                    <input type="checkbox" name="from_on"> 启用来源网址
                </label>
            </p>

            <p id="from" style="display: none;">
                <label for="frompath">
                    添加来源
                </label>
                <input type="text" id="frompath" name="frompath">&nbsp;<input type="text" style="width:25px;" value="100" disabled id="fromscale" name="fromscale">%
            </p>
            <p>
                <input type="hidden" name="urlid" id="urlid" value="0" />
                <button type="submit" class="button blue" id="saveurl" style="width: 200px;" contenteditable="true">填写完毕，立即<span class="addtag">添加</span></button>
            </p>
        </form>
    </div>

</div>
<!-- dialog -->

<!-- 接framer的div -->
</div>
<!-- 接framer的div -->
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
                    $(":checkbox[name='from_on']").bind("change", function() {
                        if (this.checked) {
                            $("#from").show();
                        } else {
                            $("#from").hide();
                        }
                    })
					$("#url_add_dialog del").bind("click",function(){
						
						$("#urlpath").val('');
						$("#urlname").val('');
						$("#url_add_dialog #urlid").val('0');
						$(".addtag").html("添加");
						$(".urlpatherror").html("");
						$(".urlnameerror").html("");
						var frompath = '';
						if (frompath != "") {
							$("[name='from_on']:checkbox").each(function() {
								this.checked = true;
							});
							$("#frompath").val(frompath);
							$("#from").show();
						} else {
							$("[name='from_on']:checkbox").each(function() {
								this.checked = false;
							});
							$("#frompath").val('');
							$("#from").hide();
						}
					});
                    $("#urlpath").bind("blur", function() {

                        var url = $.trim($(this).val());
                        var tomatch = /(https|http):\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/;
                        if (tomatch.test(url)) {
                            $(".urlpatherror").removeClass('red green').addClass('green').html("网址有效");
                        } else {
                            $(".urlpatherror").removeClass('red green').addClass('red').html("网址无效");
                        }

                    })
                    $("#urlname").bind("blur", function() {

                        var name = $.trim($(this).val());
                        if (name != '') {
                            $(".urlnameerror").removeClass('red green').addClass('green').html("网站名称有效");
                        } else {
                            $(".urlnameerror").removeClass('red green').addClass('red').html("网站名称无效");
                        }

                    })

                    $("#b_meal").bind("blur", function() {
                        var val = parseInt($(this).val()) * 100;
                        $("#mealspan").val(val);
                        $("#meal").val(val);
                    })
                    $("#saveurl").bind("click", function() {
                        var urlpath = $.trim($("#urlpath").val());
                        var urlname = $.trim($("#urlname").val());
                        var frompath = $.trim($("#frompath").val());
                        if (urlpath == '') {
                            alert('我的网址不能为空！');
                            return false;
                        }
                        if (urlname == '') {
                            alert('网站名称不能为空！');
                            return false;
                        }
                        var tomatch = /(https|http):\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/;
                        if (tomatch.test(urlpath)) {
                            $(".urlpatherror").removeClass('text-success text-error').addClass('text-success').html("网址有效");
                        } else {
                            $(".urlpatherror").removeClass('text-success text-error').addClass('text-error').html("网址无效");
                            return false;
                        }
                        if ($(":checkbox[name='from_on']").is(":checked")) {
                            if (!tomatch.test(frompath)) {
                                alert('来源网址称格式不正确！');
                                return false;
                            }
                        }
                        var data = $("form[name='url']").serialize();
                        var url = "<?php echo base_url('admin/url/add') ?>";
                        $.ajax({
                            type: "post",
                            url: url,
                            data: data,
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    alert(res.message);
                                    var url = "<?php echo base_url('admin/url') ?>";
                                    window.location.href = url;
                                } else {
                                    alert(res.message);
                                }
                            }
                        })
                        return false;
                    });
                    $("#savemeal").bind("click", function() {
                        var meal = $.trim($("#meal").val());
                        var servertimes = $.trim($("#servertimes").val());
                        if (meal == '' || isNaN(meal) || meal < 1 || meal % 1 != 0) {
                            alert('代挂流量请输入1或1以上的整数倍值！');
                            return false;
                        }
                        if (servertimes == '' || isNaN(servertimes) || servertimes > 365 || servertimes < 1) {
                            alert('服务时间请输入1到365之间的数值！');
                            return false;
                        }
                        var id = [];
                        $("[name='ids[]']:checked").each(function() {
                            id.push($(this).val());
                        });
                        $("#url_meal_dialog #id_for_meal").val(id);
                        var data = $("form[name='url_meal']").serialize();
                        var url = "<?php echo base_url('admin/url/open') ?>";
                        $.ajax({
                            type: "post",
                            url: url,
                            data: data,
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    alert(res.message);
                                    var url = "<?php echo current_url() ?>";
                                    window.location.href = url;
                                } else {
                                    alert(res.message);
                                }
                            }
                        })
                        return false;
                    });
                })

                function openurl(id, name, no) {
                    $("input[name='ids[]']").attr("checked", false);
                    $("input[name='ids[]'][value='" + id + "']").attr("checked", true);
                    $("#urlname_for_meal").html(name);
                    $("#urlno").html(no);
                    $("a[href='#url_meal_dialog']").trigger("click");
                }

                function share(id, type) {

                    var url = "<?php echo base_url('admin/url/stopshare') ?>";
                    if (type == "start")
                        url = "<?php echo base_url('admin/url/startshare') ?>";
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
                    var data = "id=" + id;
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
                }

                function del(id) {
                    var url = "<?php echo base_url('admin/url/del') ?>";
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
                    var data = "id=" + id;
                    //alert(id);return false;
                    $.ajax({
                        type: "post",
                        url: url,
                        data: data,
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                var url = "<?php echo base_url('admin/url') ?>";
                                window.location.href = url;
                            } else {
                                alert(res.message);
                            }
                        }
                    })
                }

                function edit(id, name, path, frompath) {
                    $("#urlpath").val(path);
                    $("#urlname").val(name);
                    $("#url_add_dialog #urlid").val(id);
                    $(".addtag").html("修改");
                    $(".urlpatherror").html("");
                    $(".urlnameerror").html("");
                    if (frompath != "") {
                        $("[name='from_on']:checkbox").each(function() {
                            this.checked = true;
                        });
                        $("#frompath").val(frompath);
                        $("#from").show();
                    } else {
						$("#frompath").val('');
                        $("#from").hide();
                    }
                    $('#a_addurl').trigger('click');
                }

                function closemeal(id) {
                    var question = confirm("退订将返还余额，确定要退订吗?");
                    if (question) {
                        var url = "<?php echo base_url('admin/url/close') ?>";
                        if (id == "some") {
                            id = [];
                            $("[name='ids[]']:checked").each(function() {
                                id.push($(this).val());
                            });
                        }
                        if (id == '') {
                            alert('请至少选择一个选项！');
                            return false;
                        }
                        var data = "id=" + id;
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
                    }

                }

                function importdata() {
                    var url = "<?php echo base_url('upload') ?>";
                    $.get(url, function(res) {
                        $("#url_import_dialog_body").html(res);
                    });
                    $('body').prepend('<div id="mask"></div>').find('#mask').css({
                        opacity: 0.5,
                        cursor: 'pointer',
                        background: 'black',
                        position: 'fixed',
                        zIndex: 100,
                        width: '100%',
                        height: '100%'
                    });
                    $("#url_import_dialog").fadeIn();
                }






</script>
