<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_public_activity_poster_config}}".
 *
 * @property int $id
 * @property int $activity_id 活动id
 * @property int $is_heard 是否使用头像
 * @property string $heard_width 头像宽
 * @property string $heard_height 头像高
 * @property int $heard_type 1正方形2圆形
 * @property string $heard_top 头像距离顶部
 * @property string $heard_left 头像距离左边
 * @property string $heard_ratio 头像比例
 * @property string $code_width 二维码宽
 * @property string $code_height 二维码高
 * @property string $code_top 二维码距离顶部
 * @property string $code_left 二维码距离左边
 * @property string $code_ratio 二维码比列
 * @property int $is_font 是否使用名称
 * @property string $font_top 字体距离顶部
 * @property string $font_left 字体距离左边
 * @property string $font_size 字体大小
 * @property string $font_color 字体颜色
 * @property string $background_url 背景地址
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
 *
 * @property WorkPublicActivity $activity
 */
class WorkPublicActivityPosterConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_public_activity_poster_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity_id', 'is_heard', 'heard_type', 'is_font', 'create_time', 'update_time'], 'integer'],
            [['heard_width', 'heard_height', 'heard_top', 'heard_left', 'heard_ratio', 'code_width', 'code_height', 'code_top', 'code_left', 'code_ratio', 'font_top', 'font_left', 'font_size'], 'number'],
            [['heard_ratio', 'code_ratio', 'font_color', 'background_url', 'create_time'], 'required'],
            [['font_color'], 'string', 'max' => 30],
            [['background_url'], 'string', 'max' => 255],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'activity_id' => Yii::t('app', '活动id'),
            'is_heard' => Yii::t('app', '是否使用头像'),
            'heard_width' => Yii::t('app', '头像宽'),
            'heard_height' => Yii::t('app', '头像高'),
            'heard_type' => Yii::t('app', '1正方形2圆形'),
            'heard_top' => Yii::t('app', '头像距离顶部'),
            'heard_left' => Yii::t('app', '头像距离左边'),
            'heard_ratio' => Yii::t('app', '头像比例'),
            'code_width' => Yii::t('app', '二维码宽'),
            'code_height' => Yii::t('app', '二维码高'),
            'code_top' => Yii::t('app', '二维码距离顶部'),
            'code_left' => Yii::t('app', '二维码距离左边'),
            'code_ratio' => Yii::t('app', '二维码比列'),
            'is_font' => Yii::t('app', '是否使用名称'),
            'font_top' => Yii::t('app', '字体距离顶部'),
            'font_left' => Yii::t('app', '字体距离左边'),
            'font_size' => Yii::t('app', '字体大小'),
            'font_color' => Yii::t('app', '字体颜色'),
            'background_url' => Yii::t('app', '背景地址'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }

	/**
	 *
	 * @return object|\yii\db\Connection|null
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getDb ()
	{
		return Yii::$app->get('mdb');
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(WorkPublicActivity::className(), ['id' => 'activity_id']);
    }

	public static function getPosterConfig($activity_id)
	{
		return self::find()->where(["activity_id"=>$activity_id])->asArray()->one();
	}
}
