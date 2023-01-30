<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%wait_level}}".
 *
 * @property int    $id
 * @property int    $uid         用户ID
 * @property string $title       优先级名称
 * @property string $color       颜色
 * @property string $desc        优先级描述
 * @property int    $sort        排序 越小越靠前
 * @property int    $key         用于前端传的默认字段
 * @property int    $create_time 创建时间
 *
 * @property User   $u
 */
class WaitLevel extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName ()
	{
		return '{{%wait_level}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules ()
	{
		return [
			[['uid', 'sort', 'key', 'create_time'], 'integer'],
			[['title'], 'string', 'max' => 64],
			[['color'], 'string', 'max' => 32],
			[['desc'], 'string', 'max' => 255],
			[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels ()
	{
		return [
			'id'          => 'ID',
			'uid'         => '用户ID',
			'title'       => '优先级名称',
			'color'       => '颜色',
			'desc'        => '优先级描述',
			'sort'        => '排序 越小越靠前',
			'key'         => '用于前端传的默认字段',
			'create_time' => '创建时间',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getU ()
	{
		return $this->hasOne(User::className(), ['uid' => 'uid']);
	}

	public function dumpData ()
	{
		return [
			'id'    => $this->id,
			'title' => $this->title,
			'color' => $this->color,
			'desc'  => $this->desc,
			'sort'  => $this->sort,
		];
	}

	/**
	 * @param $data
	 * @param $uid
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function add ($data, $uid)
	{
		$ids        = array_column($data, 'id');
		$dataResult = self::find()->where(['uid' => $uid])->all();
		if (!empty($dataResult)) {
			/** @var WaitLevel $res */
			foreach ($dataResult as $res) {
				if (!in_array($res->id, $ids)) {
					$id = $res->id;
					$res->delete();
					$project = WaitProject::find()->where(['level_id' => $id])->all();
					if (!empty($project)) {
						$level = WaitLevel::find()->where(['uid' => $uid])->orderBy(['sort' => SORT_ASC])->one();
						/** @var WaitProject $pro */
						foreach ($project as $pro) {
							$pro->level_id = $level->id;
							$pro->save();
						}
					}
				}
			}
		}
		$i = 5;
		foreach ($data as $key => $val) {
			$keyId = !empty($val['key']) ? $val['key'] : $i++;
			self::addData($val['id'], $uid, $val['title'], $val['color'], $val['desc'], $key + 1, $keyId);
		}

		return true;
	}

	/**
	 * @param $id
	 * @param $uid
	 * @param $title
	 * @param $color
	 * @param $desc
	 * @param $sort
	 * @param $key
	 *
	 * @return          {"error":0,"data":[]}
	 *
	 * @return_param    error int 状态码
	 * @return_param    data array 结果数据
	 *
	 * @remark          Create by PhpStorm. User: wangpan. Date: 2020/11/6 10:34
	 * @number          0
	 *
	 * @throws InvalidDataException
	 */
	public static function addData ($id, $uid, $title, $color, $desc, $sort, $key)
	{
		if (empty($id)) {
			$waitLevel = self::findOne(['uid' => $uid, 'title' => $title]);
		} else {
			$waitLevel = self::findOne($id);
		}
		if (empty($waitLevel)) {
			$waitLevel              = new WaitLevel();
			$waitLevel->create_time = time();
			$waitLevel->key         = $key;
		}
		$waitLevel->uid   = intval($uid);
		$waitLevel->title = $title;
		$waitLevel->color = $color;
		$waitLevel->desc  = $desc;
		$waitLevel->sort  = $sort;
		if (!$waitLevel->validate() || !$waitLevel->save()) {
			throw new InvalidDataException('创建失败：' . SUtils::modelError($waitLevel));
		}
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 */
	public static function getData ($uid)
	{
		$data       = [];
		$waitStatus = self::find()->where(['uid' => $uid])->orderBy(['sort' => SORT_ASC])->all();
		if (!empty($waitStatus)) {
			/** @var WaitLevel $val */
			foreach ($waitStatus as $val) {
				array_push($data, $val->dumpData());
			}
		}
		if (empty($data)) {
			$data = [
				[
					'key'   => 1,
					'title' => '非常重要',
					'color' => '#f85e5e',
					'desc'  => '',
					'sort'  => 1
				],
				[
					'key'   => 2,
					'title' => '重要',
					'color' => '#93c36b',
					'desc'  => '',
					'sort'  => 2
				],
				[
					'key'   => 3,
					'title' => '一般',
					'color' => '#97afd0',
					'desc'  => '',
					'sort'  => 3
				]
			];
		}

		return $data;
	}

	/**
	 * @param $uid
	 *
	 * @return bool
	 *
	 */
	public static function defaultData ($uid)
	{
		$waitLevel = WaitLevel::findOne(['uid' => $uid]);
		if (empty($waitLevel)) {
			$data = [
				[
					'key'   => 1,
					'title' => '非常重要',
					'color' => '#f85e5e',
					'desc'  => '',
					'sort'  => 1
				],
				[
					'key'   => 2,
					'title' => '重要',
					'color' => '#93c36b',
					'desc'  => '',
					'sort'  => 2
				],
				[
					'key'   => 3,
					'title' => '一般',
					'color' => '#97afd0',
					'desc'  => '',
					'sort'  => 3
				]
			];
			foreach ($data as $dt) {
				$level = WaitLevel::findOne(['uid' => $uid, 'title' => $dt['title']]);
				if (empty($level)) {
					$level              = new WaitLevel();
					$level->uid         = intval($uid);
					$level->sort        = $dt['sort'];
					$level->title       = $dt['title'];
					$level->color       = $dt['color'];
					$level->desc        = $dt['desc'];
					$level->key         = $dt['key'];
					$level->create_time = time();
					$level->save();
				}
			}
		}

		return true;
	}


}
