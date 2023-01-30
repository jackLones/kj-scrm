<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%menu_action}}".
	 *
	 * @property int    $id
	 * @property string $model        模块名
	 * @property string $control      控制器名
	 * @property string $action       方法名
	 * @property int    $method       1：get、2：post、3：ajax(get)、4：ajax(post)、5：内部调用、6：其他
	 * @property int    $status       是否启用，0：不启用、1：启用
	 * @property string $introduction 介绍
	 * @property string $update_time  修改时间
	 * @property string $create_time  创建时间
	 */
	class MenuAction extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%menu_action}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['method', 'status'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['model', 'control', 'action'], 'string', 'max' => 32],
				[['introduction'], 'string', 'max' => 250],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'model'        => Yii::t('app', '模块名'),
				'control'      => Yii::t('app', '控制器名'),
				'action'       => Yii::t('app', '方法名'),
				'method'       => Yii::t('app', '1：get、2：post、3：ajax(get)、4：ajax(post)、5：内部调用、6：其他'),
				'status'       => Yii::t('app', '是否启用，0：不启用、1：启用'),
				'introduction' => Yii::t('app', '介绍'),
				'update_time'  => Yii::t('app', '修改时间'),
				'create_time'  => Yii::t('app', '创建时间'),
			];
		}

		public static function setAction ($data)
		{
			$menuModels     = trim($data['menuModels']);
			$menuController = trim($data['menuController']);
			$menuAction     = trim($data['menuAction']);
			$requestId      = trim($data['requestId']);
			$introduction   = trim($data['introduction']);
			$ActionId       = trim($data['ActionId']);

			if (!$menuModels || !$menuController || !$menuAction || !$requestId || !$introduction) {
				throw new InvalidDataException('数据填写不全');
			}
			$actionInfo = static::find()->where(['action' => $menuAction, 'control' => $menuController, 'model' => $menuModels, 'method' => $requestId, 'status' => 1]);
			if (!empty($ActionId)) {
				$actionInfo = $actionInfo->andWhere(['<>', 'id', $ActionId]);
			}
			$actionInfo = $actionInfo->one();
			if (!empty($actionInfo)) {
				throw new InvalidDataException('方法名称已经存在');
			}
			if (!empty($ActionId)) {
				$model              = static::findOne($ActionId);
				$model->update_time = DateUtil::getCurrentTime();
			} else {
				$model              = new MenuAction();
				$model->create_time = DateUtil::getCurrentTime();
			}
			$model->model        = $menuModels;
			$model->control      = $menuController;
			$model->action       = $menuAction;
			$model->method       = $requestId;
			$model->status       = 1;
			$model->introduction = $introduction;
			if (!$model->save()) {
				throw new InvalidDataException(SUtils::modelError($model));
			}

			return ['error' => 0, 'msg' => ''];
		}
	}
