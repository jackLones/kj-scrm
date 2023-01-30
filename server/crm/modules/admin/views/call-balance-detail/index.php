<?php
    $this->title = '平台明细-外呼系统';

use yii\helpers\Html;
use yii\widgets\LinkPager;

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
                <strong>平台明细</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>
<div class="wrapper wrapper-content">
    <div class="row" style="background-color:#fff; padding: 10px;">
        <div class="col-md-6">
            <div class="col-md-4 h4">
                <div class="row">
                    预存总金额
                </div>
                <div class="row" style="margin-top:15px;">
                    ￥<?=sprintf('%.2f', $recharge_amount_sum)?>
                </div>
            </div>
            <div class="col-md-4 h4">
                <div class="row">
                    平台扣除总金额
                </div>
                <div class="row" style="margin-top:15px;">
                    ￥<?=sprintf('%.2f', $dissipate_amount_sum)?>
                </div>
            </div>
            <div class="col-md-4 h4">
                <div class="row">
                    剩余金额
                </div>
                <div class="row" style="margin-top:15px;">
                    ￥<?=sprintf('%.2f', $balance)?>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-group">
                        <form id="searchForm" method="get" action="/admin/call-balance-detail/index">
                            <div class="row text-center" style="padding: 0 15px;">
                                <div class="row search_top" style="margin-bottom: 10px">
                                    <div class="col-lg-4">
                                        <select style="width: calc(40% - 23px);display: inline-block;" class="form-control select" name="action" >
                                            <option value="0" <?= ($action == '0')?'selected':'';?> >全部类型</option>
                                            <option value="1" <?= ($action == '1')?'selected':'';?> >开通坐席</option>
                                            <option value="2" <?= ($action == '2')?'selected':'';?> >话费充值</option>
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
                                            <th width="15%">客户账号</th>
                                            <th width="15%">公司名称</th>
                                            <th width="12%">企业微信</th>
                                            <th width="10%">消耗类型</th>
                                            <th width="25%">扣费详情</th>
                                            <th>平台扣除费用</th>
                                            <th>扣除时间</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-list-body" class="js-list-body-region">
                                        <?php if (!empty($dissipateLogs)) { ?>
                                            <?php foreach($dissipateLogs as $dissipateLog){ ?>
                                                <tr class="widget-list-item">
                                                    <td><?=\app\util\SUtils::hideString($dissipateLog->user->account)?></td>
                                                    <td><?=\app\util\SUtils::deepHideString($dissipateLog->workCorp->corp_full_name)?></td>
                                                    <td><?=\app\util\SUtils::deepHideString($dissipateLog->workCorp->corp_name)?></td>
                                                    <td><?=$dissipateLog->action_str?></td>
                                                    <td><?=$dissipateLog->detailed?></td>
                                                    <td>- ￥ <?=sprintf('%.2f', $dissipateLog->amount)?></td>
                                                    <td><?=$dissipateLog->create_time?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <tr class="widget-list-item text-center"><td colspan="15">暂无线路资费信息</td></tr>
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
<script>
    function search(){
        $('#searchForm').submit();
    }
</script>
