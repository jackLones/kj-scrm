<?php


namespace app\modules\api\controllers;


use app\components\InvalidParameterException;
use app\models\WechatMenus;
use app\models\WechatMenusForm;
use app\modules\api\components\AuthBaseController;
use app\util\SUtils;

class WechatMenuController extends AuthBaseController
{
    /**
     * 参数验证
     * @param $scenario
     * @return array
     * @throws \Exception
     */
    private function validateParameter($scenario,$method = 'post')
    {
        $wechatMenusForm = new WechatMenusForm(['scenario'=>$scenario]);
        !($wechatMenusForm->load(\Yii::$app->request->$method(), '') && $wechatMenusForm->validate())
        && SUtils::throwException(InvalidParameterException::class,current($wechatMenusForm->getFirstErrors()));

        return array_filter($wechatMenusForm->attributes);
    }

    /**
     * @param array $data
     * @return WechatMenus
     */
    private function wechatMenus(array $data)
    {
        $data['appid'] = $this->wxAuthorInfo->authorizer_appid;
        $wechatMenus = new WechatMenus(['appid'=>$data['appid']]);
        $wechatMenus->load($data,'');
        return $wechatMenus;
    }
    /**
     * 菜单列表
     * @return array
     * @throws InvalidParameterException
     */
    public function actionWechatMenuList()
    {
        $wechatMenus = $this->wechatMenus($this->validateParameter('list'));
        $list = $wechatMenus->menusList();
        $initMenu = 0;
        if(empty($list['ordinaryMenu'])){
            $menu = $wechatMenus->wechatMenu();
            !empty($menu['is_menu_open']) && $initMenu = 1;//需要同步菜单
        }
        return compact('list','initMenu');
    }

    /**
     * 公众号菜单生成
     * @return string[]
     * @throws InvalidParameterException
     * @throws \Exception
     */
    public function actionSaveWechatMenu ()
    {
        $confirmSave = \Yii::$app->request->post('confirm_save');
        $scenario = \Yii::$app->request->post('type',WechatMenus::ORDINARY_MENU) == WechatMenus::ORDINARY_MENU ? 'ordinary' : 'personalized';
        $menuID = $this->wechatMenus($this->validateParameter($scenario))->addMenus($confirmSave);

        return compact('menuID');
    }

    /**
     * 删除个性化菜单
     * @return string[]
     * @throws \app\components\InvalidDataException
     */
    public function actionDelPersonalizedMenu()
    {
        $result = $this->wechatMenus($this->validateParameter('delPersonalizedMenu'))->delPersonalizedMenu();
        return [
            'msg' => $result ? '删除成功' : '删除失败'
        ];
    }

    /**
     * 公众号菜单列表
     * @return array
     * @throws InvalidParameterException
     */
    public function actionWechatMenus()
    {
        $wechatMenu = $this->wechatMenus($this->validateParameter('list'));
        $menus = $wechatMenu->wechatMenu();
        $allMenus = $wechatMenu->wechatAllMenu();
        return compact('menus','allMenus');
    }

    /**
     * 同步公众号菜单列表
     * @return array
     * @throws InvalidParameterException
     */
    public function actionSyncOfficialWechatMenus()
    {
        $wechatMenus = $this->wechatMenus($this->validateParameter('list'));
        $menu = $wechatMenus->wechatMenu();
        empty($menu['is_menu_open']) && SUtils::throwException(InvalidParameterException::class,'没有正在使用的菜单');

        $result = $wechatMenus->syncWechatMenu();

        return [
            'msg' => $result ? '同步成功' : '同步失败'
        ];
    }
}