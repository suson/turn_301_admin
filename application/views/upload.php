

<?php echo $error;?>

<form  target="fileup" action="<?php echo base_url('upload/do_upload') ?>" method="post" accept-charset="utf-8" name="upload" enctype="multipart/form-data">
<p>
<input type="file" name="userfile" size="20" />
</p>
<br />
<?php if ($roleid == 2): ?>
<p>
<input type="submit" class="button" id="savefile" value="上传" />文件类型为txt,编码为ANSI(默认新建文本文档即为该类型),网站名称、网址、来源网址、套餐数量、开通天数用空格隔开多个换行.<a target="target" href="<?php echo base_url().'public/superdemo.txt' ?>">Demo示例</a>
</p>
<?php else: ?>
<p>
<input type="submit" class="button" id="savefile" value="上传" />文件类型为txt,编码为ANSI(默认新建文本文档即为该类型),网站名称、网址、来源网址用空格隔开多个换行.<a target="target" href="<?php echo base_url().'public/demo.txt' ?>">Demo示例</a>
</p>
<?php endif; ?>

</form><iframe width="0px" height="0px" style="display:none;" name="fileup"></iframe> 
<script>
    $(document).ready(function(){
        $("#savefile").bind("click",function(){
            $("form[name='upload']").submit();
            return false;
        })
    })
</script>
