<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_chat_way_list}}".
	 *
	 * @property int                $id
	 * @property int                $way_id      群聊活码ID
	 * @property int                $chat_id     群列表id
	 * @property int                $tag_pull_id 标签拉群的id
	 * @property int                $chat_way_name 群活码名称
	 * @property int                $limit       上限
	 * @property int                $total       群总共人数
	 * @property int                $add_num     当前群聊人数
	 * @property int                $media_id    图片的id对应attachment的id
	 * @property string             $local_path  二维码图片本地地址
	 * @property string             $create_time 创建时间
	 * @property int                $status      0：禁用；1：启用
	 * @property int                $is_del      0：未删除；1：已删除
	 * @property int                $sort        排序
	 * @property int                $chat_status 0未开始1拉人中2已满群
	 *
	 * @property WorkChatContactWay $way
	 */
	class WorkChatWayList extends \yii\db\ActiveRecord
	{
		// 0未开始1拉人中2已满群
		const NO_OPEN_WAY = 0;
		const DOING_WAY = 1;
		const CLOSE_WAY = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_way_list}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['way_id', 'chat_id', 'limit', 'status', 'is_del', 'tag_pull_id'], 'integer'],
				[['local_path', 'chat_way_name'], 'string'],
				[['create_time'], 'safe'],
				[['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChatContactWay::className(), 'targetAttribute' => ['way_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => 'ID',
				'way_id'        => 'Way ID',
				'chat_id'       => 'Chat ID',
				'chat_way_name' => 'Chat Way Name',
				'limit'         => 'Limit',
				'total'         => 'Total',
				'add_num'       => 'Add Num',
				'local_path'    => 'Local Path',
				'create_time'   => 'Create Time',
				'status'        => 'Status',
				'is_del'        => 'Is Del',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWay ()
		{
			return $this->hasOne(WorkChatContactWay::className(), ['id' => 'way_id']);
		}

		/**
		 * @param $way_id
		 * @param $updateStatus
		 *
		 * @return mixed|string
		 *
		 */
		public static function getImage ($way_id, $updateStatus = 0)
		{
			$mediaId = [
				'mediaId'  => '',
				'way_list' => 0,
				'chat_id'  => 0
			];
			$list = static::find()->where(['way_id' => $way_id, 'is_del' => 0, 'status' => 1, 'chat_status' => self::NO_OPEN_WAY])->orderBy(['sort' => SORT_ASC])->asArray()->all();
			if (!empty($list)) {
				$id  = $list[0]['id'];
				$way = WorkChatWayList::findOne($id);
				if ($updateStatus == 1 && $way->limit == 1) {
					$way->chat_status = 2;
				} else {
					$way->chat_status = 1;
				}
				$way->save();
				$mediaId['way_list'] = $id;
				$mediaId['chat_id']  = !empty($way->chat_id) ? $way->chat_id : 0;
				$mediaId['mediaId']  = $list[0]['media_id'];
			}

			return $mediaId;
		}
	}
