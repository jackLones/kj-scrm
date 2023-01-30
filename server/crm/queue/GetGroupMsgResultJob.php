<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/2/14
	 * Time: 15:14
	 */

	namespace app\queue;

	use app\models\UserCorpRelation;
	use app\models\WorkChat;
	use app\models\WorkCorpAgent;
	use app\models\WorkExternalContact;
	use app\models\WorkGroupSending;
	use app\models\WorkGroupSendingRedpacketSend;
	use app\models\WorkGroupSendingUser;
	use app\models\WorkTagGroupStatistic;
	use app\models\WorkTagGroupUserStatistic;
	use app\models\WorkTagPullGroup;
	use app\models\ExternalTimeLine;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use app\util\DateUtil;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class GetGroupMsgResultJob extends BaseObject implements JobInterface
	{

		public $type; //0 标签拉群 1 群发客户 2 群发客户群
		public $sendId;

		public function execute ($queue)
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);
			\Yii::error($this->type, 'GetGroupMsgResultJob-type');
			if ($this->type == 0 || $this->type == 1) {
				//标签拉群
				\Yii::error($this->sendId, 'GetGroupMsgResultJob-Id');
				$pullSendId = '';
				if ($this->type == 0) {
					$group      = WorkTagPullGroup::findOne($this->sendId);
					$pullSendId = 'pull_id';
				} elseif ($this->type == 1) {
					$group      = WorkGroupSending::findOne($this->sendId);
					$pullSendId = 'send_id';
				}
				if (!empty($group)) {
					if($this->type == 1 && $group->interval == 2){
						//间隔分发
						$sendUser = WorkGroupSendingUser::find()->where(['send_id'=>$this->sendId,'status'=>1])->all();
						if(!empty($sendUser)){
							/** @var WorkGroupSendingUser $send */
							foreach ($sendUser as $send){
								try {
									if(!empty($send->msgid) && $send->push_type == 0){
										$workApi = WorkUtils::getWorkApi($group->corp_id, WorkUtils::EXTERNAL_API);
										$result  = $workApi->ECGetGroupMsgResult($send->msgid);
										if ($result['errcode'] == 0) {
											$detail_list = $result['detail_list'];
											if (!empty($detail_list)) {
												foreach ($detail_list as $list){
													$contact  = WorkExternalContact::findOne(['corp_id' => $group->corp_id, 'external_userid' => $list['external_userid']]);
													$workUser = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $list['userid']]);
													if ($list['status'] != 0) {
														$send->push_type = 1;
														$send->push_time = time();
														$send->save();
														if (!empty($contact) && !empty($workUser)) {
															$groupSta = WorkTagGroupStatistic::findOne(['send_id' => $this->sendId, 'external_id' => $contact->id, 'user_id' => $workUser->id]);
															if(!empty($groupSta)){
																$groupSta->send = $list['status'];
																$groupSta->push_type = 1;

																if (isset($list['send_time']) && !empty($list['send_time'])) {
																	$groupSta->push_time = date('Y-m-d H:i:s', $list['send_time']);
																}

																$groupSta->save();

																if (isset($group->is_redpacket) && $group->is_redpacket == 1 && $list['status'] == 1){
																	$create_time = !empty($list['send_time']) ? $list['send_time'] : time();
																	WorkGroupSendingRedpacketSend::updateAll(['create_time' => $create_time, 'is_send' => 1], ['group_send_id' => $groupSta->id, 'create_time' => 0]);
																}
															}
														}

														if ($list['status'] == 1) {
															$stat           = WorkTagGroupUserStatistic::findOne(['send_id' => $this->sendId, 'user_id' => $workUser->id]);
															$realNum        = intval($stat->real_num);
															$realNum        = $realNum + 1;
															$stat->real_num = $realNum;
															$stat->save();
														}

														if ($group->is_redpacket == 0) {
															//记录客户行为轨迹
															$content = $group->content;
															$text    = '';
															if (strpos($content, 'text') !== false) {
																$text .= '文本、';
															}
															if (strpos($content, 'media_id') !== false) {
																$text .= '图片、';
															}
															if (strpos($content, 'link') !== false) {
																$text .= '图文、';
															}
															if (strpos($content, 'miniprogram') !== false) {
																$text .= '图文、';
															}
															$remark = '';
															if ($list['status'] == 1) {
																$remark = '发送成功';
															} elseif ($list['status'] == 2) {
																$remark = '客户已不是好友';
															} elseif ($list['status'] == 3) {
																$remark = '客户接收已达上限';
															}
															$text   = trim($text, '、');
															$remark = $text . '，发送结果：' . $remark;
															$user   = UserCorpRelation::findOne(['corp_id' => $group->corp_id]);
															ExternalTimeLine::addExternalTimeLine(['uid' => $user->uid, 'external_id' => $contact->id, 'remark' => $remark, 'user_id' => $workUser->id, 'event' => 'customer_send_group', 'related_id' => $this->sendId]);
														}

													}

													$sendUserSta = WorkGroupSendingUser::findOne(['send_id' => $this->sendId, 'user_id' => $workUser->id, 'push_type' => 0]);
													$tagUser = WorkTagGroupUserStatistic::findOne(['send_id' => $this->sendId, 'user_id' => $workUser->id]);
													if (empty($sendUserSta)) {
														if (!empty($tagUser)) {
															$tagUser->status = 1;
															$tagUser->save();
														}
													}else{
														if (!empty($tagUser)) {
															$tagUser->status = 2;//排队中
															$tagUser->save();
														}
													}

												}
											}


										}

									}

								} catch (\Exception $e) {

								}
							}
						}

						$groupUser = WorkGroupSendingUser::find()->where(['send_id'=>$this->sendId,'push_type'=>0])->one();
						if(empty($groupUser)){
							$group->status = 1;
							$group->save();
						}

					}else{
						$msgIds = $group->success_list;
						if (!empty($msgIds)) {
							$msg = explode(',', $msgIds);
							if (!empty($msg)) {
								foreach ($msg as $val) {
									try {
										$workApi = WorkUtils::getWorkApi($group->corp_id, WorkUtils::EXTERNAL_API);
										$result  = $workApi->ECGetGroupMsgResult($val);
										if ($result['errcode'] == 0) {
											$detail_list = $result['detail_list'];
											if (!empty($detail_list)) {
												$userId = [];
												foreach ($detail_list as $key => $listVal) {
													if (!empty($listVal['userid'])) {
														array_push($userId, $listVal['userid']);
													}
												}
												$userId   = array_unique($userId);
												$statData = [];
												if (!empty($userId)) {
													foreach ($userId as $k => $uData) {
														$statData[$k]['userid'] = $uData;
														$user                   = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $uData]);
														$tag                    = WorkTagGroupStatistic::findOne([$pullSendId => $this->sendId, 'user_id' => $user->id, 'push_type' => 1]);
														$flag                   = 0;
														if (!empty($tag)) {
															$flag = 1;
														}
														$statData[$k]['flag'] = $flag;
													}
												}
												foreach ($detail_list as $list) {
													foreach ($statData as $userVal) {
														if ($list['userid'] == $userVal['userid']) {
															if (empty($userVal['flag'])) {
																$contact  = WorkExternalContact::findOne(['corp_id' => $group->corp_id, 'external_userid' => $list['external_userid']]);
																$workUser = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $list['userid']]);
																if (!empty($contact) && !empty($workUser)) {
																	$statistic = WorkTagGroupStatistic::findOne([$pullSendId => $this->sendId, 'external_id' => $contact->id, 'user_id' => $workUser->id]);
																	if (!empty($statistic)) {
																		if (isset($list['send_time']) && !empty($list['send_time'])) {
																			$statistic->push_time = date('Y-m-d H:i:s', $list['send_time']);
																		}
																		$statistic->send = $list['status'];
																		if (!$statistic->validate() || !$statistic->save()) {
																			\Yii::error(SUtils::modelError($statistic), 'GetGroupMsgResultJob-error');
																		}
																		if ($list['status'] == 1) {
																			$stat           = WorkTagGroupUserStatistic::findOne([$pullSendId => $this->sendId, 'user_id' => $workUser->id]);
																			$realNum        = intval($stat->real_num);
																			$realNum        = $realNum + 1;
																			$stat->real_num = $realNum;
																			\Yii::error($realNum, '$realNum');
																			\Yii::error($stat->id, 'id');
																			$stat->save();

																			if (isset($group->is_redpacket) && $group->is_redpacket == 1){
																				$create_time = isset($list['send_time']) && !empty($list['send_time']) ? $list['send_time'] : time();
																				WorkGroupSendingRedpacketSend::updateAll(['create_time' => $create_time, 'is_send' => 1], ['group_send_id' => $statistic->id, 'create_time' => 0]);
																			}
																		}
																		if ($list['status'] != 0) {
																			WorkTagGroupStatistic::updateAll(['push_type' => 1], [$pullSendId => $this->sendId, 'user_id' => $workUser->id]);

																			$userSta = WorkTagGroupUserStatistic::findOne([$pullSendId => $this->sendId, 'user_id' => $workUser->id]);
																			if (!empty($userSta)) {
																				$userSta->status = 1;
																				if (isset($list['send_time']) && !empty($list['send_time'])) {
																					$userSta->push_time = $list['send_time'];
																				}
																				$userSta->save();
																			}

																			if ($this->type == 0) {
																				//记录客户行为轨迹
																				$remark = '';
																				if ($list['status'] == 1) {
																					$remark = '发送成功';
																				} elseif ($list['status'] == 2) {
																					$remark = '客户已不是好友';
																				} elseif ($list['status'] == 3) {
																					$remark = '客户接收已达上限';
																				}
																				$user = UserCorpRelation::findOne(['corp_id' => $group->corp_id]);
																				ExternalTimeLine::addExternalTimeLine(['uid' => $user->uid, 'external_id' => $contact->id, 'remark' => $remark, 'user_id' => $workUser->id, 'event' => 'tag_send_group', 'related_id' => $this->sendId]);

																			} elseif ($this->type == 1 && $group->is_redpacket == 0) {
																				$group->status = 1;
																				$content       = $group->content;
																				$text          = '';
																				if (strpos($content, 'text') !== false) {
																					$text .= '文本、';
																				}
																				if (strpos($content, 'media_id') !== false) {
																					$text .= '图片、';
																				}
																				if (strpos($content, 'link') !== false) {
																					$text .= '图文、';
																				}
																				if (strpos($content, 'miniprogram') !== false) {
																					$text .= '图文、';
																				}
																				//记录客户行为轨迹
																				$remark = '';
																				if ($list['status'] == 1) {
																					$remark = '发送成功';
																				} elseif ($list['status'] == 2) {
																					$remark = '客户已不是好友';
																				} elseif ($list['status'] == 3) {
																					$remark = '客户接收已达上限';
																				}
																				$text   = trim($text, '、');
																				$remark = $text . '，发送结果：' . $remark;
																				$user   = UserCorpRelation::findOne(['corp_id' => $group->corp_id]);
																				ExternalTimeLine::addExternalTimeLine(['uid' => $user->uid, 'external_id' => $contact->id, 'remark' => $remark, 'user_id' => $workUser->id, 'event' => 'customer_send_group', 'related_id' => $this->sendId]);

																			}
																		}
																	}
																}
															}
														}
													}

												}
											}
										}
										if ($this->type == 1) {
											//更改整个任务的发送状态
											$tagUser = WorkTagGroupUserStatistic::findOne(['send_id' => $group->id, 'status' => [0, 2]]);
											if (empty($tagUser)) {
												if ($group->status == 3) {
													$group->status = 1;
												}
											}
										}
										$group->queue_id = 0;
										$group->save();
									} catch (\Exception $e) {
										$group->queue_id = 0;
										$group->save();
										\Yii::error($e->getMessage(), 'GetGroupMsgResultJob');
									}

								}
							}
						}
					}

				}
			} else {
				$group  = WorkGroupSending::findOne($this->sendId);
				$msgIds = $group->success_list;
				if (!empty($msgIds)) {
					$msg = explode(',', $msgIds);
					if (!empty($msg)) {
						foreach ($msg as $val) {
							try {
								$workApi = WorkUtils::getWorkApi($group->corp_id, WorkUtils::EXTERNAL_API);
								$result  = $workApi->ECGetGroupMsgResult($val);
								$status  = 0;
								if ($result['errcode'] == 0) {
									$detail_list = $result['detail_list'];
									if (!empty($detail_list)) {

										$userId = [];
										foreach ($detail_list as $key => $listVal) {
											if (!empty($listVal['userid'])) {
												array_push($userId, $listVal['userid']);
											}
										}
										$userId   = array_unique($userId);
										$statData = [];
										if (!empty($userId)) {
											foreach ($userId as $k => $uData) {
												$statData[$k]['userid'] = $uData;
												$user                   = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $uData]);
												$tag                    = WorkTagGroupStatistic::findOne(['send_id' => $this->sendId, 'user_id' => $user->id, 'push_type' => 1]);
												$flag                   = 0;
												if (!empty($tag)) {
													$flag = 1;
												}
												$statData[$k]['flag'] = $flag;
											}
										}

										foreach ($detail_list as $list) {
											foreach ($statData as $userVal) {
												if ($list['userid'] == $userVal['userid']) {
													if (empty($userVal['flag'])) {
														$chat     = WorkChat::findOne(['corp_id' => $group->corp_id, 'chat_id' => $list['chat_id']]);
														$workUser = WorkUser::findOne(['corp_id' => $group->corp_id, 'userid' => $list['userid']]);
														if (!empty($chat) && !empty($workUser)) {
															$statistic = WorkTagGroupStatistic::findOne(['send_id' => $this->sendId, 'chat_id' => $chat->id, 'user_id' => $workUser->id]);
															if (!empty($statistic)) {
																if (isset($list['send_time']) && !empty($list['send_time'])) {
																	$statistic->push_time = date('Y-m-d H:i:s', $list['send_time']);
																}
																$statistic->send = $list['status'];
																if (!$statistic->validate() || !$statistic->save()) {
																	\Yii::error(SUtils::modelError($statistic), 'GetGroupMsgResultJob-error');
																}
																if ($list['status'] == 1) {
																	$stat           = WorkTagGroupUserStatistic::findOne(['send_id' => $this->sendId, 'user_id' => $workUser->id]);
																	$realNum        = intval($stat->real_num);
																	$realNum        = $realNum + 1;
																	$stat->real_num = $realNum;
																	\Yii::error($realNum, '$realNum');
																	\Yii::error($stat->id, 'id');
																	$stat->save();

																	if (isset($group->is_redpacket) && $group->is_redpacket == 1) {
																		$create_time = isset($list['send_time']) && !empty($list['send_time']) ? $list['send_time'] : time();
																		WorkGroupSendingRedpacketSend::updateAll(['create_time' => $create_time, 'is_send' => 1], ['send_id' => $this->sendId, 'user_id' => $workUser->id, 'external_userid' => $chat->id, 'is_chat' => 1, 'create_time' => 0]);
																	}
																}

																if ($list['status'] != 0) {
																	WorkTagGroupStatistic::updateAll(['push_type' => 1], ['send_id' => $this->sendId, 'user_id' => $workUser->id]);

																	$userSta = WorkTagGroupUserStatistic::findOne(['send_id' => $this->sendId, 'user_id' => $workUser->id]);
																	if (!empty($userSta)) {
																		$userSta->status = 1;
																		if (isset($list['send_time']) && !empty($list['send_time'])) {
																			$userSta->push_time = $list['send_time'];
																		}
																		$userSta->save();
																	}
																	$status = 1;

																}

															}

														}

													}
												}
											}

										}

									}
								}
								if ($status != 0) {
									$group->status = $status;
								}
								$group->queue_id = 0;
								$group->save();
							} catch (\Exception $e) {
								$group->queue_id = 0;
								$group->save();
								\Yii::error($e->getMessage(), 'GetGroupChatMsgResultJob');
							}
						}
					}
				}
			}
		}

	}