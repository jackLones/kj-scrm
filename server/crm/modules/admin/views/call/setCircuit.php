<?php
    $this->title = '设置线路';
?>

<style>

</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>管理后台</h2>
        <ol class="breadcrumb">
            <li>
                <a>系统</a>
            </li>
            <li>
                <a>外呼系统</a>
            </li>
            <li class="active">
                <strong>设置线路</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="set-circult-box">
                <div class="text-center h2" style="color:#000; margin-top: 30px;">日思夜想云呼叫系统</div>
                <div class="text-center" style="margin-top: 30px;">无需硬件部署即可快速搭建外呼系统，实现在线呼叫、通话语音留存、智能数据分析。帮助企业精准定位意向客户，扩展企业营收利器</div>
                <div class="center-block" style="width: 500px; margin-top:80px;">
                    <div class="row">
                        <div class="col-md-9">
                            <input type="text" value="<?=$api_key?>" class="form-control api-key-form" placeholder="请输入对接KEY">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" id="openButton">立即开通</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-left" style="color:red; margin-top:10px;">
                            请联系您的渠道经理获取对接key值
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#openButton').click(function(){
            var apiKey = $('.api-key-form').val()

            if(! apiKey){
                return swal("温馨提示", '请填写对接KEY！', "error");
            }

            $.ajax({
                url: '<?=\yii\helpers\Url::to('/admin/call/set-circuit')?>',
                type: "post",
                data: {
                    api_type: '7moor',
                    api_key: apiKey
                },
                dataType: "JSON",
                success: function (res) {
                    if(res.error == 1) {
                        return swal("温馨提示", res.msg, "error");
                    }

                    swal({title:"温馨提示", text:"保存成功", type:"success"}, function(){
                        window.location.href = '<?=\yii\helpers\Url::to('/admin/call-customer/index')?>'
                    })
                }
            });
        })
    })
</script>