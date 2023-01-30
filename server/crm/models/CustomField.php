<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%custom_field}}".
	 *
	 * @property int    $id
	 * @property string $key         字段名称  ps:英文名称用于input下的name
	 * @property string $title       字段标题 ps:中文标题用于告诉用户字段用处
	 * @property string $desc        字段备注
	 * @property int    $type        字段类型 （1文本 2单选 3多选 4日期 5手机号 6邮箱 7区域）
	 * @property int    $is_define   是否商户自定义 1是0否
	 * @property int    $uid         自定义商户的uid
	 * @property int    $updatetime  修改时间
	 * @property int    $createtime  创建时间
	 * @property int    $status      0关闭，1开启，2删除
	 * @property int    $chat_status 群属性状态0关闭，1开启(仅自定义属性)
	 * @property int    $sort        排序值
	 */
	class CustomField extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%custom_field}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['key', 'title', 'type', 'updatetime', 'createtime'], 'required'],
				[['type', 'is_define', 'uid', 'updatetime', 'createtime', 'status', 'sort'], 'integer'],
				[['key'], 'string', 'max' => 50],
				[['title'], 'string', 'max' => 64],
				[['desc'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => Yii::t('app', 'ID'),
				'key'        => Yii::t('app', '字段名称  ps:英文名称用于input下的name'),
				'title'      => Yii::t('app', '字段标题 ps:中文标题用于告诉用户字段用处'),
				'desc'       => Yii::t('app', ' 字段备注'),
				'type'       => Yii::t('app', '字段类型 （1文本 2单选 3多选 4日期 5手机号 6邮箱 7区域）'),
				'is_define'  => Yii::t('app', '是否商户自定义 1是0否'),
				'uid'        => Yii::t('app', '自定义商户的uid'),
				'updatetime' => Yii::t('app', ' 修改时间'),
				'createtime' => Yii::t('app', ' 创建时间'),
				'status'     => Yii::t('app', '0关闭，1开启，2删除'),
				'chat_status'=> Yii::t('app', '群属性状态0关闭，1开启'),
				'sort'       => Yii::t('app', '排序值'),
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
		 * 设置默认高级属性字段
		 */
		public static function setField ($pdata)
		{
			$id = intval($pdata['id']);
			if (empty($id)) {
				$key   = trim($pdata['namekey']);
				$title = trim($pdata['title']);
				//查询此字段名是否已存在
				$hasField = static::find()->where('`is_define`=0 AND (`key`=\'' . $key . '\' OR `title`=\'' . $title . '\') AND `status`!=2')->all();
				if (!empty($hasField)) {
					throw new InvalidDataException('此字段名:' . $key . '或名称:' . $title . '已存在,请更换！');
				}

				$field             = new CustomField();
				$field->key        = $key;
				$field->title      = $title;
				$field->type       = $pdata['type'];
				$field->is_define  = 0;
				$field->createtime = time();
				$field->updatetime = time();
			} else {
				$field             = static:: findOne($id);
				$field->updatetime = time();
			}
			$field->status = $pdata['status'];

			if (!$field->save()) {
				throw new InvalidDataException(SUtils::modelError($field));
			}

			//添加属性值
			if (empty($id)) {
				if (!empty($pdata['default_val'])) {
					$val    = trim($pdata['default_val']);
					$varArr = explode("\r\n", $val);
					$varArr = array_unique($varArr);
					$i      = 0;
					foreach ($varArr as $k => $v) {
						$v = trim($v);
						if (!empty($v)) {
							$fieldOption          = new CustomFieldOption();
							$fieldOption->fieldid = $field->id;
							$fieldOption->value   = $i + 1;
							$fieldOption->match   = $v;
							if (!$fieldOption->save()) {
								throw new InvalidDataException(SUtils::modelError($fieldOption));
							}

							$i++;
						}
					}
				}
			} else {
				$val = trim($pdata['default_val_extend']);
				if (!empty($val) || strlen($val) > 0) {
					$option = CustomFieldOption::find()->where(['fieldid' => $field->id])->asArray()->all();

					$optionArr = [];
					$valueArr  = [];
					if (!empty($option)) {
						foreach ($option as $o) {
							array_push($optionArr, $o['match']);
							array_push($valueArr, $o['value']);
						}
					}
					$varArr = explode("\r\n", $val);
					$varArr = array_unique($varArr);
					$temp   = [];
					foreach ($varArr as $k => $v) {
						$v = trim($v);
						if (!empty($v) || strlen($v) > 0) {
							array_push($temp, $v);
						}
					}
					$varArr = array_diff($temp, $optionArr);
					if (!empty($valueArr)) {
						$valueMax = max($valueArr);
					} else {
						$valueMax = 0;
					}
					$i = 0;
					foreach ($varArr as $k => $v) {
						$fieldOption          = new CustomFieldOption();
						$fieldOption->fieldid = $field->id;
						$fieldOption->value   = $valueMax + $i + 1;//修改时要取原来的最大值
						$fieldOption->match   = trim($v);
						if (!$fieldOption->save()) {
							throw new InvalidDataException(SUtils::modelError($fieldOption));
						}

						$i++;
					}
				}
			}

			return ['error' => 0, 'msg' => ''];
		}

		/**
		 * 用户设置自定义属性字段
		 */
		public static function UserSetField ($pdata)
		{
			$id        = isset($pdata['id']) ? intval($pdata['id']) : 0;
			$uid       = $pdata['uid'];
			$title     = trim($pdata['title']);
			$type      = $pdata['type'];
			$optionVal = trim($pdata['optionVal']);
			$status    = isset($pdata['status']) ? $pdata['status'] : 0;
			$chat_status = isset($pdata['chat_status']) ? $pdata['chat_status'] : 0;
			$sort      = isset($pdata['sort']) ? $pdata['sort'] : 0;

			if (empty($uid) || empty($type)) {
				throw new InvalidDataException('缺少必要参数！');
			}
			if (empty($title)) {
				throw new InvalidDataException('请填写字段名称！');
			}
			if (in_array($type, [2, 3]) && empty($optionVal)) {
				throw new InvalidDataException('选项内容不能为空！');
			}
			if (empty($id)) {
				$lastField = static::find()->where(['uid' => $uid])->select('key')->orderBy('id DESC')->one();
				if (!empty($lastField['key'])) {
					$fieldKey = explode('_', $lastField['key']);
					$key      = $uid . '_' . ($fieldKey[1] + 1);
				} else {
					$key = $uid . '_1';
				}
				//查询此字段名是否已存在
				/*$hasField = static::find()->where('(`uid`=0 AND `title`=\'' . $title . '\' AND `status`=1) OR (`uid`=' . $uid . ' AND (binary `title`=\'' . $title . '\' OR `key`=\'' . $key . '\') AND `status`!=2)')->one();
				if (!empty($hasField)) {
					throw new InvalidDataException('此字段名:' . $title . '或key已存在,请更换！');
				}*/

				$field             = new CustomField();
				$field->key        = $key;
				$field->type       = $type;
				$field->uid        = $uid;
				$field->is_define  = 1;
				$field->createtime = time();
				$field->updatetime = time();
			} else {
				$field             = static:: findOne($id);
				$field->updatetime = time();
			}
			$field->title  = $title;
			$field->status = $status;
			$field->chat_status = $chat_status;
			$field->sort   = $sort;

			if (!$field->save()) {
				throw new InvalidDataException(SUtils::modelError($field));
			}

			//添加属性值
			if (!empty($optionVal) && in_array($type, [2, 3])) {
				if (empty($id)) {
					$varArr = explode(',', $optionVal);
					$varArr = array_unique($varArr);
					$i      = 0;
					foreach ($varArr as $k => $v) {
						$v = trim($v);
						if (!empty($v)) {
							$fieldOption          = new CustomFieldOption();
							$fieldOption->fieldid = $field->id;
							$fieldOption->value   = $i + 1;
							$fieldOption->match   = $v;
							if (!$fieldOption->save()) {
								throw new InvalidDataException(SUtils::modelError($fieldOption));
							}

							$i++;
						}
					}
				} else {
					CustomFieldOption::updateAll(['is_del' => 1], ['fieldid' => $field->id, 'is_del' => 0]);

					//全部属性值
					$option    = CustomFieldOption::find()->where(['fieldid' => $field->id])->asArray()->all();
					$optionArr = [];
					$valueArr  = [];
					if (!empty($option)) {
						foreach ($option as $o) {
							array_push($optionArr, $o['match']);
							array_push($valueArr, $o['value']);
						}
					}
					//提交的属性值
					$optionValArr = explode(',', $optionVal);
					$optionValArr = array_unique($optionValArr);
					$temp         = [];
					foreach ($optionValArr as $k => $v) {
						$v = trim($v);
						if (!empty($v) || strlen($v) > 0) {
							array_push($temp, $v);
						}
					}
					//修改属性值
					$uptOption = array_intersect($temp, $optionArr);
					if ($uptOption) {
						CustomFieldOption::updateAll(['is_del' => 0], ['fieldid' => $field->id, 'match' => $uptOption]);
					}
					//增加属性值
					$addOption = array_diff($temp, $optionArr);
					if (!empty($valueArr)) {
						$valueMax = max($valueArr);
					} else {
						$valueMax = 0;
					}
					$i = 1;
					foreach ($addOption as $k => $v) {
						$fieldOption          = new CustomFieldOption();
						$fieldOption->uid     = $uid;
						$fieldOption->fieldid = $field->id;
						$fieldOption->value   = $valueMax + $i;//修改时要取原来的最大值
						$fieldOption->match   = trim($v);
						if (!$fieldOption->save()) {
							throw new InvalidDataException(SUtils::modelError($fieldOption));
						}

						$i++;
					}
				}
			}

			return ['error' => 0, 'msg' => '', 'fieldid' => $field->id];
		}

		/**
		 * 获取客户的自定义属性详情
		 *
		 * @param     $uid           // 用户id
		 * @param     $cid           // 客户id
		 * @param int $type          // 1客户2粉丝
		 * @param int $unshare_field // 不共享会员画像1是0否
		 * @param int $user_id       // 员工id
		 */
		public static function getCustomField ($uid, $cid, $type = 1, $unshare_field = 0, $user_id = 0)
		{
			$fieldWhere    = '(uid=' . $uid . ' AND status=1)';//自定义属性
			$userField     = CustomFieldUser::find()->where(['uid' => $uid])->select('`fieldid`,`status`,`sort`')->asArray()->all();
			$userFieldSort = [];
			foreach ($userField as $k => $v) {
				$userFieldSort[$v['fieldid']] = $v['sort'];
			}
			if ($type == 3) {
				$chatField    = CustomFieldChat::find()->where(['uid' => $uid])->select('`fieldid`,`status`')->asArray()->all();
				$chatFieldStr = '';
				foreach ($chatField as $k => $v) {
					if ($v['status'] == 1) {
						$chatFieldStr .= $v['fieldid'] . ',';
					}
				}
				$fieldWhere = '(uid=' . $uid . ' AND chat_status=1)';//自定义属性
				if (!empty($chatFieldStr)) {
					$fieldWhere .= ' OR (is_define=0 AND status=1 AND id IN (' . trim($chatFieldStr, ',') . '))';//开启的群默认属性
				} elseif (empty($chatField)) {
					$fieldWhere .= ' OR (is_define=0 AND status=1 AND `key` not in (\'sex\', \'birthday\', \'age\', \'education\', \'income\', \'idCard\'))';//商户没有设置过群默认属性，则默认部分开启
				}
				$fieldList = CustomField::find()->where($fieldWhere)->select('`id` fieldid,`key`,`title`,`type`,`sort`')->orderBy(['sort' => 'desc', 'is_define' => 'asc', 'id' => 'asc'])->asArray()->all();
			} else {
				$userFieldStr = '';
				foreach ($userField as $k => $v) {
					if ($v['status'] == 1) {
						$userFieldStr .= $v['fieldid'] . ',';
					}
				}
				if (!empty($userFieldStr)) {
					$fieldWhere .= ' OR (is_define=0 AND status=1 AND id IN (' . trim($userFieldStr, ',') . '))';//开启的默认属性
				} elseif (empty($userField)) {
					$fieldWhere .= ' OR (is_define=0 AND status=1)';//商户没有设置过默认属性，则默认全部开启
				}
				$fieldWhere .= ' OR `key`=\'sex\'';//性别一直展示
				$fieldList  = CustomField::find()->where($fieldWhere)->andWhere(['!=', 'key', 'offline_source'])->select('`id` fieldid,`key`,`title`,`type`,`sort`')->orderBy(['sort' => 'desc', 'is_define' => 'asc', 'id' => 'asc'])->asArray()->all();
			}

			foreach ($fieldList as $k => $v) {
				//属性值
				$optionVal = '';
				if (in_array($v['type'], [2, 3])) {
					$fieldOption = CustomFieldOption::find()->where(['fieldid' => $v['fieldid'], 'is_del' => 0])->asArray()->all();

					foreach ($fieldOption as $vv) {
						$optionVal .= $vv['match'] . ',';
					}
					$optionVal = trim($optionVal, ',');
				}
				$fieldList[$k]['optionVal'] = $optionVal;
				$fieldList[$k]['sort']      = isset($userFieldSort[$v['fieldid']]) ? $userFieldSort[$v['fieldid']] : $v['sort'];//排序
			}
			//设置的用户属性
			if ($type == 1 && $unshare_field == 1) {
				$fieldWhere       = ' cid=' . $cid . ' and type=' . $type . ' and user_id in (0, ' . $user_id . ') ';
				$fieldSql         = 'SELECT * FROM (SELECT `fieldid`,`value` FROM {{%custom_field_value}} where ' . $fieldWhere . ' ORDER BY user_id desc,time desc,id desc) as v GROUP BY `fieldid`';
				$customFieldValue = CustomFieldValue::findBySql($fieldSql)->asArray()->all();
			} else {
				$customFieldValue = CustomFieldValue::find()->where(['cid' => $cid, 'type' => $type])->andWhere(['!=', 'value', ''])->select('`fieldid`,`value`')->groupBy('fieldid')->asArray()->all();
			}

			$customFieldD     = [];
			foreach ($customFieldValue as $k => $v) {
				$custom = CustomField::findOne($v['fieldid']);
				if (!empty($custom) && $custom->type == 8) {
					if (!empty($v['value'])) {
						$v['value'] = json_decode($v['value'], true);
					} else {
						$v['value'] = [];
					}
				}
				$customFieldD[$v['fieldid']] = $v['value'];
			}

			$user          = User::findOne($uid);
			$is_hide_phone = $user->is_hide_phone;
			foreach ($fieldList as $k => $v) {
				$fieldList[$k]['value'] = isset($customFieldD[$v['fieldid']]) ? $customFieldD[$v['fieldid']] : '';
				if ($is_hide_phone && $fieldList[$k]['key'] == 'phone'){
					$fieldList[$k]['value'] = '';
				}
				if ($v['type'] == 8 && empty($fieldList[$k]['value'])) {
					$fieldList[$k]['value'] = [];
				}
			}
			//排序
			$sort_names = array_column($fieldList, 'sort');
			array_multisort($sort_names, SORT_DESC, $fieldList);

			return $fieldList;
		}

		public static function getDialoutPhone($cid, $uid)
        {
            $result = [];
            $dialoutPhone = '';
            $fieldid = static::find()->where('is_define=0')->select('id')->where(['key'=>'phone'])->asArray()->all();
            if (!empty($fieldid[0]['id'])){
                $fieldid = $fieldid[0]['id'];
                $user_id = [$uid,0];
                $data = CustomFieldValue::find()->select(['id','value phone','if(user_id>0,0,1) is_public'])->where(['type'=>1,'fieldid'=>$fieldid,'cid'=>$cid,'user_id'=>$user_id])->addGroupBy('is_public')->asArray()->all();
                foreach ($data as $value){
                    $phone = explode(',',$value['phone']);
                    $result = array_merge($result, $phone);
                }
            }

            if (!empty($result)) {
                $dialoutPhone = $result[array_rand($result)];
            }
            return $dialoutPhone;
        }
	}
