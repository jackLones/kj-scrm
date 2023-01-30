<?php


namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\models\ShopCustomerOrder;
use app\models\ShopMaterialCollect;
use app\models\ShopMaterialConfig;
use app\models\ShopMaterialCoupon;
use app\models\ShopMaterialPage;
use app\models\ShopMaterialProduct;
use app\models\ShopMaterialProductGroup;
use app\models\ShopMaterialSourceRelationship;
use app\models\ShopThirdOrderCoupon;
use app\models\WorkChat;
use app\models\WorkExternalContact;
use app\models\WorkUser;
use app\modules\api\components\WorkBaseController;
use app\util\StringUtil;
use yii\caching\TagDependency;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ShopMaterialController extends WorkBaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'get-group'     => ['POST'],
                    'product'       => ['POST'],
                    'get-collect'   => ['POST'],
                    'collect'       => ['POST'],
                    'del-collect'   => ['POST'],
                    'page'          => ['POST'],
                    'coupon'        => ['POST'],
                    'save-config'   => ['POST'],
                    'get-config'    => ['POST'],
                    'make-share'    => ['POST'],
                    'update-share'  => ['POST'],
                    'all-url'       => ['POST'],
                    'share-detail'  => ['POST'],
                    'share-account' => ['POST']
                ],
            ],
        ]);
    }

    /**
     * 手机端获取商品分组接口
     * @url  http://{host_name}/api/shop-material/get-group
     * @throws InvalidDataException
     */
    public function actionGetGroup()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $source = \Yii::$app->request->post('source', '1');
        $group  = ShopMaterialProductGroup::getGroupList($this->corp->id, ['source' => $source]);
        return ['result' => $group ?: []];
    }

    /**
     * 商品素材接口
     * @url  http://{host_name}/api/shop-material/product
     *
     * @throws InvalidDataException
     */
    public function actionProduct()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }

        $post     = \Yii::$app->request->post();
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;

        //导购id
        $corpUserId = \Yii::$app->request->post('now_userid', '');
        $guideId    = 0;
        if (!empty($corpUserId)) {
            $workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $corpUserId]);
            if (empty($workUser)) {
                throw new InvalidDataException('导购信息错误！');
            }
            $guideId = $workUser->id;
        }

        $productId = \Yii::$app->request->post('product_id', 0);
        $keyWord   = \Yii::$app->request->post('key_word', '');
        $groupId   = \Yii::$app->request->post('group_id', 0);
        $price     = \Yii::$app->request->post('price', '');
        $endPrice  = \Yii::$app->request->post('end_price', '');

        $source = \Yii::$app->request->post('source', '1');
        $cateId = \Yii::$app->request->post('cate_id', '0');
        $status = \Yii::$app->request->post('status', '-1');
        //是否存在筛选条件 默认无
        $isChoose = false;
        if ($pageSize > 15) {
            $isChoose = true;
        }
        //组合筛选条件
        $material = ShopMaterialProduct::find();
        $material = $material->where(['corp_id' => $this->corp->id]);
        if (!empty($productId)) {
            $isChoose = true;
            if (!is_numeric($productId)) {
                throw new InvalidDataException('素材id必须为数字！');
            }
            $material = $material->andWhere(['id' => $productId]);
        }
        if (!empty($keyWord)) {
            $isChoose = true;
            $material = $material->andWhere(['or', ['like', 'name', $keyWord], ['=', 'code', $keyWord]]);
        }
        if (!empty($groupId)) {
            $material = $material->andWhere(['group_id' => $groupId]);
        }
        if (!empty($endPrice) && !empty($price)) {
            $isChoose = true;
            if (!is_numeric($endPrice) || !is_numeric($price)) {
                throw new InvalidDataException('价格类型必须为数字！');
            }
            $material = $material->andWhere(['between', 'price', $price, $endPrice]);
        }
        if (!empty($source)) {
            $material = $material->andWhere(['source' => $source]);
        }
        if (!empty($cateId)) {
            $isChoose = true;
            $material = $material->andWhere(['cate_id' => $cateId]);
        }
        if ($status != '-1') {
            $isChoose = true;
            $material = $material->andWhere(['status' => $status]);
        }

        //查询
        $count    = $material->count();
        $fields   = ['id', 'id as key', 'status', 'code', 'name', 'image', 'weapp_url', 'web_url', 'recommend_remark',
            'original_price', 'original_end_price', 'price', 'end_price', 'type', 'source', 'group_id'];
        $material = $material->select($fields);
        $material = $material->with(['group' => function (\yii\db\ActiveQuery $query) {
            return $query->select('id,name');
        }]);

        //存在导购id则查看收藏情况
        if ($guideId > 0) {
            $material = $material->with(['collect' => function (\yii\db\ActiveQuery $query) use ($guideId) {
                return $query->select('id,material_id')->where(['user_id' => $guideId]);
            }]);
        }

        //缓存列表数据
        $cacheKey = 'shop_material_product_' . $this->corp->id . '_' . $groupId . '_' . $page;
        $result   = \Yii::$app->cache->get($cacheKey);
        if ($isChoose || empty($result)) {
            //获取数据
            $list = $material->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();
            //处理列表数据
            $result = $this->dealProduct($list);
            if (!$isChoose) {
                \Yii::$app->cache->set($cacheKey, $result, null, new TagDependency(['tags' => 'shop_material_product_' . $this->corp->id]));
            }
        }

        //分组
        $group = ShopMaterialProductGroup::getGroupList($this->corp->id, ['source' => $source]);
        //是否存在下一页
        $next_page = $count > $page * $pageSize;
        return [
            'where'     => $post,
            'count'     => $count,
            'next_page' => $next_page,
            'result'    => $result ?: [],
            'group'     => $group ?: []
        ];
    }

    /**
     * 获取收藏素材
     * @url  http://{host_name}/api/shop-material/get-collect
     * @throws InvalidDataException
     */
    public function actionGetCollect()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        //分页信息
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;
        //商品名称或者编号
        $keyWord = \Yii::$app->request->post('key_word', '');
        //收藏人
        $corpUserId = \Yii::$app->request->post('now_userid', '');
        if (empty($corpUserId)) {
            throw new InvalidDataException('缺少导购userid参数！');
        }
        $workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $corpUserId]);
        if (empty($workUser)) {
            throw new InvalidDataException('导购信息错误！');
        }
        $guideId = $workUser->id;

        $isChoose = false;
        if ($pageSize > 15 || !empty($keyWord)) {
            $isChoose = true;
        }

        //实例模型
        $collectModel = ShopMaterialCollect::find()->alias('c')
            ->leftJoin('{{%shop_material_product}} p', 'c.material_id=p.id')
            ->where(['c.corp_id' => $this->corp->id, 'c.user_id' => $guideId, 'c.material_type' => 1, 'p.status' => 1]);

        //搜索商品名称或者编码
        if (!empty($keyWord)) {
            $collectModel = $collectModel->andWhere(['or', ['like', 'p.name', $keyWord], ['like', 'p.code', $keyWord]]);
        }

        $cacheKey = 'collect_product_' . $this->corp->id . '_' . $guideId . '_' . $page;
        $return   = \Yii::$app->cache->get($cacheKey);
        if ($isChoose || empty($return)) {
            $count  = $collectModel->count();
            $fields = ['p.id as key', 'p.status', 'p.code', 'p.name', 'p.image', 'p.weapp_url', 'p.web_url', 'p.recommend_remark',
                'p.original_price', 'p.original_end_price', 'p.price', 'p.end_price', 'p.type', 'p.source', 'c.id as collect_id'];
            $list   = $collectModel->select($fields)->limit($pageSize)->offset($offset)->orderBy(['c.id' => SORT_DESC])->asArray()->all();
            //处理列表数据
            $result = $this->dealProduct($list);
            //是否存在下一页
            $next_page = $count > $page * $pageSize;
            $return    = [
                'count'     => $count,
                'next_page' => $next_page,
                'result'    => $result ?: []
            ];
            if (!$isChoose) {
                \Yii::$app->cache->set($cacheKey, $return, null, new TagDependency(['tags' => 'shop_material_product_' . $this->corp->id]));
            }
        }
        return $return;

    }

    /**
     * 获取收藏素材
     * @url  http://{host_name}/api/shop-material/collect
     * @throws InvalidDataException
     */
    public function actionCollect()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }

        //收藏人
        $corpUserId = \Yii::$app->request->post('now_userid', '');
        if (empty($corpUserId)) {
            throw new InvalidDataException('缺少导购userid参数！');
        }
        $workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $corpUserId]);
        if (empty($workUser)) {
            throw new InvalidDataException('导购信息错误！');
        }
        $guideId = $workUser->id;

        //收藏素材
        $materialType  = \Yii::$app->request->post('material_type', '1');
        $productId     = \Yii::$app->request->post('material_id', '');
        $productIdList = explode(',', $productId);

        if (empty($productIdList)) {
            throw new InvalidDataException('素材id不能为空！');
        }

        //操作类型
        $type = \Yii::$app->request->post('type', '1');


        //循环保存或者删除
        foreach ($productIdList as $pid) {
            //收藏时验证商品是否有效
            if ($type == 1) {
                $product = ShopMaterialProduct::findOne(['id' => $pid, 'status' => 1, 'corp_id' => $this->corp->id]);
                if (empty($product)) {
                    throw new InvalidDataException('该商品在不存在或者已下架！');
                }
            }

            $saveData = [
                'corp_id'       => intval($this->corp->id),
                'user_id'       => intval($guideId),
                'material_id'   => intval($pid),
                'material_type' => intval($materialType),
            ];
            if ($type == 1) {//收藏
                ShopMaterialCollect::saveData($saveData);
            } else {//删除
                ShopMaterialCollect::deleteAll($saveData);
            }

        }

        //清除商品缓存
        TagDependency::invalidate(\Yii::$app->cache, 'shop_material_product_' . $this->corp->id);

        return ['result' => 1];
    }

    //处理商品素材返回数据
    private function dealProduct($list)
    {
        if (empty($list)) {
            return [];
        }
        foreach ($list as &$pro) {
            if (!empty($pro['recommend_remark'])) {
                $pro['recommend_remark'] = rawurldecode($pro['recommend_remark']);
            }
            $pro['status_name'] = ($pro['status'] == 1) ? '正常' : '下架';
            //价格
            if ($pro['original_end_price'] > $pro['original_price']) {
                $pro['original_price_text'] = '￥' . $pro['original_price'] . '~' . '￥' . $pro['original_end_price'];
            } else {
                $pro['original_price_text'] = '￥' . $pro['original_price'];
            }
            if ($pro['end_price'] > $pro['price']) {
                $pro['end_price_text'] = '￥' . $pro['price'] . '~' . '￥' . $pro['end_price'];
            } else {
                $pro['end_price_text'] = '￥' . $pro['price'];
            }
            //处理分组
            if (isset($pro['group'])) {
                $pro['group_name'] = $pro['group'] ? $pro['group']['name'] : '-';
                unset($pro['group']);
            }
            //处理收藏情况
            if (isset($pro['collect'])) {//商品列表
                $pro['collect_id'] = $pro['collect'] ? $pro['collect'][0]['id'] : 0;
                unset($pro['collect']);
            } else if (!isset($pro['collect_id'])) {//收藏列表
                $pro['collect_id'] = 0;
            }
            //处理来源
            $pro['source_name'] = ShopMaterialProduct::getSource($pro['source']);
            //处理类型
            $pro['type_name'] = ShopMaterialProduct::getType($pro['type']);
        }
        return $list;
    }

    /**
     * 页面素材接口
     * @url  http://{host_name}/api/shop-material/page
     * */
    public function actionPage()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;
        $status = \Yii::$app->request->post('status', '-1');

        $material = ShopMaterialPage::find()->where(['corp_id' => $this->corp->id]);
        $isChoose = false;
        if ($pageSize > 15) {
            $isChoose = true;
        }
        if ($status != '-1') {
            $isChoose = true;
            $material = $material->andWhere(['status' => $status]);
        }

        $cacheKey = 'shop_material_page_' . $this->corp->id . '_' . $page;
        $return   = \Yii::$app->cache->get($cacheKey);
        if ($isChoose || empty($return)) {

            $count    = $material->count();
            $list     = $material->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();
            //处理列表数据
            $result = $this->dealPage($list);
            //是否存在下一页
            $next_page = $count > $page * $pageSize;
            $return    = [
                'count'     => $count,
                'next_page' => $next_page,
                'result'    => $result ?: [],
            ];
            if (!$isChoose) {
                \Yii::$app->cache->set($cacheKey, $return, null, new TagDependency(['tags' => 'shop_material_page_' . $this->corp->id]));
            }
        }
        return $return;
    }

    //处理页面素材返回数据
    private function dealPage($list)
    {
        if (empty($list)) {
            return [];
        }
        $result = [];
        foreach ($list as $k => $v) {
            $result[$k]['key'] = $v['id'];
            //处理来源
            $result[$k]['source']      = $v['source'];
            $result[$k]['source_name'] = ShopMaterialProduct::getSource($v['source']);
            $result[$k]['page_id']     = $v['page_id'];
            $result[$k]['title']       = $v['title'];
            $result[$k]['image']       = $v['image'];
            $result[$k]['desc']        = $v['desc'];
            $result[$k]['status_name'] = ($v['status'] == 1) ? '正常' : '下架';
            $result[$k]['weapp_url']   = $v['weapp_url'];
            $result[$k]['web_url']     = $v['web_url'];
        }
        return $result;
    }

    /**
     * 优惠券素材接口
     * @url  http://{host_name}/api/shop-material/coupon
     * */
    public function actionCoupon()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;
        $status = \Yii::$app->request->post('status', '-1');
        $material = ShopMaterialCoupon::find()->where(['corp_id' => $this->corp->id]);
        $isChoose = false;
        if ($pageSize > 15) {
            $isChoose = true;
        }
        if ($status != '-1') {
            $isChoose = true;
            $material = $material->andWhere(['status' => $status]);
        }

        $cacheKey = 'shop_material_coupon_' . $this->corp->id . '_' . $page;
        $return   = \Yii::$app->cache->get($cacheKey);
        if ($isChoose || empty($return)) {
            $count    = $material->count();
            $list     = $material->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();
            //处理列表数据
            $result = $this->dealCoupon($list);
            //是否存在下一页
            $next_page = $count > $page * $pageSize;
            $return    = [
                'count'     => $count,
                'next_page' => $next_page,
                'result'    => $result ?: [],
            ];
            if (!$isChoose) {
                \Yii::$app->cache->set($cacheKey, $return, null, new TagDependency(['tags' => 'shop_material_coupon_' . $this->corp->id]));
            }
        }
        return $return;
    }

    //处理优惠券素材返回数据
    private function dealCoupon($list)
    {
        if (empty($list)) {
            return [];
        }
        $result = [];
        foreach ($list as $k => $v) {
            $result[$k]['key'] = $v['id'];
            //处理来源
            $result[$k]['name']              = $v['name'];
            $result[$k]['source']            = $v['source'];
            $result[$k]['source_name']       = ShopMaterialProduct::getSource($v['source']);
            $result[$k]['coupon_id']         = $v['coupon_id'];
            $result[$k]['type']              = $v['type'];
            $result[$k]['type_name']         = ShopMaterialCoupon::getType($v['type']);
            $result[$k]['face_money']        = $v['face_money'];
            $result[$k]['limit_money']       = $v['limit_money'];
            $result[$k]['limit_money_text']  = $v['limit_money'] == 0 ? '无门槛' : '满' . $v['limit_money'] . '元可用';
            $result[$k]['use_range_product'] = $v['is_all_product'] == ShopMaterialCoupon::IS_PRODUCT_ALL ? '全店通用' : '指定商品使用';
            if ($v['time_type'] == ShopMaterialCoupon::TIME_TYPE_ZERO) {
                $result[$k]['use_range_time'] = date('Y/m/d', $v['start_time']) . '-' . date('Y/m/d', $v['end_time']);
            } else {
                $result[$k]['use_range_time'] = $v['time_fixed'];
            }
            $result[$k]['weapp_url']   = $v['weapp_url'];
            $result[$k]['web_url']     = $v['web_url'];
            $result[$k]['status_name'] = ($v['status'] == 1) ? '正常' : '下架';
        }
        return $result;
    }

    /**
     * 保存素材侧边栏配置接口
     * @url  http://{host_name}/api/shop-material/save-config
     *
     * @throws \Throwable
     */
    public function actionSaveConfig()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        //接收数据
        $saveData                 = [];
        $saveData['corp_id']      = $this->corp->id;
        $saveData['product']      = \Yii::$app->request->post('product', 0);
        $saveData['page']         = \Yii::$app->request->post('page', 0);
        $saveData['coupon']       = \Yii::$app->request->post('coupon', 0);
        $saveData['weapp_appid']  = \Yii::$app->request->post('weapp_appid', '');
        $saveData['weapp_name']   = \Yii::$app->request->post('weapp_name', '');
        $saveData['page_image']   = \Yii::$app->request->post('page_image', '');
        $saveData['coupon_image'] = \Yii::$app->request->post('coupon_image', '');
        $saveData['web_open']     = \Yii::$app->request->post('web_open', 0);
        if (!empty($saveData['weapp_appid']) && empty($saveData['weapp_name'])) {
            throw new InvalidDataException('小程序名称不能为空！');
        }
        if (empty($saveData['weapp_appid']) && !empty($saveData['weapp_name'])) {
            throw new InvalidDataException('小程序app_key不能为空！');
        }
        if (empty($saveData['web_open']) && empty($saveData['weapp_appid'])) {
            throw new InvalidDataException('小程序和H5必须配置一项！');
        }
        if (empty($saveData['product']) && empty($saveData['page']) && empty($saveData['coupon'])) {
            throw new InvalidDataException('商品、页面和优惠券必须配置一项！');
        }
        //保存数据
        return ShopMaterialConfig::saveConfig($this->corp->id, $saveData);
    }

    /**
     * 获取素材侧边栏配置接口
     * @url  http://{host_name}/api/shop-material/get-config
     *
     * @throws InvalidDataException
     */
    public function actionGetConfig()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $re      = ShopMaterialConfig::getConfig($this->corp->id);
        $siteUrl = \Yii::$app->params['site_url'];
        if (empty($re['coupon_image'])) {
            $re['coupon_image'] = $siteUrl . '/static/image/default_coupon.png';
        } else {
            $re['coupon_image'] = $siteUrl . $re['coupon_image'];
        }
        if (empty($re['page_image'])) {
            $re['page_image'] = $siteUrl . '/static/image/default_page.png';
        } else {
            $re['page_image'] = $siteUrl . $re['page_image'];
        }
        return ['result' => $re ?: []];
    }

    /**
     * 分享是生成分享id
     * @url  http://{host_name}/api/shop-material/make-share
     *
     * @throws InvalidDataException
     */
    public function actionMakeShare()
    {
        //企业id
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        //获取配置信息
        $config = ShopMaterialConfig::getConfig($this->corp->id);
        if (empty($config)) {
            throw new InvalidDataException('企业配置信息不存在！');
        }
        //发送人
        $corpUserId = \Yii::$app->request->post('now_userid', '');
        if (empty($corpUserId)) {
            throw new InvalidDataException('缺少导购userid参数！');
        }
        $workUser = WorkUser::findOne(['corp_id' => $this->corp->id, 'userid' => $corpUserId]);
        if (empty($workUser)) {
            throw new InvalidDataException('导购信息错误！');
        }
        $userId = $workUser->id;

        //素材id
        $materialId = \Yii::$app->request->post('material_id', '');
        if (empty($materialId)) {
            throw new InvalidDataException('缺少素材id参数！');
        }


        //素材类型
        $materialType = \Yii::$app->request->post('material_type', '');
        if (empty($materialType)) {
            throw new InvalidDataException('缺少素材类型参数！');
        }

        $siteUrl       = \Yii::$app->params['site_url'];
        $defaultCoupon = $config['coupon_image'] ?: '/static/image/default_coupon.png';
        $defaultPage   = $config['page_image'] ?: '/static/image/default_page.png';
        $materialName  = $materialContent = $materialUrl = '';

        //获取内容快照
        switch ($materialType) {
            case ShopMaterialSourceRelationship::MATERIAL_TYPE_PRODUCT:
                $material        = ShopMaterialProduct::find()->where(['id' => $materialId])->asArray()->all();
                $material        = $this->dealProduct($material);
                $material        = count($material) > 0 ? $material[0] : [];
                $materialName    = isset($material['name']) ? $material['name'] : '';
                $materialUrl     = !empty($material['image']) ? $material['image'] : '';
                $materialContent = !empty($material['recommend_remark']) ? $material['recommend_remark'] . "\n" : $material['name'] . "\n";
                break;
            case ShopMaterialSourceRelationship::MATERIAL_TYPE_PAGE:
                $material        = ShopMaterialPage::find()->where(['id' => $materialId])->asArray()->all();
                $material        = $this->dealPage($material);
                $material        = count($material) > 0 ? $material[0] : [];
                $materialUrl     = $siteUrl . $defaultPage;
                $materialName    = !empty($material['title']) ? $material['title'] : '';
                $materialContent = !empty($material['title']) ? $material['title'] . "\n" : '';
                break;
            case ShopMaterialSourceRelationship::MATERIAL_TYPE_COUPON:
                $material     = ShopMaterialCoupon::find()->where(['id' => $materialId])->asArray()->all();
                $material     = $this->dealCoupon($material);
                $material     = count($material) > 0 ? $material[0] : [];
                $materialUrl  = $siteUrl . $defaultCoupon;
                $materialName = isset($material['name']) ? $material['name'] : '';
                break;
            default:
                $material = [];
                break;
        }

        if (empty($material)) {
            throw new InvalidDataException('素材不存在！');
        } else {
            $weappUrl   = $material['weapp_url'];
            $webContent = $webUrl = '';
        }

        //短地址标识
        $saveShare    = [
            'corp_id'       => $this->corp->id,
            'user_id'       => $userId,
            'material_id'   => $materialId,
            'material_type' => $materialType
        ];
        $relationship = new ShopMaterialSourceRelationship();
        $relationship->setAttributes($saveShare);
        $relationship->save();
        $shareId = $relationship->id;
        if (empty($shareId)) {
            throw new InvalidDataException('生成分享id失败！请重试');
        }

        //组合短地址
        $oldWebUrl = '';
        if (!empty($material['web_url'])) {
            $shortUrl   = $this->randShortUrl();
            $webUrl     = \Yii::$app->params['site_url'] . "/shop/" . $shortUrl;
            $webContent = $materialContent . $webUrl;
            $oldWebUrl  = $material['web_url'];
        }

        //小程序页面分享id拼接
        if (!empty($weappUrl)) {
            if (strstr($weappUrl, '?') !== false) {
                $weappUrl = str_replace('?', '.html?', $weappUrl);
                $weappUrl = $weappUrl . '&scrmsid=' . $shareId;
            } else {
                $weappUrl = $weappUrl . '.html' . '?scrmsid=' . $shareId;
            }
            //券则加上券id
            if(isset($material['coupon_id']) && !empty($material['coupon_id'])){
                $weappUrl.='&scrmcid='.$material['coupon_id'];
            }
        }


        //小程序和H5分享数据格式
        $shareData['web']   = [
            'msgtype' => 'text',
            'text'    => [
                'content' => $webContent
            ]
        ];
        $shareData['weapp'] = [
            'msgtype'     => 'miniprogram',
            'miniprogram' => [
                'appid'  => $config['weapp_appid'],
                'title'  => $materialName,
                'imgUrl' => $materialUrl,
                'page'   => $weappUrl
            ]
        ];

        //更新信息
        $relationship->short_flag  = isset($shortUrl) ? $shortUrl : '';
        $material['short_web_url'] = $webUrl;
        $material['share_id']      = $shareId;
        $material['share_data']    = $shareData;
        $material['weapp_name']    = isset($config['weapp_name']) && !empty($config['weapp_name']) ? $config['weapp_name'] : '';
        $relationship->ext_json    = json_encode($material);
        $relationship->update();

        return ['share_id' => $shareId, 'web_url' => $oldWebUrl, 'share_data' => $shareData];
    }

    /**
     * 更新分享id信息
     * @url  http://{host_name}/api/shop-material/update-share
     *
     * @throws InvalidDataException
     */
    public function actionUpdateShare()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $shareId = \Yii::$app->request->post('share_id', 0);
        //发送类型
        $sendType = \Yii::$app->request->post('send_type', '');
        if (empty($sendType)) {
            throw new InvalidDataException('缺少发送类型参数！');
        }
        //发送渠道
        $channel = \Yii::$app->request->post('channel', '0');
        if (empty($channel) || !in_array($channel, [1, 2])) {
            throw new InvalidDataException('缺少发送渠道参数！');
        }
        //接收人
        $chatIdStr = \Yii::$app->request->post('chat_id', '');
        if (empty($chatIdStr)) {
            throw new InvalidDataException('缺少接收人参数！');
        }
        $chatId = 0;
        if ($channel == 1) {
            $sendObj = WorkExternalContact::findOne(['corp_id' => $this->corp->id, 'external_userid' => $chatIdStr]);
            $chatId  = $sendObj['id'];
        } else if ($channel == 2) {
            $sendObj = WorkChat::findOne(['corp_id' => $this->corp->id, 'chat_id' => $chatIdStr]);
            $chatId  = $sendObj['id'];
        }


        //获取当前发送方的
        $saveData     = [
            'type'    => $sendType,
            'channel' => $channel,
            'chat_id' => $chatId
        ];
        $relationship = ShopMaterialSourceRelationship::findOne(['id' => $shareId]);
        if (empty($relationship)) {
            throw new InvalidDataException('该分享不存在！');
        }
        $relationship->setAttributes($saveData);
        $relationship->update();
        return ['result' => 1];
    }

    /**
     * 分享明细列表
     * @url  http://{host_name}/api/shop-material/share-detail
     *
     * @throws InvalidDataException
     */
    public function actionShareDetail()
    {
        $post     = \Yii::$app->request->post();
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;

        $relationModel    = $this->shareFilterRequest();
        $count            = $relationModel->count();
        $list             = $relationModel->with(['user' => function ($query) {
            return $query->select('id,name');
        }])->limit($pageSize)->offset($offset)->orderBy(['id' => SORT_DESC])->asArray()->all();
        $materialTypeList = [0 => '未知', 1 => '商品', 2 => '页面', 3 => '优惠券'];
        $typeNameList     = [0 => '未知', 1 => 'H5', 2 => '小程序'];
        $channelNameList  = [0 => '未知', 1 => '好友', 2 => '群'];
        $result           = [];
        if (!empty($list)) {
            foreach ($list as $k => $re) {
                $result[$k]['key']          = $re['id'];
                $result[$k]['send_time']    = $re['send_time'];
                $result[$k]['send_user']    = (isset($re['user']['name']) ? $re['user']['name'] : '暂无') . '(' . $re['user_id'] . ')';
                $result[$k]['channel_name'] = $channelNameList[$re['channel']];
                //顾客或者群昵称
                $result[$k]['chat_name']          = ShopMaterialSourceRelationship::getChatName($this->corp->id, $re['channel'], $re['chat_id']);
                $result[$k]['type']               = $re['type'];
                $result[$k]['type_name']          = isset($typeNameList[$re['type']]) ? $typeNameList[$re['type']] : '';
                $result[$k]['material_type']      = $re['material_type'];
                $result[$k]['material_type_name'] = isset($materialTypeList[$re['material_type']]) ? $materialTypeList[$re['material_type']] : '';
                $result[$k]['material_id']        = $re['material_id'];
                $result[$k]['ext_json']           = json_decode($re['ext_json'], true);
                $result[$k]['review_count']       = intval($re['review_count']);
                //统计数据
                //1、分享类型：分享优惠券
                if ($re['material_type'] == ShopMaterialSourceRelationship::MATERIAL_TYPE_COUPON) {
                    $orderDetail = ShopThirdOrderCoupon::find()->alias('s')
                        ->select(new Expression('count(distinct c.cus_id) as person_num,count(c.order_no) as order_num,sum(c.payment_amount) as account'))
                        ->leftJoin('{{%shop_third_order}} o', 'o.id = s.third_order_id')
                        ->leftJoin('{{%shop_customer_order}} c', 'o.order_no = c.order_no')
                        ->where(['s.coupon_share_id' => $re['id']])->asArray()->one();
                } //2、分享类型：分享商品或者页面
                else {
                    $orderDetail = ShopCustomerOrder::find()
                        ->select(new Expression('count(distinct cus_id) as person_num,count(order_no) as order_num,sum(payment_amount) as account'))
                        ->where(['order_share_id' => $re['id']])->asArray()->one();
                }
                $result[$k]['order_num']  = !empty($orderDetail['order_num']) ? $orderDetail['order_num'] : 0;
                $result[$k]['person_num'] = !empty($orderDetail['person_num']) ? $orderDetail['person_num'] : 0;
                $result[$k]['account']    = !empty($orderDetail['account']) ? $orderDetail['account'] : 0;
            }
        }
        //分享列表
        return [
            'where'  => $post,
            'count'  => $count,
            'result' => $result
        ];
    }

    /**
     * 分享明细统计数据接口
     * @url  http://{host_name}/api/shop-material/share-account
     *
     * @throws InvalidDataException
     */
    public function actionShareAccount()
    {
        $relationModel = $this->shareFilterRequest();
        //统计总金额
        $account  = [
            'send_count' => 0,
            'order_num'  => 0,
            'rate'       => 0,
            'person_num' => 0,
            'account'    => 0
        ];
        $allShare = $relationModel->select('id,material_type')->asArray()->all();
        if (!empty($allShare)) {
            $count = count($allShare);
            foreach ($allShare as $sd) {
                if ($sd['material_type'] == ShopMaterialSourceRelationship::MATERIAL_TYPE_COUPON) {
                    $cShareId[] = $sd['id'];
                } else {
                    $oShareId[] = $sd['id'];
                }
            }
            //统计优惠券对应的订单号
            $orderNoList = [];
            if (!empty($cShareId)) {
                $orderDetail = ShopThirdOrderCoupon::find()->alias('s')
                    ->select('o.order_no')
                    ->leftJoin('{{%shop_third_order}} o', 'o.id = s.third_order_id')
                    ->where(['s.coupon_share_id' => $cShareId])->asArray()->all();
                if (!empty($orderDetail)) {
                    $orderNoList = array_column($orderDetail, 'order_no');
                }
            }
            //统计总数据
            $orderAccount = ShopCustomerOrder::find()
                ->select(new Expression('count(distinct cus_id) as person_num,count(order_no) as order_num,sum(payment_amount) as account'))
                ->where(['corp_id' => $this->corp->id]);
            if (!empty($orderNoList) && !empty($oShareId)) {
                $orderAccount->andWhere(['or', ["order_no" => $orderNoList], ["order_share_id" => $oShareId]]);
            }
            if (empty($orderNoList) && !empty($oShareId)) {
                $orderAccount->andWhere(["order_share_id" => $oShareId]);
            }
            if (!empty($orderNoList) && empty($oShareId)) {
                $orderAccount->andWhere(["order_no" => $orderNoList]);
            }
            $orderAccount = $orderAccount->asArray()->one();
            if (!empty($orderAccount)) {
                $account['send_count'] = intval($count);
                $account['person_num'] = intval($orderAccount['person_num']);
                $account['order_num']  = intval($orderAccount['order_num']);
                $account['account']    = intval($orderAccount['account']);
                $account['rate']       = $count > 0 ? round($orderAccount['order_num'] * 100 / $count) : 0;
            }
        }
        return [
            'result' => $account
        ];
    }

    //处理分享明细接收参数逻辑
    private function shareFilterRequest()
    {
        $startTime    = \Yii::$app->request->post('start_time', 0);
        $endTime      = \Yii::$app->request->post('end_time', 0);
        $chatId       = \Yii::$app->request->post('chat_id', 0);
        $guideKey     = \Yii::$app->request->post('guide_key', 0);
        $materialType = \Yii::$app->request->post('material_type', 0);
        $materialId   = \Yii::$app->request->post('material_id', 0);

        $relationModel = ShopMaterialSourceRelationship::find()
            ->where(['corp_id' => $this->corp->id])
            ->andWhere(['IN', 'channel', [1, 2]]); //1，2确认分享出去的 0为未分享出去
        if (!empty($startTime) && !empty($endTime)) {
            $relationModel = $relationModel->andWhere(['between', 'send_time', $startTime . ' 00:00:00', $endTime . ' 23:59:59']);
        }
        if (!empty($chatId)) {
            $chatId        = explode(',', $chatId);
            $relationModel = $relationModel->andWhere(['chat_id' => $chatId]);
        }
        if (!empty($materialType)) {
            $relationModel = $relationModel->andWhere(['material_type' => $materialType]);
        }
        if (!empty($materialId)) {
            $relationModel = $relationModel->andWhere(['material_id' => $materialId]);
        }
        if (!empty($guideKey)) {
            $userIdList   = [];
            $workUserList = WorkUser::find()->select('id')->where(['corp_id' => $this->corp->id])
                ->andWhere(['or', ['like', 'name', $guideKey], ['like', 'mobile', $guideKey]])->asArray()->all();
            if (!empty($workUserList)) {
                foreach ($workUserList as $wu) {
                    $userIdList[] = $wu['id'];
                }
            }
            $relationModel = $relationModel->andWhere(['user_id' => $userIdList]);
        }
        return $relationModel;
    }

    //生成随机不重复短地址
    private function randShortUrl()
    {
        $code = 'abcdefghijklmnopqrstuvwxyz';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789abcdefghijklmnopqrstuv',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;
        return $d;
    }
}