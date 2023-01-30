<?php

	namespace app\modules\api\controllers;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\models\ExternalTimeLine;
	use app\models\RedPack;
	use app\models\TempMedia;
    use app\models\WorkCorp;
	use app\models\WorkExternalContact;
	use app\models\WorkMomentEdit;
	use app\models\WorkMomentGoods;
    use app\models\WorkMomentRemind;
    use app\models\WorkMomentReply;
	use app\models\WorkMoments;
	use app\models\WorkMomentsBase;
	use app\models\WorkMomentSetting;
	use app\models\WorkMomentUserConfig;
	use app\models\WorkUser;
	use app\modules\api\components\BaseController;
	use app\util\SUtils;
    use yii\data\Pagination;
    use yii\db\Exception;
	use yii\helpers\Json;

	class MomentsWebController extends BaseController
	{
		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/get-lists
		 * @title           获取内容列表
		 * @description     获取内容列表
		 * @method   post
		 * @url  http://{host_name}/api/moment-web/get-lists
		 *
		 * @param userid 必须 int 企业成员userid
		 * @param corp_id 必须 int 企业微信id
		 * @param extId 必须 int  外部联系人id
		 * @param is_self 必须 int  是否自己
		 * @param lat 必须 int    经度
		 * @param lng 必须 int    维度
		 *
		 * @return_param    error int 状态码
		 * @return_param    heard_img   string 员工头像
		 * @return_param    goods_sum   string 点赞次数
		 * @return_param    info   array 附加内容图片活图文链接
		 * @return_param    create_time   string 创建时间
		 * @return_param    type   string 1、仅文本；2、图片；3、视频；4、图文
		 * @return_param    text   string 文本内容
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/20 19:28
		 */
		public function actionGetLists ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidDataException("请求方式出错");
			}
			$userid   = \Yii::$app->request->post("userid");
			$corp_id  = \Yii::$app->request->post("corp_id");
			$openid   = \Yii::$app->request->post("openid");
			$lat      = \Yii::$app->request->post("lat");
			$lng      = \Yii::$app->request->post("lng");
			$is_self  = \Yii::$app->request->post("is_self", 0);
			$page     = \Yii::$app->request->post("page", 0);
			$pageSize = \Yii::$app->request->post("pageSize", 15);
			$offset   = ($page - 1) * $pageSize;
			$corp     = WorkCorp::findOne(["corpid" => $corp_id]);
			if (empty($corp)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$workUser = WorkUser::findOne(["userid" => $userid, "status" => 1, "corp_id" => $corp->id]);
			if (empty($workUser)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$external = WorkExternalContact::findOne(["openid" => $openid, "corp_id" => $corp->id]);
			$moments  = WorkMoments::find()->alias("a")
				->leftJoin("{{%work_moments_base}} as b", "a.base_id = b.id")
				->where(["a.corp_id" => $corp->id, "a.user_id" => $workUser->id, "a.status" => 1, "b.is_del" => 0]);
			if (empty($is_self)) {
				$moments = $moments->andWhere(["b.status" => 1]);
			}
			if (empty($is_self)) {
				if (!empty($external)) {
					$moments = $moments->andWhere("IF( a.open_status = 1, TRUE, FIND_IN_SET( $external->id, a.open_range ) ) ");
					$moments = $moments->andWhere("IF( a.external_status = 1, TRUE, FIND_IN_SET($external->id, a.external_userid) )");
					$address = RedPack::getAddress($lat, $lng);
					if (!empty($address)) {
						$province = substr($address['province'], 0, strlen($address['province']) - 3);
						$tmpStr   = ($address['city'] == $address['province']) ? $address['district'] : $address['city'];
						$city     = substr($tmpStr, 0, strlen($tmpStr) - 3);
						$moments  = $moments->andWhere("(b.province is null AND b.city is null) or IF( b.province IS NULL, TRUE,IF( b.province = '全部', TRUE, b.province like '%" . $province . "%' )) AND IF(b.city IS NULL,TRUE,IF(b.city = '全部',TRUE,b.city like '%" . $city . "%'))");
					} else {
						$moments = $moments->andWhere("(b.province is null AND b.city is null) or (b.province = '全部' and b.city = '全部')");
					}
				} else {
					$moments = $moments->andWhere("a.open_status = 1");
				}
			}
			$count   = $moments->count();
			$moments = $moments->select("b.status,a.id,b.advanced_setting,a.text,a.type,a.create_time")
				->limit($pageSize)->offset($offset)
				->orderBy("a.create_time desc")
				->asArray()->all();
			foreach ($moments as $key => &$moment) {
				$moment["text"]      = urldecode($moment["text"]);
				$moment["heard_img"] = $workUser->avatar;
				$moment["text"]      = urldecode($moment["text"]);
				$moment["info"]      = WorkMoments::getMomentInfo($moment["type"], $moment["id"]);

			}

			if (!empty($external)) {
				$timeLine = ExternalTimeLine::find()
					->where(["external_id" => $external->id, "user_id" => $workUser->id, "event" => "moment_time"])
					->andWhere("event_time+60 > UNIX_TIMESTAMP()")->one();
				if (empty($timeLine)) {
					$str                   = "该客户打开成员【" . $workUser->name . "】历史朋友圈，停留1秒";
					$timeLine              = new ExternalTimeLine();
					$timeLine->external_id = $external->id;
					$timeLine->user_id     = $workUser->id;
					$timeLine->event       = "moment_time";
					$timeLine->event_time  = time();
					$timeLine->remark      = $str;
					$timeLine->save();
				}
			}

			return ["data" => empty($moments) ? [] : array_values($moments), "count" => $count, "user_id" => $workUser->id, "extId" => empty($external) ? 0 : $external->id, "timeLine" => empty($timeLine) ? 0 : $timeLine->id];
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/moment-setting
		 * @title           获取设置
		 * @description     获取设置
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/moment-setting
		 *
		 * @param userid 必须 int 企业成员userid
		 * @param corp_id 必须 int 企业微信id
		 *
		 * @return_param    error int 状态码
		 * @return_param    banner_url   string 背景
		 * @return_param    heard   string 头像
		 * @return_param    description   array 个性签名
		 * @return_param    is_description   int 是否自定义个性签名
		 * @return_param    is_heard   int 是否自定义头像
		 * @return_param    banner_type   int 是否自定义背景
		 * @return_param    title   int 网页标题
		 * @return_param    is_self   int 0不能改1能改
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/20 19:28
		 */
		public function actionMomentSetting ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidDataException("请求方式出错");
			}
			$userid  = \Yii::$app->request->post("userid");
			$user_id = \Yii::$app->request->post("user_id");
			$corp_id = \Yii::$app->request->post("corp_id");
			$corp    = WorkCorp::findOne(["corpid" => $corp_id]);
			if (empty($corp)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$workUser    = WorkUser::findOne(["userid" => $userid, "status" => 1, "corp_id" => $corp->id]);
			$workUserTmp = WorkUser::findOne(["userid" => $user_id, "corp_id" => $corp->id]);
			if (empty($workUser)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$setting = WorkMomentSetting::findOne(["corp_id" => $corp->id]);
			if (empty($setting)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$is_self = 0;
			if (!empty($workUserTmp)) {
				if ($workUser->userid == $user_id) {
					$is_self = 1;
				}
			}
			$userSetting = WorkMomentUserConfig::findOne(["corp_id" => $corp->id, "user_id" => $workUser->id]);
			$banner_info = json_decode($setting->banner_info, 255);
			if (!is_array($banner_info)) {
				$banner_str = $setting->banner_info;
			}
			$data                  = [];
			$data["corp_name"]     = $corp->corp_name;
			$data["default_heard"] = !empty($setting->is_heard) ? 1 : 0;
			$data["heard"]         = $setting->heard_img;
			if ($setting->is_heard == 1) {
				$data["heard"] = $workUser->avatar;
			}
			$data["description"]    = $setting->description;
			$data["name"]           = $workUser->name;
			$data["is_description"] = $setting->is_description;
			$data["is_heard"]       = $setting->is_heard;
			$data["banner_type"]    = $setting->banner_type;
			$data["title"]          = $setting->external_title;
			$data["is_context"]     = $setting->is_context;
			$data["id"]             = $setting->id;
			$data["user_id"]        = $workUser->id;
			$data["is_self"]        = $is_self;
			$data["can_goods"]        = $setting->can_goods;
            $data["is_synchro"]        = $setting->is_synchro;
			if (!empty($userSetting)) {
				if (!empty($userSetting->banner_info) && $setting->banner_type == 1) {
					$banner_info = json_decode($userSetting->banner_info, 255);
					if (!is_array($banner_info)) {
						$banner_str = $userSetting->banner_info;
					}
				}
				if (!empty($userSetting->heard) && $setting->is_heard == 1) {
					$data["heard"]         = $userSetting->heard;
					$data["default_heard"] = 0;
				}
				if ($setting->is_description == 1) {
					$data["description"] = $userSetting->description;
				}
			}
			if (is_array($banner_info)) {
				$data["banner_url"]   = is_null($banner_info["banner_info"]) ? '' : $banner_info["banner_info"];
				$data["s_banner_url"] = is_null($banner_info["s_banner_info"]) ? '' : $banner_info["s_banner_info"];
			} else {
				$data["s_banner_url"] = $data["banner_url"] = empty($banner_str) ? "" : $banner_str;
			}

			return $data;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/moment-detail
		 * @title           获取具体内容
		 * @description     获取具体内容
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/moment-detail
		 *
		 * @param momentId 必须 int 内容id
		 * @param extId 必须 int 外部联系人id
		 * @param is_self 必须 int 是否自己
		 *
		 * @return_param    error int 状态码
		 * @return_param    heard_img   string 员工头像
		 * @return_param    goods_sum   string 点赞次数
		 * @return_param    info   array 附加内容图片活图文链接
		 * @return_param    create_time   string 创建时间
		 * @return_param    type   string 1、仅文本；2、图片；3、视频；4、图文
		 * @return_param    text   string 文本内容
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/20 19:28
		 */
		public function actionMomentDetail ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidDataException("请求方式出错");
			}
			$momentId = \Yii::$app->request->post("momentId");
			$extId    = \Yii::$app->request->post("extId");
			$userId    = \Yii::$app->request->post("userId");
			$is_self  = \Yii::$app->request->post("is_self", 0);
			$corp_id  = \Yii::$app->request->post("corp_id", '');
			if(!empty($userId)) {
				$extId = '';
			} else {
				$userId = '';
			}
			$moment   = WorkMoments::find()->alias("a")
				->leftJoin("{{%work_moments_base}} as b", "a.base_id = b.id")
				->leftJoin("{{%work_moments_audit}} as c", "b.audit_id = c.id")
				->where(["a.id" => $momentId])
				->select("a.*,b.title,b.status,c.reply replys,b.is_mobile")
				->asArray()->one();
			if (empty($moment)) {
				return ["error" => 1, "msg" => "暂无数据"];
			}
            $moment["uid"] = WorkCorp::getUidByCorpid($corp_id);
			$moment["info"] = WorkMoments::getMomentInfo($moment["type"], $moment["id"]);
			$moment["text"] = urldecode($moment["text"]);
			$workUser       = WorkUser::findOne($moment["user_id"]);
			$external       = WorkExternalContact::findOne($extId);
			$moment["line"] = [];
			if (!empty($is_self)) {
				WorkMoments::getExternalTimeLine($moment["id"], empty($workUser) ? 0 : $workUser->id, $moment["line"]);
			}
			if (!empty($external) && $moment["status"] == 1 && empty($is_self)) {
				$timeLine = ExternalTimeLine::find()
					->where(["external_id" => $external->id, "user_id" => $workUser->id, "related_id" => $moment["id"], "event" => "moment_time"])
					->andWhere("event_time+60 > UNIX_TIMESTAMP()")->one();
				if (empty($timeLine)) {
					$str                   = "该客户打开成员【" . $workUser->name . "】历史朋友圈，查看【" . $moment["title"] . "】停留1秒";
					$timeLine              = new ExternalTimeLine();
					$timeLine->external_id = $external->id;
					$timeLine->user_id     = $workUser->id;
					$timeLine->event       = "moment_time";
					$timeLine->related_id  = $moment["id"];
					$timeLine->event_time  = time();
					$timeLine->remark      = $str;
					$timeLine->save();
				}
			}
			$moment["timeLine"] = empty($timeLine) ? 0 : $timeLine->id;
			//获取朋友圈点赞/评论
            if(!empty($userId)) {
                $WorkUser = WorkUser::findOne(['userid' => $userId, 'corp_id' => $moment['corp_id']]);
                $userId = empty($WorkUser) ? '' : $WorkUser->id;
                $is_self = $moment['user_id'] == $userId ? 1 : 0;
            }
            $moment["goods"] = WorkMoments::getMomentGoodHeardImage($momentId);
            $moment["reply"] = WorkMoments::getMomentReply($momentId, '', $extId, $userId, $is_self);
            //该用户是否点赞
            $moment["is_good"] = WorkMoments::getIsGood($momentId, $extId, $userId);
            $moment['can_goods'] = WorkMomentSetting::findOne(['corp_id' => $moment['corp_id']])['can_goods'];
			return $moment;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/moment-good
		 * @title           点赞
		 * @description     点赞
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/moment-good
		 *
		 * @param momentId 必须 int 内容id
		 * @param user_id 必须 int 员工id
		 * @param external_id 必须 int 外部联系人id
		 *
		 * @return_param    error int 状态码
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/20 19:28
		 */
		public function actionMomentGood ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidDataException("请求方式出错");
			}
			$momentId    = \Yii::$app->request->post("momentId");
			$user_id     = \Yii::$app->request->post("user_id");
			$external_id = \Yii::$app->request->post("external_id");
            $corp_id     = \Yii::$app->request->post("corp_id");
            if(empty($external_id) && empty($user_id)) {
                return ["error" => 1, "msg" => "未知客户"];
            }
            if(!empty($user_id)) {
                $external_id = '';
            } else {
                $user_id = '';
            }
            $corp = WorkCorp::findOne(["corpid" => $corp_id]);
            if (empty($corp)) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            $setting = WorkMomentSetting::findOne(["corp_id" => $corp->id]);
            if (empty($setting)) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            if (!$setting->can_goods) {
                return ["error" => 1, "msg" => "暂时无法点赞"];
            }
            $moments = WorkMoments::find()->where(['id' => $momentId])->asArray()->one();
            if(!$moments) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            $userName = WorkUser::find()->select('name')->where(['id' => $moments['user_id']])->andWhere(['corp_id' => $corp->id])->asArray()->one();
            if(!$userName) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            if($user_id) {
                $workUser = WorkUser::find()->where(['userid' => $user_id])->andWhere(['corp_id' => $corp->id])->asArray()->one();
                $user_id  = $workUser['id'];
            }
			$moment = WorkMomentGoods::find()
                ->where(["moment_id" => $momentId])
                ->andFilterWhere(["user_id" => $user_id])
                ->andFilterWhere(["external_id" => $external_id])
                ->one();
            $status = 0;
            if (empty($moment)) {
				$moment              = new WorkMomentGoods();
				$moment->moment_id   = $momentId;
				$moment->user_id     = $user_id;
				$moment->external_id = $external_id;
				$moment->status      = 1;
                $str                 = "该客户点赞了成员【".$userName['name']."】".date('Y-m-d', strtotime($moments['create_time']))."的历史朋友圈";
            } else {
                if($moment->status == 0) {
                    $moment->status = 1;
                    $status         = 0;
                    $str            = "该客户点赞了成员【".$userName['name']."】".date('Y-m-d', strtotime($moments['create_time']))."的历史朋友圈";
                } else {
                    $moment->status = 0;
                    $status         = 1;
                    $str            = "该客户取消点赞了成员【".$userName['name']."】".date('Y-m-d', strtotime($moments['create_time']))."的历史朋友圈";
                }
			}
			$moment->create_time = date('Y-m-d H:i:s');
            $moment->save();

            //互动轨迹
            if($external_id) {
                $timeLine              = new ExternalTimeLine();
                $timeLine->external_id = $external_id;
                $timeLine->user_id     = $moments['user_id'];
                $timeLine->event       = "moment_goods";
                $timeLine->related_id  = $momentId;
                $timeLine->event_time  = time();
                $timeLine->remark      = $str;
                $timeLine->save();
            }

            if($moments['user_id'] != $user_id) {
                //添加消息通知
                $WorkMomentRemind = new WorkMomentRemind();
                $WorkMomentRemind->remindAdd($moments['user_id'], $user_id, $external_id, $moment->id, $moments['id'], $status);
            }

			return;
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/moment-reply
		 * @title           朋友圈回复
		 * @description     朋友圈回复
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/moment-reply
		 *
		 * @param momentId 必须 int 内容id
		 * @param user_id 必须 int 员工id
		 * @param external_id 必须 int 外部联系人id
		 * @param replay_id 必须 int 回复指定内容id
		 * @param is_del 必须 int 刪除指定回复内容
		 * @param context 必须 int 内容
		 *
		 * @return_param    error int 状态码
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/24 13:14
		 */
		public function actionMomentReply ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidDataException("请求方式出错");
			}
			$momentId    = \Yii::$app->request->post("momentId");
			$replay_id   = \Yii::$app->request->post("replay_id");
			$user_id     = \Yii::$app->request->post("user_id");
			$external_id = \Yii::$app->request->post("external_id");
			$context     = \Yii::$app->request->post("context");
			$is_del      = \Yii::$app->request->post("is_del");
            $corp_id     = \Yii::$app->request->post("corp_id");
            $is_replay   = true;
            if(empty($external_id) && empty($user_id)) {
                return ["error" => 1, "msg" => "未知客户"];
            }
            if(!empty($user_id)) {
                $external_id = '';
            } else {
                $user_id = '';
            }
            $corp = WorkCorp::findOne(["corpid" => $corp_id]);
            if (empty($corp)) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            $setting = WorkMomentSetting::findOne(["corp_id" => $corp->id]);
            if (empty($setting)) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            if (!$setting->can_reply) {
                return ["error" => 1, "msg" => "暂时无法评论"];
            }
            $moments = WorkMoments::find()->where(['id' => $momentId])->asArray()->one();
            if(!$moments) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            $userName = WorkUser::find()->where(['id' => $moments['user_id'], 'corp_id' => $corp->id])->asArray()->one();
            if(!$userName) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            $name = '';
            if($user_id) {
                $workUser = WorkUser::find()->where(['userid' => $user_id])->andWhere(['corp_id' => $corp->id])->asArray()->one();
                $user_id  = $workUser['id'];
                $name = $workUser['name'];
            } elseif ($external_id) {
                $contact = WorkExternalContact::findOne($external_id);
                $name = $contact->name_convert;
            }
            $status = 0;
			if (!empty($replay_id) && !empty($is_del)) {
				WorkMomentReply::updateAll(["status" => 0], ["id" => $replay_id]);
				$is_replay = false;
                $status    = 1;
                $reply     = WorkMomentReply::find()->where(['id' => $is_replay])->asArray()->one();
                if(!empty($reply['reply_id'])) {
                    $replyUser = WorkMomentReply::find()->where(['id' => $reply['reply_id']])->asArray()->one();
                    $remind_user_id = empty($replyUser['user_id']) ? $replyUser['external_id'] : $replyUser['user_id'];
                } else {
                    $remind_user_id = $moments['user_id'];
                }
                $str       = "该客户删除了对成员【".$userName['name']."】".date('Y-m-d', strtotime($moments['create_time']))."的历史朋友圈的评论";
			} else {
                if(!empty($replay_id)) {
                    $replyUser = WorkMomentReply::find()->where(['id' => $replay_id])->asArray()->one();
                    $remind_user_id = empty($replyUser['user_id']) ? $replyUser['external_id'] : $replyUser['user_id'];
                } else {
                    $remind_user_id = $moments['user_id'];
                }
				$replay              = new WorkMomentReply();
				$replay->external_id = $external_id;
				$replay->user_id     = $user_id;
				if (!empty($replay_id)) {
					$replay->reply_id = $replay_id;
				}
				$replay->moment_id = $momentId;
				$replay->content   = urlencode($context);
				$replay->status    = 1;
				$replay->save();
                $replay_id = $replay->id;
                $str       = "该客户评论了成员【".$userName['name']."】".date('Y-m-d', strtotime($moments['create_time']))."的历史朋友圈";
			}
            //互动轨迹
            if($external_id) {
                $timeLine              = new ExternalTimeLine();
                $timeLine->external_id = $external_id;
                $timeLine->user_id     = $moments['user_id'];
                $timeLine->event       = "moment_reply";
                $timeLine->related_id  = $momentId;
                $timeLine->event_time  = time();
                $timeLine->remark      = $str;
                $timeLine->save();
            }

            //添加消息通知
            if($user_id != $remind_user_id) {
                $WorkMomentRemind = new WorkMomentRemind();
                $arr = [$remind_user_id, $moments['user_id']];
                $arr = array_unique($arr);
                foreach ($arr as $val) {
                    if($val == $user_id || $val == $external_id) {
                        continue;
                    }
                    $WorkMomentRemind->remindAdd($val, $user_id, $external_id, $replay_id, $moments['id'], $status,2);
                }
            }
            //应用消息通知
//            if(empty($is_del) && $moments['user_id'] == $remind_user_id && $user_id != $moments['user_id']) {
//                $detail = \Yii::$app->params["web_url"];
//                $detail.='/h5/pages/moments/list?user_id='.$userName['userid'].'&corpid='.$corp_id.'&agentId='.$setting->agent_id;
//                $text = "用户 ".$name." 给你的朋友圈评论了。快去看看吧。<a href='$detail'>查看详情</a>";
//                WorkMomentsBase::SendAgentMessage([$userName['userid']], $setting->agent_id, $text, $corp->id);
//            }
//			\Yii::$app->websocket->send([
//				'channel' => 'web-message',
//				'to'      => $momentId . WorkMoments::NAME,
//				'type'    => WorkMoments::NAME,
//				'info'    => [
//					'context'   => $context,
//					'replay_id' => $replay_id,
//					'type'      => WorkMoments::NAME,
//					'is_replay' => $is_replay,
//				]
//			]);

			return;
		}

        /**
         * 新消息提醒列表
         */
        public function actionMomentRemind ()
        {
            if (\Yii::$app->request->isPost) {
                throw new InvalidDataException("请求方式出错");
            }
            $corp_id        = \Yii::$app->request->get('corp_id');
            $user_id        = \Yii::$app->request->get('user_id');
            $external_id    = \Yii::$app->request->get('external_id');
            $moment_user_id = \Yii::$app->request->get('moment_user_id');
            $is_new         = \Yii::$app->request->get('is_new', 0);
            $pageSize       = \Yii::$app->request->get('pageSize', 10);

            if(empty($external_id) && empty($user_id)) {
                return [];
            }
            $corp = WorkCorp::findOne(["corpid" => $corp_id]);
            if (empty($corp)) {
                return ["error" => 1, "msg" => "暂无数据"];
            }
            if(!empty($user_id)) {
                $user = WorkUser::findOne(['userid' => $user_id, 'corp_id' => $corp->id]);
                $remind_user_id = $user->id;
            } else {
                $remind_user_id = $external_id;
            }
            if($moment_user_id) {
                $moment_user = WorkUser::findOne(['userid' => $moment_user_id, 'corp_id' => $corp->id]);
                $moment_user_id = $moment_user->id;
            }
            $pagination = '';
            if($is_new) {
                $list = WorkMomentRemind::find()
                    ->where(['is_show' => 0])
                    ->andWhere(['remind_user_id' => $remind_user_id])
                    ->andWhere(['moment_user_id' => $moment_user_id])
                    ->orderBy('create_time desc')
                    ->asArray()
                    ->all();
                $id = array_column($list, 'id');
                if($is_new == 1) {
                    WorkMomentRemind::updateAll(['is_show' => 1], ['id' => $id]);
                }
            } else {
                $query = WorkMomentRemind::find()
                    ->where(['status' => 0])
                    ->andWhere(['remind_user_id' => $remind_user_id])
                    ->andWhere(['moment_user_id' => $moment_user_id]);
                $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);

                $list = $query
                    ->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->orderBy('create_time desc')
                    ->asArray()
                    ->all();
            }
            foreach ($list as $key => $val) {
                $userInfo = '';
                $reply    = '';
                if($val['user_id']) {
                    $userInfo = WorkUser::find()->select(['name', 'thumb_avatar avatar'])->where(['id' => $val['user_id']])->asArray()->one();
                } else if($val['external_id']) {
                    $userInfo = WorkExternalContact::find()->select(['name_convert name', 'avatar'])->where(['id' => $val['external_id']])->asArray()->one();
                }
                $reply_user = '';
                if($val['type'] == 2) {
                    $reply = WorkMomentReply::find()->where(['id' => $val['related_id']])->asArray()->one();
                    $reply['content'] = urldecode($reply['content']);
                    if($reply['reply_id']) {
                        $upreply = WorkMomentReply::find()->where(['id' => $reply['reply_id']])->asArray()->one();
                        if($upreply['user_id']) {
                            $reply_user = WorkUser::find()->select(['name', 'thumb_avatar avatar'])->where(['id' => $upreply['user_id']])->asArray()->one();
                        } else if($upreply['external_id']) {
                            $reply_user = WorkExternalContact::find()->select(['name_convert name', 'avatar'])->where(['id' => $upreply['external_id']])->asArray()->one();
                        }
                    }
                }
                $list[$key]['reply_user'] = $reply_user;
                $list[$key]['user_info'] = $userInfo;
                $list[$key]['reply']     = $reply;
                $moment = WorkMoments::find()->select('base_id,status')->where(['id' => $val['moment_id']])->asArray()->one();
                $base = WorkMomentsBase::findOne($moment['base_id']);
                $base_type = 1;
                $image = '';
                if($base->type == 1) {
                    $image = urldecode($base['context']);
                    $base_type = $base->type;
                } elseif ($base->type == 2) {
                    $base_type = $base->type;
                    $image = @json_decode($base->info, true)[0]['local_path'];
                } elseif ($base->type == 3) {
                    $base_type = $base->type;
                    $image = @json_decode($base->info, true)[0]['local_path'];
                } else if($base->type == 4) {
                    $base_type = $base->type;
                    $json = json_decode($base->info, true);
                    if(isset($json[0]['local_path'])) {
                        $image = $json[0]['local_path'];
                    } else {
                        $image = @$json[0]['pic_url'];
                    }
                }
                $list[$key]['base_type'] = $base_type;
                $list[$key]['image'] = $image;
                $time1        = strtotime(date('Y-m-d', time()));
                $time2        = strtotime("+1 day") - 1;
                $time3        = strtotime($val["create_time"]);
                if ($time3 > $time1 && $time3 < $time2) {
                    $list[$key]["create_time"] = date("H:i", $time3);
                } else {
                    $list[$key]["create_time"] = date("Y-m-d H:i", $time3);
                }
                $is_del = $base->is_del == 0 ? $moment['status'] == 0 ? 1 : 0 : $base->is_del;
                $list[$key]['moment_status'] = $is_del;
            }

            return ['data' => $list, 'total_count' => empty($pagination) ? count($list) : (int)$pagination->totalCount, 'page_count' => empty($pagination) ? 1 : (int)$pagination->pageCount];
        }

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/moments-upload
		 * @title           上传文件
		 * @description     上传文件
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/moments-upload
		 *
		 * @param type 必须 string 文件类型支持png,jpeg,mp4
		 * @param md5 必须 string 文件md5值
		 *
		 * @return_param    error int 状态码
		 * @return_param    local_path string 图片地址
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/24 16:51
		 */
		public function actionMomentsUpload ()
		{
			$type     = \Yii::$app->request->post("type");
			$md5      = \Yii::$app->request->post("md5");
			$is_heard = \Yii::$app->request->post("is_heard", false);

			return TempMedia::UploadTempFile($type, $md5, $is_heard == 1);
		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/user-setting
		 * @title           员工朋友圈自定义配置
		 * @description     员工朋友圈自定义配置
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/user-setting
		 *
		 * @param user_id 必须 string 员工id
		 * @param corp_id 必须 string 企业id
		 * @param type 必须 string 1头像2签名3背景
		 * @param heard 必须 string  头像地址
		 * @param description 必须 string  个性签名
		 * @param banner_info 必须 string  背景
		 *
		 * @return_param    error int 状态码
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/10/24 16:51
		 */
		public function actionUserSetting ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidDataException("请求方式出错");
			}
			$corp_id             = \Yii::$app->request->post("corp_id");
			$user_id             = \Yii::$app->request->post("user_id");
			$type                = \Yii::$app->request->post("type");
			$heard_img           = \Yii::$app->request->post("heard");
			$description         = \Yii::$app->request->post("description");
			$ra["banner_info"]   = \Yii::$app->request->post("banner_info");
			$ra["s_banner_info"] = \Yii::$app->request->post("s_banner_info");
			$corp                = WorkCorp::findOne(["corpid" => $corp_id]);
			if (empty($corp)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$workUser = WorkUser::findOne(["userid" => $user_id, "status" => 1, "corp_id" => $corp->id]);
			if (empty($workUser)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$setting = WorkMomentSetting::findOne(["corp_id" => $corp->id]);
			if (empty($setting)) {
				return ["error" => 0, "msg" => "暂无数据"];
			}
			$userSetting = WorkMomentUserConfig::findOne(["corp_id" => $corp->id, "user_id" => $workUser->id]);
			if (empty($userSetting)) {
				$userSetting          = new WorkMomentUserConfig();
				$userSetting->corp_id = $corp->id;
				$userSetting->user_id = $workUser->id;
				$userSetting->status  = 1;
			}
			if ($type == 1) {
				if ($setting->is_heard == 0) {
					return ['error' => 0, "msg" => "头像不允许修改"];
				}
				$userSetting->heard = $heard_img;
			} elseif ($type == 2) {
				if ($setting->is_description == 0) {
					return ['error' => 0, "msg" => "签名不允许修改"];
				}
				$userSetting->description = $description;
			} elseif ($type == 3) {
				if ($setting->banner_type == 0) {
					return ['error' => 0, "msg" => "背景不允许修改"];
				}
				$userSetting->banner_info = Json::encode($ra, JSON_UNESCAPED_UNICODE);
			}
			$userSetting->save();

			return ['error' => 0];

		}

		/**
		 * showdoc
		 * @catalog         数据接口/api/moments-web/moment-add
		 * @title           员工发表朋友圈
		 * @description     员工发表朋友圈
		 * @method   post
		 * @url  http://{host_name}/api/moments-web/moment-add
		 *
		 * @param userid 必须 string 员工userid
		 * @param corpid 必须 string 企业corpid
		 * @param info 必须 array 内容图片视频图文内容引擎携带内容引擎id,attachment
		 * @param type 必须 string  1文本2图片3视频4图文
		 * @param context 必须 string  内容
		 * @param save_edit 必须 int 1保存此次编辑机
		 * @param is_edit 必须 int 1查看上次保留编辑2删除上次编辑
		 * @param edit_id 必须 int 1查看上次保留编辑id
		 * @param momentId 必须 int 朋友圈内容id
		 *
		 * @return_param    error int 状态码
		 *
		 * @remark          Create by PhpStorm. User: sym. Date: 2020/11/6 15:13
		 */
		public function actionMomentAdd ()
		{
			if (\Yii::$app->request->isGet) {
				throw new InvalidParameterException("请求方式错误");
			}
			$corp_id     = \Yii::$app->request->post("corpid");
			$userid      = \Yii::$app->request->post("userid");
			$momentId    = \Yii::$app->request->post("momentId", 0);
			$save_edit   = \Yii::$app->request->post("save_edit", 0);
			$is_edit     = \Yii::$app->request->post("is_edit", 0);
			$edit_id     = \Yii::$app->request->post("edit_id", 0);
			$info        = \Yii::$app->request->post("info", []);
			$infoData    = Json::encode($info, JSON_UNESCAPED_UNICODE);
			$type        = \Yii::$app->request->post("type");
			$context     = \Yii::$app->request->post("context");
			$Transaction = \Yii::$app->db->beginTransaction();
			try {
				$corp = WorkCorp::findOne(["corpid" => $corp_id]);
				if (empty($corp)) {
					return ["error" => 0, "msg" => '暂无数据'];
				}
				$workUser = WorkUser::findOne(["corp_id" => $corp->id, "userid" => $userid]);
				$setting  = WorkMomentSetting::findOne(["corp_id" => $corp->id]);
				if (empty($workUser) || empty($setting)) {
					return ["error" => 0, "msg" => '暂无数据'];
				}
				if ($setting->is_context == WorkMomentSetting::IS_CONTEXT_FALSE) {
					return ["error" => 0, "msg" => '无权限发表内容'];
				}
				if (!empty($is_edit) && empty($momentId)) {
					if ($is_edit == 1) {
						$edit = WorkMomentEdit::findOne(["corp_id" => $corp->id, "user_id" => $workUser->id]);
						if (!empty($edit)) {
							$edit = $edit->toArray();
							$info = Json::decode($edit["info"]);
							unset($edit["info"]);
							$edit = array_merge($edit, $info);
						}

						return $edit;
					} else {
						WorkMomentEdit::deleteAll(["user_id" => $workUser->id, "corp_id" => $corp->id, "id" => $edit_id]);
						$Transaction->commit();

						return ["error" => 0];
					}
				}
				if (!empty($is_edit) && !empty($momentId)) {
					$detail = WorkMoments::find()->alias("a")
						->leftJoin("{{%work_moments_base}} as b", "a.base_id = b.id")
						->leftJoin("{{%work_moments_audit}} as c", "b.audit_id = c.id")
						->where(["a.id" => $momentId, "a.user_id" => $workUser->id, "a.corp_id" => $corp->id])
						->select("b.*,a.id as ids,b.info,c.reply")->asArray()->one();
					if (!empty($detail)) {
						if (!empty($detail["info"])) {
							$detail["info"] = Json::decode($detail["info"]);
						}
						$detail["context"] = urldecode($detail["context"]);
					}

					return ["error" => 0, "data" => $detail];
				}
				if ($save_edit == 1) {
					//保存编辑
					$data["info"]    = $info;
					$data["context"] = $context;
					$data["type"]    = $type;
					$edit            = WorkMomentEdit::findOne(["corp_id" => $corp->id, "user_id" => $workUser->id]);
					if (empty($edit)) {
						$edit          = new WorkMomentEdit();
						$edit->corp_id = $corp->id;
						$edit->user_id = $workUser->id;
					}
					$edit->info = Json::encode($data, JSON_UNESCAPED_UNICODE);
					if (!$edit->validate() || !$edit->save()) {
						throw new InvalidParameterException(SUtils::modelError($edit));
					}
				} else {
					if (empty($context) && empty($info)) {
						$Transaction->rollBack();

						return ["error" => 0, "msg" => "未提交任何信息"];
					}
					//重新编辑审核不通过内容|发表新内容
					if (!empty($momentId)) {
						$base = WorkMomentsBase::findOne(["corp_id" => $corp->id, "user_id" => $workUser->id, "id" => $momentId]);
						if (!empty($base)) {
							if ($base->status == 1) {
								$Transaction->rollBack();
								return ["error" => 0, "msg" => "已审核"];
							}
							if ($base->context != $context) {
								$base->context = $context;
							}
							if ($base->info != $infoData) {
								$base->info = $infoData;
							}
						}
					} else {
//						$bases = WorkMomentsBase::findAll(["corp_id" => $corp->id, "user_id" => $workUser->id, "status" => 2]);
//						foreach ($bases as $base) {
//							if (!empty($base) && (($base->context == $context && !empty($context) )|| $base->info == $infoData)) {
//								$Transaction->rollBack();
//
//								return ['error' => 0, "msg" => "存在相似内容待审核"];
//							}
//						}
						$base            = new WorkMomentsBase();
						$base->corp_id   = $corp->id;
						$base->user_id   = $workUser->id;
						$base->context   = $context;
						$base->is_mobile = 2;
					}
					$base->title    = "手机端发表";
					$base->type     = $type;
					$base->agent_id = $setting->agent_id;
					$base->status   = 2;
					//不需要审核直接通过
					if ($setting->is_audit == WorkMomentSetting::IS_AUDIT_TRUE) {
						$base->status = 1;
					}
					//前端好判断
					$base->ownership   = '[]';
					$base->info        = $infoData;
					$base->create_time = time();
					if (!$base->validate() || !$base->save()) {
						throw new InvalidParameterException(SUtils::modelError($base));
					}
					WorkMomentsBase::setMomentContext($base, $setting->agent_id, $info, false, true);
					WorkMomentEdit::deleteAll(["user_id" => $workUser->id, "corp_id" => $corp->id]);
				}
				$Transaction->commit();
			} catch (Exception $e) {
				$Transaction->rollBack();
				throw new InvalidParameterException($e->getMessage());
			}

			return ['error' => 0];
		}

	}