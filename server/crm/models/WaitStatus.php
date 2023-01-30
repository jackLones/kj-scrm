<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%wait_status}}".
 *
 * @property int    $id
 * @property int    $uid         用户ID
 * @property string $title       待办项目阶段
 * @property string $color       颜色
 * @property string $desc        描述
 * @property int    $sort        排序 越小越靠前
 * @property int    $key         用于前端传的默认字段
 * @property int    $create_time 创建时间
 * @property int    $is_del      是否删除1已删除0未删除
 *
 * @property User   $u
 */
class WaitStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wait_status}}';
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
			'title'       => '待办项目阶段',
			'color'       => '颜色',
			'desc'        => '描述',
			'sort'        => '排序 越小越靠前',
			'key'         => '用于前端传的默认字段',
			'create_time' => '创建时间',
			'is_del'      => '是否删除1已删除0未删除',
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
		$dataResult = self::find()->where(['uid' => $uid, 'is_del' => 0])->all();
		if (!empty($dataResult)) {
			/** @var WaitStatus $res */
			foreach ($dataResult as $res) {
				if (!in_array($res->id, $ids)) {
					$id = $res->id;
					$res->delete();
					$task = WaitCustomerTask::find()->where(['status' => $id])->all();
					if (!empty($task)) {
						$status = WaitStatus::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->one();
						/** @var WaitCustomerTask $ta */
						foreach ($task as $ta) {
							$ta->status     = $status->id;
							$ta->end_time   = 0;
							$ta->start_time = 0;
							$ta->queue_id   = 0;
							$ta->per        = '0';
							$ta->per_desc   = '';
							if (!empty($ta->queue_id)) {
								\Yii::$app->queue->remove($ta->queue_id);
							}
							$ta->queue_id = 0;
							$ta->save();

						}
					}

				}
			}
		}
		$i = 4;
		foreach ($data as $key => $val) {
			$keyId = !empty($val['key']) ? $val['key'] : $key + 1;
			self::addData($uid, $val['title'], $val['id'], $val['color'], $val['desc'], $key + 1, $keyId);
		}

		return true;
	}

	/**
	 * @param $uid
	 * @param $title
	 * @param $id
	 * @param $color
	 * @param $desc
	 * @param $sort
	 * @param $key
	 * @param $type
	 *
	 * @return          {"error":0,"data":[]}
	 *
	 * @return_param    error int 状态码
	 * @return_param    data array 结果数据
	 *
	 * @remark          Create by PhpStorm. User: wangpan. Date: 2020/11/6 10:14
	 * @number          0
	 *
	 * @throws InvalidDataException
	 */
	public static function addData ($uid, $title, $id, $color, $desc, $sort, $key, $type = 0)
	{
		if (empty($id)) {
			$waitStatus = self::findOne(['uid' => $uid, 'title' => $title, 'is_del' => 0]);
		} else {
			$waitStatus = self::findOne(['id' => $id, 'is_del' => 0]);
		}
		if (empty($waitStatus)) {
			$waitStatus              = new WaitStatus();
			$waitStatus->create_time = time();
		}
		$sortNew = $sort;
		if ($type == 1) {
			$status = WaitStatus::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_DESC])->one();
			if (!empty($status)) {
				$sortNew      = $status->sort;
				$status->sort = $sortNew + 1;
				$status->save();
			}
		}
		if ($type == 1 && empty($id)) {
			$sort = $sortNew;
		}
		$waitStatus->uid   = intval($uid);
		$waitStatus->title = $title;
		$waitStatus->color = $color;
		$waitStatus->desc  = $desc;
		$waitStatus->sort  = $sort;
		$waitStatus->key   = $key;
		if (!$waitStatus->validate() || !$waitStatus->save()) {
			throw new InvalidDataException('创建失败：' . SUtils::modelError($waitStatus));
		}
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 *
	 */
	public static function getData ($uid)
	{
		$data       = [];
		$waitStatus = self::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->all();
		if (!empty($waitStatus)) {
			/** @var WaitStatus $val */
			foreach ($waitStatus as $val) {
				array_push($data, $val->dumpData());
			}
		}
		if (empty($data)) {
			$data = [
				[
					'key'    => 1,
					'title' => '待处理',
					'color' => '#FF0000',
					'desc'  => '',
					'sort'  => 1
				],
				[
					'key'    => 2,
					'title' => '处理中',
					'color' => '#00FF00',
					'desc'  => '',
					'sort'  => 2
				],
				[
					'key'    => 3,
					'title' => '已完成',
					'color' => '#5599FF',
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
		$waitStatus = WaitStatus::findOne(['uid' => $uid, 'is_del' => 0]);
		if (empty($waitStatus)) {
			$data = [
				[
					'key'   => 1,
					'title' => '待处理',
					'color' => '#FF0000',
					'desc'  => '',
					'sort'  => 1
				],
				[
					'key'   => 2,
					'title' => '处理中',
					'color' => '#00FF00',
					'desc'  => '',
					'sort'  => 2
				],
				[
					'key'   => 3,
					'title' => '已完成',
					'color' => '#5599FF',
					'desc'  => '',
					'sort'  => 3
				]
			];
			foreach ($data as $dt) {
				$status = WaitStatus::findOne(['uid' => $uid, 'title' => $dt['title'], 'is_del' => 0]);
				if (empty($status)) {
					$status              = new WaitStatus();
					$status->uid         = intval($uid);
					$status->sort        = $dt['sort'];
					$status->title       = $dt['title'];
					$status->color       = $dt['color'];
					$status->desc        = $dt['desc'];
					$status->key         = $dt['key'];
					$status->create_time = time();
					$status->save();
				}
			}
		}

		return true;
	}


}
