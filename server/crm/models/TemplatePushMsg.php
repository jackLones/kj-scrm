<?php

	namespace app\models;

	use Yii;
	use app\util\DateUtil;
	use app\components\InvalidParameterException;
	use yii\helpers\Json;
	use callmez\wechat\sdk\Wechat;

	/**
	 * This is the model class for table "{{%template_push_msg}}".
	 *
	 * @property int                $id
	 * @property int                $author_id        公众号ID
	 * @property string             $msg_title        消息名称
	 * @property int                $template_id      模板消息ID
	 * @property string             $template_data    模板消息的data（json格式）
	 * @property string             $template_content 模板内容
	 * @property int                $redirect_type    跳转类型，1：链接、2：小程序
	 * @property string             $url              模板跳转链接（海外帐号没有跳转能力）
	 * @property string             $appid            所需跳转到的小程序appid（该小程序appid必须与发模板消息的公众号是绑定关联关系，暂不支持小游戏）
	 * @property string             $pagepath         所需跳转到小程序的具体页面路径，支持带参数,（示例index?foo=bar），要求该小程序已发布，暂不支持小游戏
	 * @property int                $push_type        发送类别：1：全部粉丝、2：标签、3：性别、4：自定义
	 * @property string             $push_rule        发送条件（json格式）
	 * @property string             $push_time        发送时间
	 * @property string             $create_time      创建时间
	 * @property int                $fans_num         发送成功粉丝数
	 * @property int                $will_fans_num    预计发生粉丝数
	 * @property int                $status           状态 0未发送 1已发送 2发送失败
	 * @property int                $queue_id         队列id
	 * @property string             $msg_id           消息id，多个逗号隔开
	 * @property int                $error_code       错误码
	 * @property string             $error_msg        错误信息
	 *
	 * @property TemplatePushInfo[] $templatePushInfos
	 * @property WxAuthorize        $author
	 * @property Template           $template
	 */
	class TemplatePushMsg extends \yii\db\ActiveRecord
	{
		const LINK_REDIRECT = 1;
		const MINIAPP_REDIRECT = 2;

		const PUSH_TO_ALL = 1;
		const PUSH_BY_TAGS = 2;
		const PUSH_BY_SEX = 3;
		const PUSH_BY_CUSTOM = 4;

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
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%template_push_msg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'template_id', 'redirect_type', 'push_type', 'fans_num', 'status', 'queue_id', 'error_code'], 'integer'],
				[['template_data', 'template_content', 'url', 'push_rule', 'error_msg'], 'string'],
				[['push_time', 'create_time'], 'safe'],
				[['msg_title'], 'string', 'max' => 32],
				[['appid'], 'string', 'max' => 64],
				[['pagepath'], 'string', 'max' => 255],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'author_id'        => Yii::t('app', '公众号ID'),
				'msg_title'        => Yii::t('app', '消息名称'),
				'template_id'      => Yii::t('app', '模板消息ID'),
				'template_data'    => Yii::t('app', '模板消息的data（json格式）'),
				'template_content' => Yii::t('app', '模板内容'),
				'redirect_type'    => Yii::t('app', '跳转类型，1：链接、2：小程序'),
				'url'              => Yii::t('app', '模板跳转链接（海外帐号没有跳转能力）'),
				'appid'            => Yii::t('app', '所需跳转到的小程序appid（该小程序appid必须与发模板消息的公众号是绑定关联关系，暂不支持小游戏）'),
				'pagepath'         => Yii::t('app', '所需跳转到小程序的具体页面路径，支持带参数,（示例index?foo=bar），要求该小程序已发布，暂不支持小游戏'),
				'push_type'        => Yii::t('app', '发送类别：1：全部粉丝、2：标签、3：性别、4：自定义'),
				'push_rule'        => Yii::t('app', '发送条件（json格式）'),
				'push_time'        => Yii::t('app', '发送时间'),
				'create_time'      => Yii::t('app', '创建时间'),
				'fans_num'         => Yii::t('app', '发送成功粉丝数'),
				'will_fans_num'    => Yii::t('app', '预计发生粉丝数'),
				'status'           => Yii::t('app', '状态 0未发送 1已发送 2发送失败'),
				'queue_id'         => Yii::t('app', '队列id'),
				'msg_id'           => Yii::t('app', '消息id，多个逗号隔开 '),
				'error_code'       => Yii::t('app', '错误码'),
				'error_msg'        => Yii::t('app', '错误信息'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplatePushInfos ()
		{
			return $this->hasMany(TemplatePushInfo::className(), ['template_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplate ()
		{
			return $this->hasOne(Template::className(), ['id' => 'template_id']);
		}

		/**
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->template_data = rawurlencode($this->template_data);

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->template_data)) {
				$this->template_data = rawurldecode($this->template_data);
			}

			parent::afterFind();
		}

		public function dumpData ()
		{
			$isProd = \Yii::$app->params['is_prod'];
			if (($this->id >= 433 || !$isProd) && $this->fans_num != count($this->templatePushInfos)) {
				$fansNum = TemplatePushInfo::find()->where(['template_id' => $this->id, 'status' => [TemplatePushInfo::SEND_SUCCESS, TemplatePushInfo::SENDING]])->count();
				if ($this->id > 433 || !$isProd) {
					$this->fans_num = $fansNum;

					$this->update();
				} else {
					$this->fans_num += $fansNum;
				}
			}

			$result = [
				'id'            => $this->id,
				'key'           => $this->id,
				'msg_title'     => $this->msg_title,
				'push_time'     => $this->push_time,
				'fans_num'      => $this->fans_num,
				'will_fans_num' => $this->will_fans_num,
				'error_msg'     => $this->error_msg,
			];

			return $result;
		}

		/**
		 * 设置模板消息
		 */
		public static function setTemplateMessage ($data, $type = 0)
		{
			try {
				if (empty($data['id'])) {
					$tmp = new TemplatePushMsg();
				} else {
					$tmp = TemplatePushMsg::findOne($data['id']);
				}
				$templateDataContent = $data['template_data'];
				$tempData            = [];
				foreach ($templateDataContent as $key => $content) {
					$content['value']          = isset($content['value']) ? $content['value'] : '';
					$value                     = rtrim($content['value']);
					$tempData[$content['key']] = [
						'color' => $content['color'],
						'value' => $value,
					];
					$start                     = $content['start'];
					$end                       = $content['end'];
					if (!empty($start['key'])) {
						$start['color']          = $start['color'] ? $start['color'] : '#333333';
						$tempData[$start['key']] = [
							'color' => $start['color'],
							'value' => $start['value'],
						];
					}
					if (!empty($end['key'])) {
						$end['color']          = $end['color'] ? $end['color'] : '#333333';
						$tempData[$end['key']] = [
							'color' => $end['color'],
							'value' => $end['value'],
						];
					}
				}

				$temp          = Template::findOne(['id' => $data['template_id']]);
				$template_data = json_encode($tempData);
				if ($type == 1) {
					$data['template_data'] = $tempData;

					return $data;
				}
				$wx_template_id = $temp->template_id;
				$wxAuth         = WxAuthorize::findOne(['author_id' => $data['author_id']]);
				$wxAuthorize    = WxAuthorize::getTokenInfo($wxAuth->authorizer_appid, false, true);
				if (!empty($wxAuthorize)) {
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $wxAuth->authorizer_appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);
				}
				$result          = $wechat->getTemplate();
				$template_id_new = array_column($result, 'template_id');
				if (!in_array($wx_template_id, $template_id_new)) {
					throw new InvalidParameterException("此模板消息ID已在微信公众平台删除，请重新选择其他模板");
				}
				$tmp->template_content = json_encode($templateDataContent);
				$tmp->author_id        = $data['author_id'];
				$tmp->msg_title        = $data['msg_title'];
				$tmp->template_id      = $data['template_id'];
				$tmp->template_data    = $template_data;
				$tmp->redirect_type    = $data['redirect_type'];
				$tmp->url              = $data['url'];
				$tmp->appid            = $data['appid'];
				$tmp->pagepath         = $data['pagepath'];
				$tmp->push_type        = $data['push_type'];
				$extend['sex']         = $data['sex'];
				$extend['stime']       = $data['stime'];
				$extend['etime']       = $data['etime'];
				$extend['province']    = $data['province'];
				$extend['city']        = $data['city'];
				$extend['tag_ids']     = $data['tag_ids'];
				$extend['openids']     = $data['openids'];
				$extend['send_type']   = $data['send_type'];
				if ($data['push_type'] == 1) {
					$fans = Fans::find()->where(['f.author_id' => $data['author_id']])->alias('f');
					if (!empty($data['sex'])) {
						if ($data['sex'] == 3) {
							$data['sex'] = 0;
						}
						$fans = $fans->andWhere(['f.sex' => $data['sex']]);
					}
					if (!empty($data['stime'])) {
						$fans = $fans->andWhere(['>=', 'f.subscribe_time', $data['stime']]);
					}
					if (!empty($data['etime'])) {
						$fans = $fans->andWhere(['<=', 'f.subscribe_time', $data['etime']]);
					}
					if (!empty($data['province'])) {
						$fans = $fans->andWhere(['f.province' => $data['province']]);
					}
					if (!empty($data['city'])) {
						$fans = $fans->andWhere(['f.city' => $data['city']]);
					}
					if (!empty($data['tag_ids'])) {
						$tagIds = explode(',', $data['tag_ids']);
						if (!in_array(0, $tagIds)) {
							$fans = $fans->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`')->andWhere(['and', ['f.author_id' => $data['author_id']], ['in', 'ft.tags_id', $tagIds]]);
						} else {
							$keyList = array_keys($tagIds, 0);
							unset($tagIds[$keyList[0]]);

							if (!empty($tagIds)) {
								$fans = $fans->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`')->andWhere(['and', ['f.author_id' => $data['author_id']], ['or', ['f.tagid_list' => '[]'], ['in', 'ft.tags_id', $tagIds]]]);
							} else {
								$fans = $fans->andWhere(['f.author_id' => $data['author_id'], 'f.tagid_list' => '[]']);
							}
						}
					}
					$fans               = $fans->andWhere(['f.subscribe' => 1]);
					$count              = $fans->count();
					$tmp->will_fans_num = $count;
//					$fansInfo           = $fans->all();
				} elseif ($data['push_type'] == 2) {
					//全部粉丝
					$fans               = Fans::find()->andWhere(['subscribe' => 1, 'author_id' => $data['author_id']]);
					$count              = $fans->count();
					$tmp->will_fans_num = $count;
//					$fansInfo           = $fans->all();
				} elseif ($data['push_type'] == 3) {
					$count              = explode(';', $data['openids']);
					$tmp->will_fans_num = count($count);
//					$fansInfo           = Fans::find()->where(['openid' => $count])->all();
				}
				if(empty($tmp->will_fans_num)){
					throw new InvalidParameterException('请选择群发粉丝');
				}
				$tmp->push_rule = base64_encode(json_encode($extend));
				if ($data['send_type'] == 1) {
					$tmp->push_time = DateUtil::getCurrentTime();
				} else {
					$tmp->push_time = $data['send_time'];
				}
				$tmp->fans_num    = 0;
				$tmp->status      = 0;
				$tmp->create_time = DateUtil::getCurrentTime();
				if (!$tmp->validate() || !$tmp->save()) {
					/*if (!empty($fansInfo)) {
						try {
							$infoType = empty($data['id']) ? TemplatePushInfo::ADD_INFO : TemplatePushInfo::EDIT_INFO;
							TemplatePushInfo::create($tmp->id, $fansInfo, $infoType);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__);
						}
					}*/

					if (empty($data['id'])) {
						throw new InvalidParameterException('创建失败.' . TemplatePushMsg::getModelError($tmp));
					} else {
						throw new InvalidParameterException('修改失败.' . TemplatePushMsg::getModelError($tmp));
					}
				}
				$insert_id = $tmp->attributes['id'];

				return $insert_id;  //返回刚插入或修改的id
			} catch (\Exception $e) {
				throw new InvalidParameterException($e->getMessage());
			}

		}

		//发送预览
		public static function sendPreviewMessage ($data, $author_id, $openid)
		{
			$data = \Yii::$app->cache->get($data);
			if (!empty($data['template_id'])) {
				$fans        = Fans::findOne(['openid' => $openid, 'author_id' => $author_id]);
				$wxAuth      = WxAuthorize::findOne(['author_id' => $author_id]);
				$wxAuthorize = WxAuthorize::getTokenInfo($wxAuth->authorizer_appid, false, true);
				if (!empty($wxAuthorize)) {
					$wechat = \Yii::createObject([
						'class'          => Wechat::className(),
						'appId'          => $wxAuth->authorizer_appid,
						'appSecret'      => $wxAuthorize['config']->appSecret,
						'token'          => $wxAuthorize['config']->token,
						'componentAppId' => $wxAuthorize['config']->appid,
					]);
				}
				$temp          = Template::findOne(['id' => $data['template_id']]);
				$template_id   = $temp->template_id;
				$content       = $temp->content;
				$content       = ltrim($content, '{{first.DATA}}');
				$content       = rtrim($content, '{{remark.DATA}}');
				$con           = explode(PHP_EOL, $content);
				$template_data = $data['template_data'];
				$template_data = Template::replaceTemplateData($template_data, $fans->nickname, $temp, $con);
				$miniprogram   = [];
				if ($data['redirect_type'] == 1) {
					$url = $data['url'];
				} elseif ($data['redirect_type'] == 2) {
					$url                     = '';
					$miniprogram['appid']    = $data['appid'];
					$miniprogram['pagepath'] = $data['pagepath'];
				}
				$result = $wechat->sendTemplateMessage($openid, $template_id, $template_data, $data['url'], $miniprogram, $data['redirect_type']);
				\Yii::error($result, 'result_sendPreviewMessage');

				return false;
			}
		}

		//获取错误信息
		public static function getModelError ($model)
		{
			$errors = $model->getErrors();
			if (!is_array($errors)) {
				return '';
			}
			$firstError = array_shift($errors);
			if (!is_array($firstError)) {
				return '';
			}

			return array_shift($firstError);
		}

		/**
		 * 批量更新模板数据
		 *
		 */
		public static function updateData ()
		{
			$pushMsg = TemplatePushMsg::find()->where(['template_content' => NULL])->all();
			if (!empty($pushMsg)) {
				foreach ($pushMsg as $msg) {

					$temp         = Template::findOne(['id' => $msg->template_id]);
					$template     = $temp->content;
					$templateData = explode("{{", $template);
					if (!empty($templateData[0])) {
						unset($templateData[0]);
						$templateData = implode("{{", $templateData);
						$template     = "{{" . $templateData;
					}
					$arr     = explode(PHP_EOL, $template);
					$newData = [];
					$tmpData = [];
					if (!empty($arr)) {
						foreach ($arr as $key => $value) {
							if (!empty($value)) {
								$data                     = explode(".DATA}}", $value);
								$data1                    = explode("{{", $data[0]);
								$newData[$key][$data1[1]] = $data1[0];
								if (isset($data[1]) && !empty(trim($data[1]))) {
									$data2                    = explode("{{", $data[1]);
									$newData[$key][$data2[1]] = $data2[0];
								}
								if (isset($data[2]) && !empty(trim($data[2]))) {
									$data3                    = explode("{{", $data[2]);
									$newData[$key][$data3[1]] = $data3[0];
								}
								$show    = 1;
								$type    = 0;//不显示前后
								$start   = '';
								$end     = '';
								$title   = '';
								$keyWord = '';
								if (count($data) > 2) {
									if (empty($data2[0])) {
										$show = 0;//不显示文字
									} else {
										$title = $data2[0];
									}
									$keyWord = $data2[1];
								} else {
									if (empty($data1[0])) {
										$show = 0;//不显示文字
									}
									$title   = $data1[0];
									$keyWord = $data1[1];
								}
								if (!empty($data[1]) && empty($data[2])) {
									$type  = 1;//显示前
									$start = $data1[1];
								}
								if (empty($data[1]) && !empty($data[2])) {
									$type = 2;//显示后
									$end  = $data3[1];
								}
								if (!empty($data[1]) && !empty($data[2])) {
									$type  = 3;//显示前后
									$start = $data1[1];
									$end   = $data3[1];
								}
								$tmpData[$key]['show']  = $show;
								$tmpData[$key]['type']  = $type;
								$tmpData[$key]['start'] = ['key' => $start, 'value' => ''];
								$tmpData[$key]['end']   = ['key' => $end, 'value' => ''];
								$tmpData[$key]['title'] = $title;
								$tmpData[$key]['key']   = $keyWord;
							}
						}
					}
					$newData = array_values($tmpData);

					$templateData = rawurldecode($msg->template_data);
					$templateData = json_decode($templateData, true);

					if (!empty($templateData)) {
						foreach ($newData as $k => $data) {
							foreach ($templateData as $key => $value) {
								if ($key == $data['key']) {
									$newData[$k]['value'] = $value['value'];
									$newData[$k]['color'] = $value['color'];
								}
								if ($data['key'] == 'amount' && $key == 'creditName') {
									$newData[$k]['start']['value'] = $value['value'];
									$newData[$k]['start']['color'] = $value['color'];
								}
								if ($data['key'] == 'number' && $key == 'creditChange') {
									$newData[$k]['start']['value'] = $value['value'];
									$newData[$k]['start']['color'] = $value['color'];
								}
							}
						}
					}

					$msg->template_content = json_encode($newData);
					$msg->save();
				}
			}
		}
	}
