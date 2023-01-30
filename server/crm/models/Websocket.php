<?php

	namespace app\models;

	use yii\redis\ActiveRecord;

	/**
	 * This is the model class for table "{{%user_profile}}".
	 *
	 * @property int    $id
	 * @property int    $uid         用户名称
	 * @property int    $subId       子账户名称
	 * @property string $openid      H5访问用户的openid
	 * @property string $session_id  浏览器Session ID
	 * @property int    $bind_type   绑定类型
	 * @property int    $created_at  更新时间
	 * @property int    $updated_at  创建时间
	 */
	class Websocket extends ActiveRecord
	{
		const PC_BIND = 1;
		const PC_APP_BIND = 2;
		const MOBILE_APP_BIND = 3;

		/**
		 * {@inheritDoc}
		 * 获取 Redis 配置
		 *
		 * @return object|\yii\redis\Connection|null
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return \Yii::$app->get('websocketRedis');
		}

		/**
		 * 主键 默认为 id
		 *
		 * @return array|string[]
		 */
		public static function primaryKey ()
		{
			return ['id'];
		}

		/**
		 * LiveRoom 属性列表
		 *
		 * @return array
		 */
		public function attributes ()
		{
			return ['id', 'uid', 'subId', 'openid', 'session_id', 'bind_type', 'created_at', 'updated_at'];
		}
	}