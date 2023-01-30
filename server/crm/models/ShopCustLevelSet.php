<?php


namespace app\models;

use Yii;
use app\util\SUtils;
use app\components\InvalidDataException;
use app\components\Invalid;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use function foo\func;

/**
 * This is the model class for table "{{%shop_customer_level_setting}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property title $title 等级名称
 * @property string $desc 等级描述
 * @property int $weight 权重（值越大等级越高）
 * @property string $color 等级颜⾊值
 * @property int $sort 排序
 * @property string|null $add_time 入库时间
 * @property string|null $update_time 更新时间
 */
class ShopCustLevelSet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_level_setting}}';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'weight', 'corp_id'], 'required', 'message' => '{attribute}不能为空'],
            ['title', 'unique', 'targetAttribute' => ['corp_id', 'title'], 'message' => '等级名称不能重复!'],
            ['weight', 'unique', 'targetAttribute' => ['corp_id', 'weight'], 'message' => '权重值不能重复!'],
            [['corp_id', 'sort', 'weight'], 'integer', 'message' => '{attribute}必须为整型'],
            [['title'], 'string', 'max' => 10, 'tooLong' => '{attribute}字符长度须小于100'],
            [['color'], 'string', 'max' => 10, 'tooLong' => '{attribute}字符长度须小于10'],
            [['desc'], 'string', 'message' => '{attribute}必须为字符串'],
            [['add_time', 'update_time'], 'safe'],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', '等级id'),
            'corp_id'     => Yii::t('app', '授权的企业ID'),
            'title'       => Yii::t('app', '等级名称'),
            'desc'        => Yii::t('app', '等级描述'),
            'weight'      => Yii::t('app', '权重'),
            'color'       => Yii::t('app', '等级颜⾊值'),
            'sort'        => Yii::t('app', '排序'),
            'add_time'    => Yii::t('app', '入库时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }


    //获取等级列表
    public static function getData($corp_id)
    {
        $cacheKey = 'shop_customer_level_setting_' . $corp_id;
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corp_id) {
            return self::find()->where(['corp_id' => $corp_id])->orderBy(['sort' => 'desc'])->asArray()->all();
        }, null, new TagDependency(['tags' => $cacheKey]));
    }

    //添加等级
    public static function addLevel($data, $operator_uid)
    {
        $level = new ShopCustLevelSet();
        foreach ($data as $k => $v) {
            $level->$k = $v;
        }
        if (!$level->validate() || !$level->save()) {
            throw new InvalidDataException(SUtils::modelError($level));
        }
        //清除缓存
        $cacheKey = 'shop_customer_level_setting_' . $level->corp_id;
        TagDependency::invalidate(\Yii::$app->cache, $cacheKey);
        //记录日志
        $logData = [
            'corp_id'        => $level->corp_id,
            'table_name'     => self::tableName(),
            'fields_name'    => 'id',
            'primary_key_id' => $level->id,
            'old_value'      => 0,
            'new_value'      => $level->id,
            'operator_uid'   => $operator_uid,
            'remarks'        => 'insert',
        ];
        ShopOperationLog::addLog($logData);
        return $level->id;
    }

    //修改等级
    public static function UpdateLevel($data, $operation_id)
    {
        $level                = new  ShopCustLevelSet();
        $level->oldAttributes = $level->findOne($data['id']);
        if (empty($level->oldAttributes)) {
            throw new InvalidDataException('该等级记录不存在！');
        }
        $oldAttributes = clone $level->oldAttributes;
        foreach ($data as $k => $v) {
            if ($level->hasAttribute($k)) {
                $level->$k = $v;
            }
        }
        if (!$level->validate()) {
            throw new InvalidDataException(SUtils::modelError($level));
        }
        $re = $level->update();
        //记录操作日志
        if ($re) {
            //清除缓存
            $cacheKey = 'shop_customer_level_setting_' . $oldAttributes->corp_id;
            TagDependency::invalidate(\Yii::$app->cache, $cacheKey);
            foreach ($data as $k => $v) {
                if ($oldAttributes->$k != $level->$k) {
                    $operator_log = [
                        'corp_id'        => $oldAttributes->corp_id,
                        'table_name'     => self::tableName(),
                        'fields_name'    => $k,
                        'primary_key_id' => $data['id'],
                        'old_value'      => $oldAttributes->$k,
                        'new_value'      => $v,
                        'operator_uid'   => $operation_id,
                        'remarks'        => 'update',
                    ];
                    ShopOperationLog::addLog($operator_log);
                }
            }
        }
        return ['result' => $re];
    }

    //删除等级
    public static function deleteLevel($id, $operator_uid)
    {
        $level = new  ShopCustLevelSet();
        $level = $level->findOne($id);
        if (!$level) {
            throw new InvalidDataException('该等级记录不存在');
        }
        $re = $level->delete();
        if ($re) {
            //清除缓存
            $cacheKey = 'shop_customer_level_setting_' . $level->corp_id;
            TagDependency::invalidate(\Yii::$app->cache, $cacheKey);
            //记录日志
            $operator_log = [
                'corp_id'        => $level->corp_id,
                'table_name'     => self::tableName(),
                'fields_name'    => 'id',
                'primary_key_id' => $id,
                'old_value'      => $id,
                'new_value'      => 0,
                'operator_uid'   => $operator_uid,
                'remarks'        => 'delete',
            ];
            ShopOperationLog::addLog($operator_log);
        }
        return ['result' => $re];
    }


}