<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '客户列表-外呼系统';
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
                <strong>客户列表</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>
<div class="wrapper wrapper-content">
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-group">
                        <form id="searchForm" method="get" action="/admin/call-customer/index">
                            <div class="row text-center" style="padding: 0 15px;">
                                <div class="row search_top" style="margin-bottom: 10px">
                                    <div class="col-lg-3">
                                        <input type="text" value="<?= $keywords;?>" name="keywords" class="input form-control" placeholder="输入客户账号/公司查询" id="keywords">
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
                                            <th>客户账号</th>
                                            <th>公司名称</th>
                                            <th>企业微信</th>
                                            <th>扣费标准</th>
                                            <th>账户累计消耗</th>
                                            <th>账户余额</th>
                                            <th>剩余坐席/已用坐席/开通坐席</th>
                                            <th>坐席消耗</th>
                                            <th>通话分钟总数</th>
                                            <th>话费消耗</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-list-body" class="js-list-body-region">
                                        <?php if (!empty($customers)) { ?>
                                            <?php foreach($customers as $customer){ ?>
                                                <tr class="widget-list-item">
                                                    <td><?=\app\util\SUtils::hideString($customer['account'])?></td>
                                                    <td><?=\app\util\SUtils::deepHideString($customer['corp_full_name'])?></td>
                                                    <td><?=\app\util\SUtils::deepHideString($customer['corp_name'])?></td>
                                                    <td>
                                                        <p>坐席(含月租): <?=$customer['exten_money'] ?: '--'?>/月</p>
                                                        <p>话费：<?=$customer['phone_money'] ?: '--'?>元/分钟</p>
                                                    </td>
                                                    <td><?=$customer['expend_money'] ?: 0?></td>
                                                    <td><?=$customer['balance']?></td>
                                                    <td><?=$customer['open_agent_num'] - $customer['used_agent_num']?>/<?=$customer['used_agent_num']?>/<?=$customer['open_agent_num']?></td>
                                                    <td><?=$customer['agent_expend_money'] ?: 0?></td>
                                                    <td><?=$customer['call_duration_num'] ?: 0?></td>
                                                    <td><?=$customer['call_expend_money'] ?: 0?></td>
                                                    <td>
                                                        <a class="btn btn-primary" onclick="showMore(<?=$customer['id'];?>)">查看更多</a>
                                                    </td>
                                                </tr>
                                                <tr class="more-btn more-btn-<?=$customer['id'];?>" style="display:none;">
                                                    <td colspan="16" style="text-align: right;">
                                                        <a class="btn btn-primary" onclick="openDialoutAgent(<?=$customer['id'];?>)" href="javascript:void(0);" style="width:8rem">开通坐席</a>
                                                        <a class="btn btn-primary" onclick="rechargeTelephone(<?=$customer['id'];?>, '<?=\app\util\SUtils::hideString($customer['account'], 3, 4, true)?>')" href="javascript:void(0);" style="width:8rem">话费充值</a>
                                                        <a class="btn btn-primary" onclick="openCall(<?=$customer['id'];?>)" href="javascript:void(0);" style="width:12rem">平台收益明细</a>
                                                        <a class="btn btn-primary" onclick="openCall(<?=$customer['id'];?>)" href="javascript:void(0);" style="width:8rem">坐席明细</a>
                                                        <a class="btn btn-primary" onclick="openCall(<?=$customer['id'];?>)" href="javascript:void(0);" style="width:8rem">账户明细</a>
                                                        <a class="btn btn-primary" onclick="accountSetting(<?=$customer['id'];?>)" href="javascript:void(0);" style="width:8rem">账户设置</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <tr class="widget-list-item text-center"><td colspan="15">暂无外呼开通客户</td></tr>
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

<div class="modal fade inmodal" tabindex="-1" id="accountSettingModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close _close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">账户设置</h4>
            </div>
            <div class="modal-body">
                <div class="data-box">
                    <form role="form">
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-4 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>设置坐席价格(含月租):</label>
                            <div class="col-md-5" style="padding-left: 0px;">
                                <input type="text" class="form-control" name="exten_money" placeholder="请输入坐席价格" style="display:inline-block">
                            </div>
                            <div class="col-md-2" style="line-height:34px;">元/月</div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-4 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>设置话费价格:</label>
                            <div class="col-md-5" style="padding-left: 0px;">
                                <input type="text" class="form-control" name="phone_money" placeholder="设置话费价格">
                            </div>
                            <div class="col-md-2" style="line-height:34px;">元/分钟</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm" onclick="accountSettingConfirm()">确定</button>
                <button type="button" class="btn btn-white _close">取消</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade inmodal" tabindex="-1" id="rechargeTelephoneModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close _close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">话费充值</h4>
            </div>
            <div class="modal-body">
                <div class="data-box">
                    <form role="form">
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-5 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>充值账号:</label>
                            <div class="col-md-6 h5" id="rechargeAccount" style="padding-left: 0px;"></div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-5 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>金额:</label>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <input type="text" class="form-control" name="recharge_amount" placeholder="充值金额">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm" onclick="rechargeTelephoneConfirm()">确定</button>
                <button type="button" class="btn btn-white _close">取消</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade inmodal" tabindex="-1" id="openDialoutAgentModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close _close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">开通坐席</h4>
            </div>
            <div class="modal-body">
                <div class="data-box">
                    <form role="form">
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>选择操作:</label>
                            <div class="col-md-6 h5" style="padding-left: 0px;">
                                <label class="radio-inline">
                                    <input type="radio" name="openAgentType" value="open" checked> 开通坐席
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="openAgentType" value="renew"> 续费坐席
                                </label>
                            </div>
                        </div>
                        <div class="form-group row" id="new_option" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>座席数:</label>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <input type="text" class="form-control" name="add_num" placeholder="输入开通坐席数量">
                            </div>
                        </div>
                        <div class="form-group row hide" id="renew_option" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>选择坐席:</label>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <select class="form-control" name="exten" id="dialoutAgentSelect"></select>
                            </div>
                        </div>
                        <div class="form-group row" style="overflow:hidden; margin-top:20px;">
                            <label class="control-label col-md-3 text-right" style="margin:0 10px 0 0;line-height:34px;"><span class="red">* </span>开通时长:</label>
                            <div class="col-md-8" style="padding-left: 0px;">
                                <div>
                                    <select class="form-control" name="duration" style="width: 70px;display: inline-block;">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                    </select> 年
                                </div>
                                <div style="margin-top:10px;font-size: 12px;">
                                    <p>开通坐席将根据开通时长自动计算到期时间。</p>
                                    <p>如新增1个坐席，开通一年则到期时间为：当前日期顺延365天</p>
                                    <p>如续费1个坐席，续费一年则到期时间为：续费坐席的到期时间再往后顺延365天</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm" onclick="openDialoutAgentConfirm()">确定</button>
                <button type="button" class="btn btn-white _close">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
    var Main = {
        view: function(){
            return {
                el: `<div @onclick="aa">
                    </div>`,
                events: {
                   aa: function(){
                       alert(1)
                   }
                }
            }
        }
    }

    function showMore(id){
        $('.more-btn').map(x=>{
            $('.more-btn')[x].style.display = 'none'
        })
        if($('.more-btn-'+id)[0].style.display == 'none'){
            $('.more-btn-'+id)[0].style.display = ''
        }else{
            $('.more-btn-'+id)[0].style.display = 'none'
        }
    }

    var dialoutConfigId;

    function getDialoutConfig(id, callback){
        $.ajax({
            url    : "/admin/call-literature/read",
            type   : "GET",
            async  : false,
            data   : {id: id},
            dataType: 'json',
            success: callback
        })
    }

    function accountSetting(id){
        dialoutConfigId = id

        getDialoutConfig(id, function(res){
            var data = res.data

            $('input[name="exten_money"]').val(data.exten_money)
            $('input[name="phone_money"]').val(data.phone_money)

            $('#accountSettingModal').modal('show');
        })
    }

    function accountSettingConfirm(){
        var exten_money = $('input[name="exten_money"]').val(),
            phone_money = $('input[name="phone_money"]').val()

        if(exten_money == ''){
            return swal('温馨提示', '坐席价格不允许为空', 'error')
        }

        if(phone_money == ''){
            return swal('温馨提示', '话费价格不允许为空', 'error')
        }

        var data = {
            id: dialoutConfigId,
            exten_money: exten_money,
            phone_money: phone_money
        }

        var formData = new FormData();

        Object.keys(data).forEach(function (key) {
            formData.append(key, data[key])
        })

        $.ajax({
            url: '<?=\yii\helpers\Url::to('/admin/dialout/set-config')?>',
            type: "POST",
            async: false,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(res){
                if(res.error == 1) {
                    return swal("温馨提示", res.data, "error");
                }
                swal('温馨提示', '账号设置成功', 'success')
                $("#accountSettingModal").modal('hide');
            }
        })
    }

    function rechargeTelephoneConfirm(){
        var recharge_amount = $('input[name="recharge_amount"]').val()

        if(recharge_amount == ''){
            return swal('温馨提示', '充值金额不允许为空', 'error')
        }

        var data = {
            id: dialoutConfigId,
            money: recharge_amount
        }

        var formData = new FormData();

        Object.keys(data).forEach(function (key) {
            formData.append(key, data[key])
        })

        $.ajax({
            url: '<?=\yii\helpers\Url::to('/admin/dialout/recharge')?>',
            type: "POST",
            async: false,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(res){
                if(res.error == 1) {
                    return swal("温馨提示", res.data, "error");
                }
                swal('温馨提示', '充值成功', 'success')
                $("#rechargeTelephoneModal").modal('hide');
            }
        })
    }

    function rechargeTelephone(id, phone){
        dialoutConfigId = id
        $('#rechargeAccount').text(phone);
        $('#rechargeTelephoneModal').modal('show');
    }

    function createElement(name){
        return $(document.createElement(name));
    }

    function openDialoutAgent(id){
        $.ajax({
            url: '<?=\yii\helpers\Url::to('/admin/dialout/agents')?>',
            type: "GET",
            async: false,
            data: {
                id: id
            },
            dataType: "json",
            success: function(res){
                var agents = res.data

                var agentElements = agents.map(function(agent){
                    return createElement('option').attr('value', agent.exten).text(agent.exten + ' (到期: ' + agent.expire +')')
                })

                agentElements = [
                    createElement('option').attr('value', 0).text('请选择坐席')
                ].concat(agentElements)

                if(agentElements.length == 0) agentElements = [ createElement('option').attr('value', 0).text('没有找到坐席')]

                $('#dialoutAgentSelect').empty().append(agentElements)

                dialoutConfigId = id
                $('#openDialoutAgentModal').modal('show')
            }
        })
    }

    function openDialoutAgentConfirm(){
        var type = $('input[name="openAgentType"]:checked').val()
        var duration = $('select[name="duration"]').val()

        var data = {
            id: dialoutConfigId,
            duration: duration
        }

        if(type == 'open'){
            data.add_num = $('input[name="add_num"]').val()

            if(data.add_num == ''){
                return swal('温馨提示', '请输入开通时长', 'error')
            }
        }else{
            data.exten = $('select[name="exten"]').val()

            if(data.exten == 0){
                return swal('温馨提示', '请选择续费坐席', 'error')
            }
        }

        var formData = new FormData();

        Object.keys(data).forEach(function (key) {
            formData.append(key, data[key])
        })

        $.ajax({
            url: '/admin/dialout/' + (type == 'open' ? 'add-agent' : 'exten-renew'),
            type: "POST",
            async: false,
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(res){
                if(res.error == 1) {
                    return swal("温馨提示", res.data, "error");
                }
                swal('温馨提示', '开通成功', 'success')
                $("#openDialoutAgentModal").modal('hide');
            }
        })
    }

    $(function(){
        $("#accountSettingModal ._close, #rechargeTelephoneModal ._close, #openDialoutAgentModal ._close").on('click', function () {
            $(this).parents('.modal').modal('hide')
        });


        $('input[name="openAgentType"]').on('change', function(){
            var type = $(this).val()

            if(type == 'open') $('#new_option').removeClass('hide').siblings('#renew_option').addClass('hide')
            else $('#new_option').addClass('hide').siblings('#renew_option').removeClass('hide')
        })
    })
</script>