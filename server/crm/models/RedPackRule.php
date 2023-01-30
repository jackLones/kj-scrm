<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\DateUtil;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%red_pack_rule}}".
 *
 * @property int $id
 * @property int $uid 账户ID
 * @property string $name 规则名称
 * @property int $type 单个红包金额类型：1、固定金额，2、随机金额
 * @property string $fixed_amount 固定金额
 * @property string $min_random_amount 最小随机金额
 * @property string $max_random_amount 最大随机金额
 * @property string $pic_url 红包封面路径
 * @property string $title 红包标题
 * @property string $des 红包描述
 * @property string $thanking 感谢语
 * @property int $status 状态：0删除、1正常
 * @property string $create_time 创建时间
 * @property string $update_time 修改时间
 *
 * @property User $u
 */
class RedPackRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%red_pack_rule}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'type', 'status'], 'integer'],
            [['fixed_amount', 'min_random_amount', 'max_random_amount'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'title', 'thanking'], 'string', 'max' => 255],
            [['pic_url', 'des'], 'string', 'max' => 500],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', '账户ID'),
            'name' => Yii::t('app', '规则名称'),
            'type' => Yii::t('app', '单个红包金额类型：1、固定金额，2、随机金额'),
            'fixed_amount' => Yii::t('app', '固定金额'),
            'min_random_amount' => Yii::t('app', '最小随机金额'),
            'max_random_amount' => Yii::t('app', '最大随机金额'),
            'pic_url' => Yii::t('app', '红包封面路径'),
            'title' => Yii::t('app', '红包标题'),
            'des' => Yii::t('app', '红包描述'),
            'thanking' => Yii::t('app', '感谢语'),
            'status' => Yii::t('app', '状态：0删除、1正常'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(User::className(), ['uid' => 'uid']);
    }

	//设置红包规则
	public static function setData ($postData)
	{
		$id                = !empty($postData['id']) ? $postData['id'] : 0;
		$uid               = !empty($postData['uid']) ? $postData['uid'] : 0;
		$name              = trim($postData['name']);
		$type              = !empty($postData['type']) ? trim($postData['type']) : 0;
		$fixed_amount      = !empty($postData['fixed_amount']) ? trim($postData['fixed_amount']) : 0;
		$min_random_amount = !empty($postData['min_random_amount']) ? trim($postData['min_random_amount']) : 0;
		$max_random_amount = !empty($postData['max_random_amount']) ? trim($postData['max_random_amount']) : 0;
		$pic_url           = !empty($postData['pic_url']) ? trim($postData['pic_url']) : '';
		$title             = !empty($postData['title']) ? trim($postData['title']) : '';
		$des               = !empty($postData['des']) ? trim($postData['des']) : '';
		$thanking          = !empty($postData['thanking']) ? trim($postData['thanking']) : '';

		if (empty($uid)) {
			throw new InvalidDataException('参数不正确');
		}
		if ($name === '') {
			throw new InvalidDataException('请填写规则名称');
		} elseif (mb_strlen($name, 'utf-8') > 30) {
			throw new InvalidDataException('规则名称最多30个字符');
		}
		if (empty($type)) {
			throw new InvalidDataException('请选择单个红包金额类型');
		}
		if ($type == 1) {
			if (empty($fixed_amount)) {
				throw new InvalidDataException('请填写红包固定金额');
			} elseif ($fixed_amount < 0.3) {
				throw new InvalidDataException('红包固定金额不能小于0.3元');
			} elseif ($fixed_amount > 200) {
				throw new InvalidDataException('红包固定金额不能大于200元');
			}
		}
		if ($type == 2) {
			if (empty($min_random_amount) || empty($max_random_amount)) {
				throw new InvalidDataException('请填写拼手气金额范围');
			}
			if ($min_random_amount < 0.3) {
				throw new InvalidDataException('拼手气最低金额不能小于0.3元');
			} elseif ($min_random_amount > 200) {
				throw new InvalidDataException('拼手气最低金额不能大于200元');
			}
			if ($max_random_amount < 0.3) {
				throw new InvalidDataException('拼手气最高金额不能小于0.3元');
			} elseif ($max_random_amount > 200) {
				throw new InvalidDataException('拼手气最高金额不能大于200元');
			}
			if ($min_random_amount >= $max_random_amount) {
				throw new InvalidDataException('拼手气最低金额需小于最高金额');
			}
		}
		if (empty($pic_url)) {
			throw new InvalidDataException('请选择红包封面');
		}
		if (empty($title)) {
			throw new InvalidDataException('请填写红包标题');
		} elseif (mb_strlen($title, 'utf-8') > 32) {
			throw new InvalidDataException('红包标题最多32个字符');
		}

		if (!empty($id)) {
			$nameInfo = static::find()->andWhere(['uid' => $uid, 'name' => $name, 'status' => 1])->andWhere(['!=', 'id', $id])->one();
			if (!empty($nameInfo)) {
				throw new InvalidDataException('规则名称已经存在，请更改');
			}
			$ruleData = static::findOne($id);
			if (empty($ruleData)) {
				throw new InvalidDataException('红包规则数据错误');
			}
			$ruleData->update_time = DateUtil::getCurrentTime();
		} else {
			//规则名称是否重复
			$nameInfo = static::findOne(['uid' => $uid, 'name' => $name, 'status' => 1]);
			if (!empty($nameInfo)) {
				throw new InvalidDataException('规则名称已经存在，请更改');
			}
			$ruleData              = new RedPackRule();
			$ruleData->create_time = DateUtil::getCurrentTime();
		}
		$ruleData->uid               = $uid;
		$ruleData->name              = $name;
		$ruleData->type              = $type;
		$ruleData->fixed_amount      = $fixed_amount;
		$ruleData->min_random_amount = $min_random_amount;
		$ruleData->max_random_amount = $max_random_amount;
		$ruleData->pic_url           = $pic_url;
		$ruleData->title             = $title;
		$ruleData->des               = $des;
		$ruleData->thanking          = $thanking;
		$ruleData->status            = 1;

		if (!$ruleData->validate() || !$ruleData->save()) {
			throw new InvalidDataException(SUtils::modelError($ruleData));
		}

		return $ruleData->id;
	}
}
