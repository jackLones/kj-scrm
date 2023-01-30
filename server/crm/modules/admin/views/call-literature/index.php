<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
$this->title = '客户资料审核列表-外呼系统';
?>

<?=Html::cssFile('@web/css/dataTable.css')?>
<?=Html::cssFile('@web/plugins/dataTables/dataTables.bootstrap.css')?>
<?=Html::cssFile('@web/plugins/chosen/chosen.css')?>
<?=Html::jsFile('@web/plugins/chosen/chosen.jquery.js')?>
<?=Html::jsFile('@web/js/moment.min.js')?>
<?=Html::jsFile('@web/plugins/daterangepicker/daterangepicker.js')?>
<?=Html::cssFile('@web/plugins/daterangepicker/daterangepicker-bs3.css')?>
<?=Html::jsFile('@web/plugins/layer/layer.js')?>

<style>
    .chosen-container-single .chosen-single {background: #fff;border-radius: 0;border-color: #e5e6e7;height: 34px !important;line-height: 34px;box-shadow: 0 0 0 white inset, 0 1px 1px rgba(0, 0, 0, 0);}
    .chosen-container-single .chosen-single div b {background-position-y: 7px;}
    .middleLine {
        display: inline-block;
        width: 15px;
        border-top: 1px solid #999;
        vertical-align : middle;
    }
    .float-e-margins .btn {
        margin-bottom:0;
    }

    .pagination {
        float:right;
        margin:20px;
    }

    #section-title:before{
        content: '#';
        display: inline-block;
        width: 2px;
        border: 1px solid #0992ff;
        color: transparent;
    }

    .upload-box{
        width: 100px;
        height:  100px;
        border: 1px solid #c3c3c3;
        cursor: pointer;
    }

    .papers-upload{
        display: flex;
        width: 150px;
        line-height: 100px;
        justify-content: center;
        align-items: center;
    }
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
                <strong>资料审核</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-group">
                        <form id="searchForm" method="get" action="/admin/call-literature/index">
                            <div class="row text-center" style="padding: 0 15px;">
                                <div class="row search_top" style="margin-bottom: 10px">
                                    <div class="col-lg-4">
                                        <select style="width: calc(40% - 23px);display: inline-block;" class="form-control select" name="status" >
                                            <option value="-1" <?= ($status == '-1')?'selected':'';?> >全部状态</option>
                                            <option value="2" <?= ($status == '2')?'selected':'';?> >审核中</option>
                                            <option value="0" <?= ($status == '0')?'selected':'';?> >审核拒绝</option>
                                        </select>
                                        <input type="text" value="<?= $keywords;?>" name="keywords" class="input form-control" placeholder="输入客户账号/公司查询" id="keywords" style="width: 60%; display: inline-block;">
                                    </div>

                                    <div class="col-sm-3 m-b-sm">
                                        <div class="m-b-sm text-left">
                                            <div class="input-group m-t-xs" style="width:100%">
                                                <a class="btn btn-primary btn-sm" style="margin-bottom:0; padding: 7px 16px;" href="javascript:search()">查 询</a>&nbsp;
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="js-real-time-region realtime-list-box loading">
                            <div class="widget-list">
                                <div style="position: relative;"
                                     class="js-list-filter-region clearfix ui-box">
                                    <div class="widget-list-filter"></div>
                                </div>
                                <div class="ui-box">
                                    <table style="padding: 0px;font-size: 13px;" data-page-size="20" class="ui-table ui-table-list default no-paging footable-loaded footable">
                                        <thead class="js-list-header-region tableFloatingHeaderOriginal">
                                        <tr class="widget-list-header">
                                            <th width="20%">客户账号</th>
                                            <th width="20%">企业微信</th>
                                            <th width="20%">审核状态</th>
                                            <th>提交时间</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-list-body" class="js-list-body-region">
                                        <?php if (!empty($dialoutConfigs)) { ?>
                                            <?php foreach($dialoutConfigs as $dialoutConfig){ ?>
                                                <tr class="widget-list-item">
                                                    <td><?=\app\util\SUtils::hideString($dialoutConfig->user->account)?></td>
                                                    <td><?=\app\util\SUtils::deepHideString($dialoutConfig->workCorp->corp_name)?></td>
                                                    <td class="<?=$dialoutConfig->status == 2 ? 'text-warning' : ($dialoutConfig->status == 1 ? 'text-success' : 'text-danger')?>">
                                                        <?=$dialoutConfig->status == 2 ? '服务商审核中' : ($dialoutConfig->status == 1 ? '审核通过' : '审核拒绝')?>
                                                        <?php if($dialoutConfig->status == 0){ ?>
                                                            <span class="glyphicon glyphicon-question-sign"></span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?=$dialoutConfig->create_time?></td>
                                                    <td>
                                                        <?php if($dialoutConfig->status == 0) { ?>
                                                            <a class="btn btn-primary" onclick="resubmit(<?=$dialoutConfig->id?>)">重新提审</a>
                                                        <?php }else{ ?>
                                                            --
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <tr class="widget-list-item"><td colspan="15">暂无外呼开通资料审核信息</td></tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox-footer">
                    <div class="row">
                        <?php
                            echo LinkPager::widget([
                                'pagination' => $pages,
                            ]);
                        ?>
                        <div class="col-sm-10"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade inmodal" tabindex="-1" id="callOpenModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close _close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">重新提审</h4>
            </div>
            <div class="modal-body">
                <div class="data-box">
                    <form role="form">
                        <div class="row">
                            <span style="margin-left: 10px;" class="h5 red">
                                审核失败！ 您所提交的资料法人身份证没有反面，且行业存在风险需要提交承诺函，请下载承诺函填写后提交
                            </span>
                        </div>
                        <div class="row h5" style="margin-top:10px;">
                            <a href="<?=\Yii::getAlias('@web')?>/file/acknowledgement.docx" download="承诺函.docx">
                                <span class="glyphicon glyphicon-download" aria-hidden="true"></span> 承诺函下载
                            </a>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>营业执照:</label>
                            <div class="upload-box col-md-9 papers-upload">
                                <img src="" class="hide image img-responsive">
                                <span class="glyphicon glyphicon-plus boot-icon h2"></span>
                                <input type="file" class="upload-file hide" name="business_license_url" accept="image/*" onchange="fileUploaded(this)">
                            </div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>法人身份证:</label>
                            <div class="upload-box col-md-4 papers-upload">
                                <img src="" class="hide image img-responsive">
                                <span class="boot-icon h5">
                                    正面
                                </span>
                                <input type="file" class="upload-file hide" name="corporate_identity_card_positive_url" accept="image/*" onchange="fileUploaded(this)">
                            </div>
                            <div class="upload-box col-md-4 papers-upload" style="margin-left: 10px;">
                                <img src="" class="hide image img-responsive">
                                <span class="boot-icon h5">
                                    反面
                                </span>
                                <input type="file" class="upload-file hide" name="corporate_identity_card_reverse_url" accept="image/*" onchange="fileUploaded(this)">
                            </div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;">经办人身份证:</label>
                            <div class="upload-box col-md-4 papers-upload">
                                <img src="" class="hide image img-responsive">
                                <span class="boot-icon h5">
                                    正面
                                </span>
                                <input type="file" class="upload-file hide" name="operator_identity_card_positive_url" accept="image/*" onchange="fileUploaded(this)">
                            </div>
                            <div class="upload-box col-md-4 papers-upload" style="margin-left: 10px;">
                                <img src="" class="hide image img-responsive">
                                <span class="boot-icon h5">
                                    反面
                                </span>
                                <input type="file" class="upload-file hide" name="operator_identity_card_reverse_url" accept="image/*" onchange="fileUploaded(this)">
                            </div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>客户话术:</label>
                            <div class="col-md-8" style="padding-left: 0px;">
                                <textarea class="form-control" name="customer_words_art" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>号码属性:</label>
                            <div class="col-md-8" style="padding-left: 0px;">
                                <input type="text" class="form-control" name="number_attribute" placeholder="如：需要开通合肥地区的8个手机号和4个固定号码">
                            </div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;">承诺函:</label>
                            <div class="col-md-8" style="padding-left: 0px;">
                                <input type="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document" name="acknowledgement_url" onchange="fileChange(this)">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm" onclick="resubmitConfirm()">确定</button>
                <button type="button" class="btn btn-white _close">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
    function search(){
        $("#searchForm").submit();
    }

    var resubmitLiterature = null

    function resubmit(id){
        $.ajax({
            url    : "/admin/call-literature/read",
            type   : "GET",
            async  : false,
            data   : {id: id},
            dataType: 'json',
            success: function (res) {
                var data = res.data;
                resubmitLiterature = data;

                ([
                    'business_license_url',
                    'corporate_identity_card_positive_url',
                    'corporate_identity_card_reverse_url',
                    'operator_identity_card_positive_url',
                    'operator_identity_card_reverse_url'
                ]).forEach(function(inputName){
                    var imgUrl = data[inputName] || ''

                    if(imgUrl){
                        $('input[name="'+inputName+'"]').siblings('.image').attr('src', imgUrl).removeClass('hide').siblings('.boot-icon').addClass('hide')
                    }
                    $('textarea[name="customer_words_art"]').val(data.customer_words_art || '');
                    $('input[name="number_attribute"]').val(data.number_attribute || '');
                })

                $("#callOpenModal").modal('show');
            }
        })
    }

    function fileChange(fileEl){
        console.log(fileEl.files)
    }

    function resubmitConfirm(){
        var data = {
            uid: resubmitLiterature.uid,
            corp_id: resubmitLiterature.corp_id,
            customer_words_art: $("textarea[name='customer_words_art']").val(),
            number_attribute: $("input[name='number_attribute']").val()
        };

        [
            'business_license_url',
            'corporate_identity_card_positive_url',
            'corporate_identity_card_reverse_url',
            'operator_identity_card_positive_url',
            'operator_identity_card_reverse_url',
            'acknowledgement_url'
        ].forEach(function(name){
            var file = $("input[name='" + name +"']")[0].files[0] || null
            if(file) data[name] = file
        });

        if(! data.business_license_url && ! resubmitLiterature.business_license_url) return swal("温馨提示!", "请上传营业执照", "error")
        if(! data.corporate_identity_card_positive_url && ! resubmitLiterature.corporate_identity_card_positive_url) return swal("温馨提示!", "请上传法人身份证正面照片", "error")
        if(! data.corporate_identity_card_reverse_url && ! resubmitLiterature.corporate_identity_card_reverse_url) return swal("温馨提示!", "请上传法人身份证反面照片", "error")
        if(! data.customer_words_art) return swal("温馨提示!", "请输入客户话术", "error")
        if(! data.number_attribute) return swal("温馨提示!", "请输入号码属性", "error")

        var formData = new FormData();

        Object.keys(data).forEach(function (key) {
            if(data[key]) formData.append(key, data[key])
        })

        $.ajax({
            url: '<?=\yii\helpers\Url::to('/admin/call/resubmit-dialout-config')?>',
            type: "POST",
            async: false,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(res){
                if(res.error == 1) {
                    return swal("温馨提示", res.msg, "error");
                }
                swal('温馨提示', '您的开通信息已经提交给服务商审核, 请等待', 'success')
            }
        })
    }

    $(function(){
        $("#callOpenModal ._close").on('click', function () {
            $("#callOpenModal").modal('hide');
        });
    })
</script>


