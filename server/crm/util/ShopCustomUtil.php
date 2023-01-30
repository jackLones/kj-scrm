<?php

namespace app\util;

use app\models\Menu;
use app\models\PackageMenu;
use app\models\User;
use app\models\UserCorpRelation;

class ShopCustomUtil
{
    //开启电商零售场景企业id数组
    public static function getHaShopCustomCropIds()
    {
        $menu = Menu::find()->where(['key' => 'shopCustom','status'=>1])->select('id')->asArray()->one();
        if (empty($menu)) {
            return [];
        }

        $package = PackageMenu::find()->where(['menu_id' => $menu['id'],'status'=>1])->select('package_id')->asArray()->all();
        if (empty($package)) {
            return [];
        }

        $packageIds = array_column($package, 'package_id');
        $user       = User::find()->where(['package_id' => $packageIds, 'status' => 1])
            ->select('uid')->asArray()->all();
        if (empty($user)) {
            return [];
        }

        $userId = array_column($user, 'uid');
        $corp   = UserCorpRelation::find()->where(['uid' => $userId])
            ->select('corp_id')->asArray()->all();
        if (empty($corp)) {
            return [];
        }

        return array_column($corp, 'corp_id');
    }


}