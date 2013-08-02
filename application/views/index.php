<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=7">
        <title>后台管理中心</title>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="stylesheet" href="<?php echo base_url() ?>public/css/cutter.css"> 
        <script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url() ?>public/js/cutter.js"></script>

    </head>

    <body class="usa">

        <div class="">

            
            <div class="wrapper">


                

                <div class="current">
                    <span  class="">公告：</span><small class="green">更新通知</small>

                </div>
                <div class="topbar">
                    <span style="float:left;">欢迎光临在线服务中心</span>
                    <div class="module" style="">
                        <ul>
                            <li>
                                <strong><a href="<?php echo base_url('admin/index') ?>">首页</a></strong>                </li>
                            <?php if ($roleid > 0): ?>
                                <li class="">
                                    <strong><a href="<?php echo base_url('admin/url') ?>" <?php if($currenttag == 'url'):?>class="selected"<?php endif; ?>>网址管理</a></strong>                </li>
                                <?php endif; ?>
                            <li class="">
                                <strong><a href="<?php echo base_url('admin/updatepass') ?>" <?php if($currenttag == 'updatepass'):?>class="selected"<?php endif; ?>>修改密码</a></strong>                </li>
                            <?php if ($roleid > 1): ?>
                                <li class="">
                                    <strong><a href="<?php echo base_url('admin/users') ?>" <?php if($currenttag == 'users'):?>class="selected"<?php endif; ?>>用户管理</a></strong>                </li>
                            <?php endif; ?>
                                <span  class="mleft20">用户等级：</span><span  class="green"><?php echo $role ?></span>
                    <span  class="mleft20">用户名：</span><span  class="green"><?php echo $name ?></span>
                    <?php if ($roleid < 2): ?>
                        <span  class="mleft20">当前余额：</span><span  class="red"><?php echo $this->common->get_overage() ?></span>
                    <?php endif; ?>
                    <span  class="mleft20" ><a href="<?php echo base_url('home/logout') ?>">退出登陆</a></span>
                        </ul>
                        
                    </div>
                </div>
                <?php echo $content ?>
            </div>
            <!--        <div id="mask" style="opacity: 0.2; cursor: pointer; background-color: black; position: absolute; z-index: 100; width: 100%; height: 3074px; background-position: initial initial; background-repeat: initial initial;"></div>-->
    </body></html>