<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;

    /**
	 * This is the model class for table "{{%external_time_line}}".
	 *
	 * @property int    $id
	 * @property int    $uid         商户id
	 * @property int    $external_id 外部联系人ID
	 * @property int    $sub_id      子账户ID
	 * @property int    $user_id     成员ID
	 * @property string $event       行为，类别见model：send_msg群发消息、view_msg浏览消息、set_field完善客户信息、follow跟进、add_tag添加标签、del_tag移除标签、add_tag通过客户群添加标签、del_tag通过客户群移除标签、del_user客户删除成员、user_del成员删除客户、add_user添加成员、send_money发送红包
	 * @property int    $event_time  行为时间
	 * @property int    $event_id    行为事件id
	 * @property int    $related_id  数据相关表id
	 * @property int    $openid      用户openid
	 * @property string $remark      行为相关备注
	 */
	class ExternalTimeLine extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%external_time_line}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'external_id', 'sub_id', 'user_id', 'event_time', 'event_id', 'related_id'], 'integer'],
				[['external_id'], 'required'],
				[['event'], 'string', 'max' => 32],
				[['remark'], 'string'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'uid'         => Yii::t('app', '商户id'),
				'external_id' => Yii::t('app', '外部联系人ID'),
				'sub_id'      => Yii::t('app', '子账户ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'event'       => Yii::t('app', '行为，类别见model'),
				'event_time'  => Yii::t('app', '行为时间'),
				'event_id'    => Yii::t('app', '行为事件id'),
				'related_id'  => Yii::t('app', '相关表id'),
				'openid'      => Yii::t('app', '用户openid'),
				'remark'      => Yii::t('app', '行为相关备注'),
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
		 * 添加客户行为轨迹
		 */
		public static function addExternalTimeLine ($eventData)
		{
			if ((!empty($eventData['external_id']) && !empty($eventData['event'])) || in_array($eventData['event'], ['chat_track', 'punch_card'])) {
				$timeLine = new self();

				$timeLine->external_id = isset($eventData['external_id']) ? $eventData['external_id'] : 0;
				$timeLine->uid         = isset($eventData['uid']) ? $eventData['uid'] : 0;
				$timeLine->sub_id      = isset($eventData['sub_id']) ? $eventData['sub_id'] : 0;
				$timeLine->user_id     = isset($eventData['user_id']) ? $eventData['user_id'] : 0;
				$timeLine->event       = $eventData['event'];
				$timeLine->event_time  = !empty($eventData['event_time']) ? $eventData['event_time'] : time();
				$timeLine->event_id    = isset($eventData['event_id']) ? $eventData['event_id'] : 0;
				$timeLine->related_id  = isset($eventData['related_id']) ? $eventData['related_id'] : 0;
				$timeLine->openid      = isset($eventData['openid']) ? $eventData['openid'] : '';
				$timeLine->remark      = isset($eventData['remark']) ? $eventData['remark'] : '';

				$timeLine->save();
			}

			return true;
		}

		/**
		 * 客户行为轨迹数据
		 */
		public static function getExternalTimeLine ($uid, $data)
		{
			$attachmentType = ['1' => '图片', '2' => '音频', '3' => '视频', '4' => '图文', '5' => '文件', '6' => '文本', '7' => '小程序'];
			$userInfo       = UserProfile::findOne(['uid' => $uid]);

			$info = [];
			foreach ($data as $key => $val) {
				$infoData               = [];
				$icon                   = '5';
				$content                = '';
				$infoData['event_time'] = !empty($val['event_time']) ? date('Y-m-d H:i:s', $val['event_time']) : '';
				$infoData['line_id']    = $val['id'];
				switch ($val['event']) {
					case 'send_msg'://发消息
						$icon    = 11;
						$content = '系统向该客户发消息';
						if ($val['event_id']) {
							$attachment = Attachment::findOne($val['event_id']);
							if (!empty($attachment)) {
								$content .= '【' . $attachment->file_name . '】【' . $attachmentType[$attachment->file_type] . '】';
							}
						} else {
							$content .= '【文本】';
						}
						break;
					case 'subscribe'://客户关注公众号
						$icon    = 12;
						$content = $val['remark'];
						break;
					case 'customer_send_group':
						$icon  = 11;
						$title = '';
						if (!empty($val['related_id'])) {
							$tagGroup = WorkGroupSending::findOne($val['related_id']);
							if (!empty($tagGroup)) {
								$title = $tagGroup->title;
							}
						}
						$name = '系统';
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
						}
						$content = $name . '通过群发消息【' . $title . '】向该客户推送了' . $val['remark'];
						break;
					case 'tag_send_group': //标签拉群
						$icon  = 11;
						$title = '';
						if (!empty($val['related_id'])) {
							$tagGroup = WorkTagPullGroup::findOne($val['related_id']);
							if (!empty($tagGroup)) {
								$title = $tagGroup->title;
							}
						}
						$name = '系统';
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
						}
						$content = $name . '通过标签拉群【' . $title . '】向该客户推送群聊码，发送结果：' . $val['remark'];
						break;
					case 'view_msg'://浏览消息
						$icon    = 12;
						$content = '浏览';
						if ($val['event_id']) {
							$attachment = Attachment::findOne($val['event_id']);
							if ($attachment !== NULL) {
								$content .= ' ' . $attachmentType[$attachment->file_type] . '【' . $attachment->file_name . '】';
							}
						} else {
							$content .= '消息';
						}
						if (!empty($val['related_id'])) {
							$attachmentSta = AttachmentStatistic::findOne($val['related_id']);
							if (!empty($attachmentSta->leave_time)) {
								$time = strtotime($attachmentSta->leave_time) - strtotime($attachmentSta->open_time);
								$time = RedPackJoin::sec2Time($time);
								if (!empty($time)) {
									$content .= ',停留时间' . $time;
								}
							}

							if (isset($attachment) && (!isset($time) || empty($time))) {
								if (($attachment->file_type == 4 && ($attachment->is_editor == 1 || $attachment->material_id > 0)) || in_array($attachment->file_type, [1, 3], false) || ($attachment->file_type == 5 && in_array($attachment->file_content_type, ['text/plain', 'application/pdf']))) {
									$content .= ',停留时间 1 秒';
								}
							}
						}
						break;
					case 'set_field'://完善客户信息
						$icon = 10;
						$name = '';
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							if (!empty($workUser)) {
								$name = $workUser->name . '的';
							}
						}
						$content = '完善' . $name . '客户信息';
						$remark = json_decode($val['remark'],true);
						if(!empty($val['remark']) && is_array($remark)){
                            array_walk($remark,function(&$val,$key){
                                if($val['key'] == 'image'){
                                    $val['old_value'] = json_decode($val['old_value'],true);
                                    $val['value'] = json_decode($val['value'],true);
                                }
                            });
                            $content = ['remark'=>$content,'info'=>$remark];
                        }else $content .= '：' . $val['remark'];

						break;
					case 'follow'://客户跟进
						$icon = 11;
						$name = '';
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
						} elseif (!empty($val['sub_id'])) {
							$subInfo = SubUserProfile::findOne(['sub_user_id' => $val['sub_id']]);
							$name    = $subInfo->name;
						} else {
							$name = $userInfo->nick_name;
						}

						$followModle = WorkExternalContactFollowRecord::findOne($val['related_id']);
						if ($followModle && $followModle->record_type == 1) {
							$content = '';
							if (is_numeric($followModle->record)) {
								$dialoutRecord = DialoutRecord::findOne((int) $followModle->record);
								if ($dialoutRecord) {
									if ($dialoutRecord->state == 1 && $dialoutRecord->begin > 0) {
										$content = "【" . $name . "】对该客户第" . $val['remark'] . "次通话：【已接通】通话时长" . DateUtil::getHumanFormatBySecond($dialoutRecord->end - $dialoutRecord->begin);
									} else {
										$waitSeconds = $dialoutRecord->ringing > 0 ? ($dialoutRecord->end - $dialoutRecord->ringing) . 's' : '-';
										$content     = "【" . $name . "】对该客户第" . $val['remark'] . "次通话：【未接通】响铃时长" . $waitSeconds;
									}

								}
							}
						} else {
							$content = '【' . $name . '】 第' . $val['remark'] . '次跟进';
							if (!empty($val['event_id'])) {
								$follow  = Follow::findOne($val['event_id']);
								$content .= '，为【' . $follow->title . '】状态';
							}
						}

						break;
					case 'add_tag'://打标签
						$icon = 1;
						$name = '';
						if (!empty($val['related_id'])) {
							$workUser = WorkUser::findOne($val['related_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
							$content = '【' . $name . '】对该客户打标签';
						} else {
							if (!empty($val['user_id'])) {
								$workUser = WorkUser::findOne($val['user_id']);
								if (!empty($workUser)) {
									$name = $workUser->name . '的';
								}
							}
							$content = '系统对' . $name . '客户打标签';
						}
						if (!empty($val['remark'])) {
							$content .= $val['remark'];
						}
						break;
					case 'del_tag'://移除标签
						$icon = 2;
						$name = '';
						if (!empty($val['related_id'])) {
							$workUser = WorkUser::findOne($val['related_id']);
							if (!empty($workUser)) {
								$name = $workUser->name;
							}
							$content = '【' . $name . '】对该客户移除标签';
						} else {
							if (!empty($val['user_id'])) {
								$workUser = WorkUser::findOne($val['user_id']);
								if (!empty($workUser)) {
									$name = $workUser->name . '的';
								}
							}
							$content = '系统对' . $name . '客户移除标签';
						}
						if (!empty($val['remark'])) {
							$content .= $val['remark'];
						}
						break;
					case 'chat_add_tag'://通过客户群打标签
						$icon = 1;
						if (!empty($val['related_id'])) {
							$workUser = WorkUser::findOne($val['related_id']);
							$name     = !empty($workUser) ? $workUser->name : '--';
							$content  = '【' . $name . '】';
						} else {
							$content = '系统';
						}
						if (!empty($val['event_id'])) {
							$chatName = WorkChat::getChatName($val['event_id']);
							if ($chatName) {
								$chatName = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;
								$content  .= '通过客户群【' . $chatName . '】';
							}
						}
						if (!empty($val['user_id'])) {
							$workUser = WorkUser::findOne($val['user_id']);
							if (!empty($workUser)) {
								$name = $workUser->name . '的';
							}
						}else{
							$name = '该';
						}
						$content .= '对' . $name . '客户打标签' . $val['remark'];
						break;
					case 'chat_del_tag'://通过客户群移除标签
						$icon = 2;
						if (!empty($val['related_id'])) {
							$workUser = WorkUser::findOne($val['related_id']);
							$name     = !empty($workUser) ? $workUser->name : '--';
							$content  = '【' . $name . '】';
						} else {
							$content = '系统';
						}
						if (!empty($val['event_id'])) {
							$chatName = WorkChat::getChatName($val['event_id']);
							if ($chatName) {
								$chatName = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;
								$content  .= '通过客户群【' . $chatName . '】';
							}
						}
						$content .= '对该客户移除标签' . $val['remark'];
						break;
					case 'del_user'://客户删除企业成员
						$icon    = 4;
						$content = '客户将企业成员【' . $val['remark'] . '】删除';
						break;
					case 'user_del'://企业成员删除客户
						$icon    = 4;
						$content = '企业成员【' . $val['remark'] . '】将该客户删除';
						if (!empty($val['related_id'])) {
							$claimUser = PublicSeaClaimUser::findOne($val['related_id']);
							if (!empty($claimUser)) {
								$newWorkUser = WorkUser::findOne($claimUser->new_user_id);
								$contactInfo = WorkExternalContact::findOne($claimUser->external_userid);
								if (!empty($newWorkUser) && !empty($contactInfo)) {
									$content = '员工【' . $val['remark'] . '】的客户【' . $contactInfo->name . '】在公海池，被员工【' . $newWorkUser->name . '】认领走了';
								}
							}
						}
						break;
					case 'add_user'://客户添加企业成员
						$icon    = 4;
						$content = $val['remark'];
						break;
					case 'send_money'://员工对客户手动发红包
						$icon     = 13;
						$workUser = WorkUser::findOne($val['user_id']);
						$name     = !empty($workUser) ? $workUser->name : '--';
						$content  = '企业成员【' . $name . '】手动向该客户发送红包【' . $val['remark'] . '元】';
						break;
					case 'send_chat_money'://员工对客户群手动发红包
						$icon     = 13;
						$workUser = WorkUser::findOne($val['user_id']);
						$name     = !empty($workUser) ? $workUser->name : '--';
						$chatName = WorkChat::getChatName($val['event_id']);
						$content  = '客户领取群主【' . $name . '】在群【' . $chatName . '】手动发放的红包【' . $val['remark'] . '元】';
						break;
					case 'group_send_money'://对客户群发红包
						$icon         = 13;
						$workUser     = WorkUser::findOne($val['user_id']);
						$name         = !empty($workUser) ? $workUser->name : '--';
						$groupSending = WorkGroupSending::findOne($val['related_id']);
						$content      = '客户（归属于成员【' . $name . '】）通过群发红包【' . $groupSending->title . '】领取红包【' . $val['remark'] . '元】';
						break;
					case 'group_send_chat_money'://对客户群群发红包
						$icon          = 13;
						$workUser      = WorkUser::findOne($val['user_id']);
						$name          = !empty($workUser) ? $workUser->name : '--';
						$groupSending  = WorkGroupSending::findOne($val['related_id']);
						$groupSendData = WorkTagGroupStatistic::findOne($val['event_id']);
						$chatName      = '--';
						if (isset($groupSendData->chat_id) && !empty($groupSendData->chat_id)) {
							$chatName = WorkChat::getChatName($groupSendData->chat_id);
						}
						$content = '客户通过群发红包【' . $groupSending->title . '】领取群主【' . $name . '】在群【' . $chatName . '】发放的红包【' . $val['remark'] . '元】';
						break;
					case 'red_way'://红包拉新发送红包
						$icon         = 13;
						$workUser     = WorkUser::findOne($val['user_id']);
						$name         = !empty($workUser) ? $workUser->name : '--';
						$redpacketWay = WorkContactWayRedpacket::findOne($val['related_id']);
						$content      = '客户通过红包拉新【' . $redpacketWay->name . '】加上成员【' . $name . '】，领取【' . $val['remark'] . '元】红包';
						break;
					case 'fission_add_tag'://营销引流打标签
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'chat_tag'://聊天打标签
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'way_redpacket_tag'://红包拉新
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'chat_track'://群
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'activity_add_tag'://新裂变引流
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'moment_time'://朋友圈
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'work_welcome'://欢迎语
						$icon     = 12;
						$workUser = WorkUser::findOne($val['user_id']);
						$name     = $workUser !== NULL ? $workUser->name : '--';
						$content  = '客户查看了添加成员【' . $name . '】时发送的欢迎语';
						break;
					case 'work_contact_way'://渠道活码欢迎语
						$icon       = 12;
						$workUser   = WorkUser::findOne($val['user_id']);
						$name       = $workUser !== NULL ? $workUser->name : '--';
						$contactWay = WorkContactWay::findOne($val['event_id']);
						$content    = '客户查看了通过渠道活码【' . $contactWay->title . '】添加成员【' . $name . '】时发送的欢迎语';
						break;
					case 'radar_tag'://雷达互动打标签
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'punch_card'://群打卡
						$icon    = 1;
						$content = $val['remark'];
						break;
					case 'moment_goods'://朋友圈点赞
						$icon    = 20;
						$content = $val['remark'];
						break;
					case 'moment_reply'://朋友圈评论
						$icon    = 20;
						$content = $val['remark'];
						break;
					case 'auto_rule_tag'://自动化打标签
						$icon    = 1;
						$content = $val['remark'];
						break;
                    case 'moment_reply'://朋友圈评论
                        $icon    = 20;
                        $content = $val['remark'];
                        break;
					case 'bind_custom'://绑定非企微
						$icon    = 24;
						$content = $val['remark'];
						break;
					case 'transfer_custom'://转交
						$icon    = 14;
						$content = $val['remark'];
						break;
					case 'protect_custom'://客户保护
						$icon    = 23;
						$content = $val['remark'];
						break;
					case 'no_protect_custom'://客户取消保护
						$icon    = 25;
						$content = $val['remark'];
						break;
					case 'give_up_custom'://客户放弃
						$icon    = 17;
						$content = $val['remark'];
						break;
				}

				$infoData['icon']    = $icon;
				$infoData['content'] = $content;
				$info[]              = $infoData;
			}

			return $info;
		}

		//群轨迹数据更新
		public static function chatLineBatch ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$cacheKey    = 'chat_line_batch';
			$cacheChatId = \Yii::$app->cache->get($cacheKey);
			$id          = !empty($cacheChatId) ? $cacheChatId : 0;
			$page        = 1;
			$pageSize    = 1000;

			while (true) {
				$offset   = ($page - 1) * $pageSize;
				$workChat = WorkChat::find()->where(['>', 'id', $id])->limit($pageSize)->offset($offset)->select('id,owner_id,create_time')->all();
				if (empty($workChat)) {
					break;
				}
				/**@var WorkChat $chat * */
				foreach ($workChat as $chat) {
					$chatId = $chat->id;
					//更新建群时间
					$lineInfo = static::findOne(['event' => 'chat_track', 'related_id' => $chatId, 'event_id' => 1, 'user_id' => $chat->owner_id]);
					if (!empty($lineInfo)) {
						$lineInfo->event_time = $chat->create_time;
						$lineInfo->update();
					}

					$workChatInfo = WorkChatInfo::find()->where(['chat_id' => $chatId])->select('id,user_id,external_id,type,userid,join_time')->all();
					/**@var WorkChatInfo $chatInfo * */
					foreach ($workChatInfo as $chatInfo) {
						if ($chatInfo->type == 1) {
							if ($chatInfo->user_id != $chat->owner_id) {
								$lineInfo = static::findOne(['event' => 'chat_track', 'related_id' => $chatId, 'event_id' => 2, 'user_id' => $chatInfo->user_id]);
								if (!empty($lineInfo)) {
									$lineInfo->event_time = $chatInfo->join_time;
									$lineInfo->update();
								}
							}
						} elseif ($chatInfo->type == 2) {
							if (!empty($chatInfo->external_id)) {
								$lineInfo = static::findOne(['event' => 'chat_track', 'related_id' => $chatId, 'event_id' => 2, 'external_id' => $chatInfo->external_id]);
							} elseif (!empty($chatInfo->userid)) {
								$lineInfo = static::findOne(['event' => 'chat_track', 'related_id' => $chatId, 'event_id' => 2, 'openid' => $chatInfo->userid]);
							}
							if (!empty($lineInfo)) {
								$lineInfo->event_time = $chatInfo->join_time;
								$lineInfo->update();
							}
						}
					}
					\Yii::$app->cache->set($cacheKey, $chatId, 7200);
				}
				$page++;
			}
			\Yii::error(\Yii::$app->cache->get($cacheKey), 'cacheChatId');
		}

		/**
		 * @param $timeLineId
		 * @param $settingId
		 * @param $type
		 * @param $fd
		 * @param $over
		 */
		public static function setMomentLiveTime ($timeLineId, $settingId, $type, $fd, $over = false)
		{
			$TimeLineModel = self::findOne($timeLineId);
			$timeDiff      = time() - $TimeLineModel->event_time;
			$date          = floor($timeDiff / 86400);
			$hour          = floor($timeDiff % 86400 / 3600);
			$minute        = floor($timeDiff % 86400 / 60);
			$second        = floor($timeDiff % 86400 % 60);

			if ($date > 0) {
				$minute = floor($minute / 60);
				$str    = $date . "天" . $hour . "小时" . $minute . "分钟" . $second . "秒";
			} else if ($hour > 0) {
				$minute = floor($minute % 60);
				$str    = $hour . "小时" . $minute . "分钟" . $second . "秒";
			} else if ($minute > 0) {
				$str = $minute . "分钟" . $second . "秒";
			} else {
				if ($second <= 0) {
					$str = "1秒";
				} else {
					$str = $second . "秒";
				}
			}
			Yii::error($timeDiff, "setMomentLiveTime");
			Yii::error($str, "setMomentLiveTime");
			$localAddress          = strripos($TimeLineModel->remark, "留");
			$remark                = substr($TimeLineModel->remark, 0, $localAddress + 3);
			$TimeLineModel->remark = $remark . $str;
			$TimeLineModel->save();

		}

	}
