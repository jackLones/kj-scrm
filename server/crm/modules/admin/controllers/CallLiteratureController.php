<?php

namespace app\modules\admin\controllers;

use app\models\DialoutConfig;
use yii\data\Pagination;
use yii\web\Request;

class CallLiteratureController extends CallController
{
    public $pageSize = 20;

    /**
     * 客户资料审核列表
     *
     * @method GET
     * @param Request $request
     * @return string
     */
    public function actionIndex(Request $request)
    {
        $status = $request->get('status', '-1');
        $keywords = $request->get('keywords', '');

        $query = DialoutConfig::find()->alias('dc');

        if (!empty($keywords)) {
            $query = $query->rightJoin('{{%work_corp}} wc', 'wc.id = dc.corp_id')
                ->rightJoin('{{%user}} u', 'u.uid = dc.uid')
                ->where(['u.account' => $keywords])
                ->orWhere(['wc.corp_name' => $keywords]);
        }

        if ($status != -1) {
            $query = $query->andWhere(['dc.status' => $status]);
        }

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);

        $dialoutConfigs = $query->with(['workCorp', 'user'])->offset($pages->offset)->limit($pages->limit)->orderBy('id DESC')->all();

        return $this->render('index', [
            'status'         => $status,
            'keywords'       => $keywords,
            'pages'          => $pages,
            'dialoutConfigs' => $dialoutConfigs
        ]);
    }

    /**
     * 获取提审资料详情
     *
     * @method GET
     * @param Request $request
     */
    public function actionRead(Request $request)
    {
        $id = $request->get('id');

        $dialoutConfig = DialoutConfig::find()->where(['id' => $id])->asArray()->one();

        $this->dexit(['error' => 0, 'data' => $dialoutConfig]);
    }
}