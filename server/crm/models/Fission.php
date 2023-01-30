<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncFissionJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;

	/**
	 * This is the model class for table "{{%fission}}".
	 *
	 * @property int    $id
	 * @property int    $uid                用户ID
	 * @property string $title              分组名称
	 * @property int    $is_end             在有效期内，奖品已无库存情况下，活动自动结束
	 * @property int    $is_friend          裂变要求:0新好友助力、1全部好友
	 * @property int    $is_invalid         删企微好友/被拉黑助力失效是否失效:0否、1是
	 * @property int    $is_brush           防刷检测:0否、1是
	 * @property string $brush_rule         防刷检测规则:{"brush_time":"","brush_num":""}
	 * @property string $complete_num       完成数量
	 * @property string $prize_type         奖品类型：0、实物，1、红包
	 * @property string $prize_rule         奖品规则:[{"fission_num":"","prize_name":"","prize_num":""}]
	 * @property string $prize_send_type    奖品发放类型：1、活动期间，2、活动结束
	 * @property string $help_limit         好友助力次数限制
	 * @property string $sex_type           性别类型：1、不限制，2、男性，3、女性，4、未知
	 * @property string $area_type          区域类型：1、不限制，2、部分地区
	 * @property string $area_data          区域数据
	 * @property string $pic_rule           图片规则
	 * @property string $is_option          引流成员选项:0选择引流成员、1渠道活码获取引流成员
	 * @property string $user_key           引流成员
	 * @property string $user               用户userID列表
	 * @property string $tag_ids            给客户打的标签
	 * @property string $corp_id            授权的企业ID
	 * @property string $agent_id           应用id
	 * @property string $config_id          联系方式的配置id
	 * @property string $qr_code            联系二维码的URL
	 * @property string $state              企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
	 * @property string $welcome            欢迎语
	 * @property string $help_tip           收到助力信息
	 * @property string $complete_tip       任务完成提醒
	 * @property string $end_tip            活动结束提醒
	 * @property int    $status             状态0删除、1未发布、2已发布、3到期结束、4奖品无库存结束、5、手动提前结束
	 * @property string $update_time        更新时间
	 * @property string $start_time         开始时间
	 * @property string $end_time           结束时间
	 * @property string $create_time        创建时间
	 * @property string $expire_time        活码过期时间
	 *
	 * @property User   $u
	 */
	class Fission extends \yii\db\ActiveRecord
	{
		const FISSION_HEAD = 'fission';
		const H5_URL = '/h5/pages/fission/ibk';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fission}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'is_end', 'is_friend', 'is_invalid', 'is_brush', 'status'], 'integer'],
				[['pic_rule', 'user_key', 'welcome', 'help_tip', 'complete_tip', 'end_tip'], 'string'],
				[['update_time', 'start_time', 'end_time', 'create_time'], 'safe'],
				[['title'], 'string', 'max' => 32],
				[['brush_rule', 'prize_rule'], 'string', 'max' => 250],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'uid'             => Yii::t('app', '用户ID'),
				'title'           => Yii::t('app', '活动标题'),
				'is_end'          => Yii::t('app', '在有效期内，奖品已无库存情况下，活动自动结束'),
				'is_friend'       => Yii::t('app', '裂变要求:0新好友助力、1全部好友'),
				'is_invalid'      => Yii::t('app', '删企微好友/被拉黑助力失效是否失效:0否、1是'),
				'is_brush'        => Yii::t('app', '防刷检测:0否、1是'),
				'brush_rule'      => Yii::t('app', '防刷检测规则:{"brush_time":"","brush_num":""}'),
				'complete_num'    => Yii::t('app', '完成数量'),
				'prize_type'      => Yii::t('app', '奖品类型：0、实物，1、红包'),
				'prize_rule'      => Yii::t('app', '奖品规则:[{"fission_num":"","prize_name":"","prize_num":"","amount":""}]'),
				'prize_send_type' => Yii::t('app', '奖品发放类型：1、活动期间，2、活动结束'),
				'help_limit'      => Yii::t('app', '好友助力次数限制'),
				'sex_type'        => Yii::t('app', '性别类型：1、不限制，2、男性，3、女性，4、未知'),
				'area_type'       => Yii::t('app', '区域类型：1、不限制，2、部分地区'),
				'area_data'       => Yii::t('app', '区域数据'),
				'pic_rule'        => Yii::t('app', '图片规则'),
				'is_option'       => Yii::t('app', '引流成员选项:0选择引流成员、1渠道活码获取引流成员'),
				'user_key'        => Yii::t('app', '引流成员'),
				'user'            => Yii::t('app', '用户userID列表'),
				'tag_ids'         => Yii::t('app', '给客户打的标签'),
				'corp_id'         => Yii::t('app', '授权的企业ID'),
				'agent_id'        => Yii::t('app', '应用id'),
				'config_id'       => Yii::t('app', '联系方式的配置id'),
				'qr_code'         => Yii::t('app', '联系二维码的URL'),
				'state'           => Yii::t('app', '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'welcome'         => Yii::t('app', '欢迎语'),
				'help_tip'        => Yii::t('app', '收到助力信息'),
				'complete_tip'    => Yii::t('app', '任务完成提醒'),
				'end_tip'         => Yii::t('app', '活动结束提醒'),
				'status'          => Yii::t('app', '状态0删除、1未发布、2已发布、3到期结束、4奖品无库存结束、5、手动提前结束'),
				'update_time'     => Yii::t('app', '修改时间'),
				'start_time'      => Yii::t('app', '开始时间'),
				'end_time'        => Yii::t('app', '结束时间'),
				'create_time'     => Yii::t('app', '创建时间'),
				'expire_time'     => Yii::t('app', '活码过期时间'),
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
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->welcome = rawurlencode(rawurldecode($this->welcome));

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->welcome)) {
				$this->welcome = rawurldecode($this->welcome);
			}

			parent::afterFind(); // TODO: Change the autogenerated stub
		}
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		public function dumpData ($isList = false, $isEdit = false)
		{
			if (!empty($this->is_brush)) {
				$brushRule = json_decode($this->brush_rule, 1);
			} else {
				$brushRule = [];
			}
			$prizeRule  = json_decode($this->prize_rule, 1);
			$picRule    = json_decode($this->pic_rule, 1);
			$welcome    = json_decode($this->welcome, 1);
			$userKeyArr = json_decode($this->user_key, 1);
			$result     = [
				'id'              => $this->id,
				'uid'             => $this->uid,
				'corp_id'         => $this->corp_id,
				'agent_id'        => $this->agent_id,
				'title'           => $this->title,
				'start_time'      => substr($this->start_time, 0, 16),
				'end_time'        => substr($this->end_time, 0, 16),
				'is_end'          => (boolean) $this->is_end,
				'is_friend'       => $this->is_friend,
				'is_invalid'      => (boolean) $this->is_invalid,
				'is_brush'        => (boolean) $this->is_brush,
				'brush_rule'      => $brushRule,
				'complete_num'    => $this->complete_num,
				'prize_type'      => $this->prize_type,
				'prize_rule'      => $prizeRule[0],
				'pic_rule'        => $picRule,
				'prize_send_type' => $this->prize_send_type,
				'help_limit'      => !empty($this->help_limit) ? $this->help_limit : '',
				'sex_type'        => $this->sex_type,
				'area_type'       => $this->area_type,
				'area_data'       => json_decode($this->area_data, 1),
				'is_option'       => (boolean) $this->is_option,
				'user'            => $userKeyArr,
				'tag_ids'         => !empty($this->tag_ids) ? explode(',', $this->tag_ids) : [],
				'welcome'         => $welcome,
				'qr_code'         => $this->qr_code,
				'status'          => $this->status,
				'create_time'     => $this->create_time,
				'update_time'     => $this->update_time,
			];

			if ($isList) {
				$result['prize_name'] = $prizeRule[0]['prize_name'];
				$result['prize_num']  = $prizeRule[0]['prize_num'];
				if (!empty($this->is_friend)) {
					$limit_str = '需邀请' . $prizeRule[0]['fission_num'] . '人助力';//（新老好友均可）
				} else {
					$limit_str = '需邀请' . $prizeRule[0]['fission_num'] . '人助力';//（必须为新好友）
				}
				$result['limit_str'] = $limit_str;
				$member_str          = [];
				if (!empty($userKeyArr)) {
					$userIds  = array_column($userKeyArr, 'id');
					$workUser = WorkUser::find()->where(['id' => $userIds])->select('name')->asArray()->all();
					if (!empty($workUser)) {
						$member_str = array_column($workUser, 'name');
					}
				}
				$result['member_str'] = $member_str;
				$reason_str = '';
				switch ($this->status) {
					case 1:
						$status_str = '未开始';
						break;
					case 2:
						$status_str = '进行中';
						break;
					case 3:
						$status_str = '已结束';
						$reason_str = '到期结束';
						break;
					case 4:
						$status_str = '已结束';
						$reason_str = '奖品无库存结束';
						break;
					case 5:
						$status_str = '已结束';
						$reason_str = '手动提前结束';
						break;
					default:
						$status_str = '已删除';
				}
				$result['status_str'] = $status_str;
				$result['reason_str'] = $reason_str;
			}

			if ($isEdit) {
				$result['brush_time']       = !empty($brushRule['brush_time']) ? $brushRule['brush_time'] : '';
				$result['brush_num']        = !empty($brushRule['brush_num']) ? $brushRule['brush_num'] : '';
				$result['fission_num']      = $prizeRule[0]['fission_num'];
				$result['prize_name']       = empty($this->prize_type) ? $prizeRule[0]['prize_name'] : '';
				$result['prize_num']        = $prizeRule[0]['prize_num'];
				$result['amount']           = !empty($prizeRule[0]['amount']) ? $prizeRule[0]['amount'] : '0';
				$result['back_pic_url']     = $picRule['back_pic_url'];
				$result['is_avatar']        = (boolean) $picRule['is_avatar'];
				$result['avatar']           = $picRule['avatar'];
				$result['shape']            = $picRule['shape'];
				$result['is_nickname']      = (boolean) $picRule['is_nickname'];
				$result['nickName']         = $picRule['nickName'];
				$result['qrCode']           = $picRule['qrCode'];
				$result['color']            = $picRule['color'];
				$result['font_size']        = $picRule['font_size'];
				$result['align']            = $picRule['align'];
				$result['text_content']     = $welcome['text_content'];
				$result['link_start_title'] = $welcome['link_start_title'];
				$result['link_end_title']   = $welcome['link_end_title'];
				$result['link_desc']        = $welcome['link_desc'];
				$result['link_pic_url']     = $welcome['link_pic_url'];
			}
			if (!empty($userKeyArr)) {
				foreach ($userKeyArr as $key => &$user) {
					if (!isset($user["title"])) {
						$workUser = WorkUser::findOne($user['id']);
						if (!empty($workUser)) {
							$user['title'] = $workUser->name;
						}
						$user["scopedSlots"] = ["title" => "custom"];
						$user["key"]         = $user["user_key"];
					}
				}
			}
			$result['user'] = $userKeyArr;
			return $result;
		}

		//检查数据
		public static function setData ($postData)
		{
			$id         = !empty($postData['id']) ? $postData['id'] : 0;
			$uid        = !empty($postData['uid']) ? $postData['uid'] : 0;
			$corp_id    = !empty($postData['corp_id']) ? $postData['corp_id'] : 0;
			$agent_id   = !empty($postData['agent_id']) ? $postData['agent_id'] : 0;
			$title      = !empty($postData['title']) ? trim($postData['title']) : '';
			$start_time = !empty($postData['start_time']) ? $postData['start_time'] : '';
			$end_time   = !empty($postData['end_time']) ? $postData['end_time'] : '';
			$is_end     = !empty($postData['is_end']) ? intval($postData['is_end']) : 1;
			//$is_friend   = !empty($postData['is_friend']) ? intval($postData['is_friend']) : 0;
			//$is_invalid  = !empty($postData['is_invalid']) ? intval($postData['is_invalid']) : 0;
			//默认值，不从前端获取
			$is_friend       = 1;
			$is_invalid      = 0;
			$is_brush        = !empty($postData['is_brush']) ? intval($postData['is_brush']) : 0;
			$brush_time      = !empty($postData['brush_time']) ? intval($postData['brush_time']) : 0;
			$brush_num       = !empty($postData['brush_num']) ? intval($postData['brush_num']) : 0;
			$prize_type      = !empty($postData['prize_type']) ? intval($postData['prize_type']) : 0;
			$fission_num     = !empty($postData['fission_num']) ? intval($postData['fission_num']) : 0;
			$prize_name      = !empty($postData['prize_name']) ? trim($postData['prize_name']) : '';
			$prize_num       = !empty($postData['prize_num']) ? intval($postData['prize_num']) : 99999999;
			$amount          = !empty($postData['amount']) ? trim($postData['amount']) : '';//红包金额
			$prize_send_type = !empty($postData['prize_send_type']) ? intval($postData['prize_send_type']) : 1;
			$help_limit      = !empty($postData['help_limit']) ? intval($postData['help_limit']) : 0;
			$sex_type        = !empty($postData['sex_type']) ? intval($postData['sex_type']) : 1;
			$area_type       = !empty($postData['area_type']) ? intval($postData['area_type']) : 1;
			$area_data       = !empty($postData['area_data']) && ($area_type == 2) ? $postData['area_data'] : [];
			//基础设置
			$back_pic_url = !empty($postData['back_pic_url']) ? trim($postData['back_pic_url']) : '';
			$is_avatar    = !empty($postData['is_avatar']) ? intval($postData['is_avatar']) : '';
			$avatar       = !empty($postData['avatar']) ? $postData['avatar'] : [];
			$shape        = !empty($postData['shape']) ? $postData['shape'] : '';
			$is_nickname  = !empty($postData['is_nickname']) ? intval($postData['is_nickname']) : '';
			$nickName     = !empty($postData['nickName']) ? $postData['nickName'] : [];
			$qrCode       = !empty($postData['qrCode']) ? $postData['qrCode'] : [];
			$color        = !empty($postData['color']) ? $postData['color'] : '';
			$font_size    = !empty($postData['font_size']) ? $postData['font_size'] : '';
			$align        = !empty($postData['align']) ? $postData['align'] : '';
			$success_tags = !empty($postData['success_tags']) ? $postData['success_tags'] : [];

			//成员
			$is_option = !empty($postData['is_option']) ? intval($postData['is_option']) : 0;
			$user_key  = !empty($postData['user']) ? $postData['user'] : '';
			$userId    = [];
			if (!empty($user_key)) {
				$Temp     = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($user_key);
				$userIds = WorkDepartment::GiveDepartmentReturnUserData($corp_id, $Temp["department"], $Temp["user"], 1, true,0);
				if(!empty($userIds)){
					foreach ($userIds as $usId) {
						$workUser = WorkUser::findOne($usId);
						if (!empty($workUser) && $workUser->corp_id == $corp_id) {
							array_push($userId, $workUser->userid);
						}
					}
				}
			}

			$tag_ids = !empty($postData['tag_ids']) ? $postData['tag_ids'] : '';
			//欢迎语
			$text_content     = !empty($postData['text_content']) ? trim($postData['text_content']) : '';
			$link_start_title = !empty($postData['link_start_title']) ? trim($postData['link_start_title']) : '';
			$link_end_title   = !empty($postData['link_end_title']) ? trim($postData['link_end_title']) : '';
			$link_desc        = !empty($postData['link_desc']) ? trim($postData['link_desc']) : '';
			$link_pic_url     = !empty($postData['link_pic_url']) ? $postData['link_pic_url'] : '';

			$help_tip     = !empty($postData['help_tip']) ? $postData['help_tip'] : '';
			$complete_tip = !empty($postData['complete_tip']) ? $postData['complete_tip'] : '';
			$end_tip      = !empty($postData['end_tip']) ? $postData['end_tip'] : '';
			if (empty($uid) || empty($corp_id) || empty($agent_id)) {
				throw new InvalidDataException('参数不正确');
			}
			if (empty($title)) {
				throw new InvalidDataException('请填写活动标题');
			}
			if (empty($start_time) && empty($end_time)) {
				throw new InvalidDataException('请选择活动时间');
			}
			if ($start_time >= $end_time) {
				throw new InvalidDataException('开始时间不能大于结束时间');
			}
			if (!empty($is_brush)) {
				if (empty($brush_time)) {
					throw new InvalidDataException('请设置防刷秒数');
				}
				if (empty($brush_num)) {
					throw new InvalidDataException('请设置防刷客户人数');
				}
			}
			if (empty($back_pic_url)) {
				throw new InvalidDataException('请选择海报图片');
			}
			if (!empty($is_nickname)) {
				if (empty($shape)) {
					throw new InvalidDataException('请选择头像类型');
				}
			}
			if (!empty($is_avatar)) {
				if (empty($color)) {
					throw new InvalidDataException('请选择昵称颜色');
				}
				if (empty($font_size)) {
					throw new InvalidDataException('请选择昵称大小');
				}
//				if (empty($align)) {
//					throw new InvalidDataException('请选择昵称对齐方式');
//				}
			}
			if (empty($fission_num)) {
				throw new InvalidDataException('请设置裂变人数');
			}
			if ($prize_type == 1) {
				if (empty($amount)) {
					throw new InvalidDataException('请设置红包金额');
				}
				if ($amount < 0.3 || $amount > 5000) {
					throw new InvalidDataException('红包金额只能在0.3-5000元之间');
				}
				$prize_rule = [
					[
						'fission_num' => $fission_num,
						'amount'      => $amount,
						'prize_name'  => $amount . '元红包',
						'prize_num'   => $prize_num
					]
				];
			} else {
				if (empty($prize_name)) {
					throw new InvalidDataException('请填写奖品名称');
				}
				$prize_rule = [
					[
						'fission_num' => $fission_num,
						'prize_name'  => $prize_name,
						'prize_num'   => $prize_num
					]
				];
			}

			if (empty($userId)) {
				throw new InvalidDataException('请选择引流成员');
			} elseif (count($userId) > 100) {
				throw new InvalidDataException('引流成员最多只能选择100个');
			}

			if (empty($link_start_title)) {
				throw new InvalidDataException('请填写开始活动标题');
			}
//			if (empty($link_end_title)) {
//				throw new InvalidDataException('请填写结束活动标题');
//			}
			if (empty($link_pic_url)) {
				throw new InvalidDataException('请选择封面图片');
			}

			if (!empty($id)) {
				$fsData = static::findOne($id);
				if (in_array($fsData->status, [3, 4, 5])) {
					throw new InvalidDataException('活动已结束，不准许再修改');
				}
				$error_msg = '修改失败';
				$prizeRule = json_decode($fsData->prize_rule, 1);
				if ($fsData->status == 2) {
					$fission_num = $prizeRule[0]['fission_num'];
					$prizeNum    = $prizeRule[0]['prize_num'];
					if ($prize_num < $prizeNum) {
						throw new InvalidDataException('奖品库存只能增加不能减少');
					}
					$is_friend  = $fsData->is_friend;
					$is_invalid = $fsData->is_invalid;
					$title      = $fsData->title;
				} else {
					//活动标题是否重复
					$titleInfo = static::find()->where(['uid' => $uid, 'title' => $title, 'status' => [1, 2, 3, 4, 5]])->andWhere(['!=', 'id', $id])->one();
					if (!empty($titleInfo)) {
						throw new InvalidDataException('活动标题已经存在，请更改');
					}
				}
			} else {
				//活动标题是否重复
				$titleInfo = static::findOne(['uid' => $uid, 'title' => $title, 'status' => [1, 2, 3, 4, 5]]);
				if (!empty($titleInfo)) {
					throw new InvalidDataException('活动标题已经存在，请更改');
				}
				$error_msg           = '创建失败';
				$fsData              = new Fission();
				$fsData->uid         = $uid;
				$fsData->corp_id     = $corp_id;
				$fsData->create_time = DateUtil::getCurrentTime();
			}
			$fsData->title        = $title;
			$fsData->agent_id     = $agent_id;
			$fsData->is_end       = $is_end;
			$fsData->start_time   = $start_time;
			$fsData->end_time     = $end_time;
			$fsData->is_friend    = $is_friend;
			$fsData->is_invalid   = $is_invalid;

			//奖品
			$fsData->prize_type      = $prize_type;
			$fsData->prize_rule      = json_encode($prize_rule, JSON_UNESCAPED_UNICODE);
			$fsData->prize_send_type = $prize_send_type;
			$fsData->help_limit      = $help_limit;
			$fsData->sex_type        = $sex_type;
			$fsData->area_type       = $area_type;
			$fsData->area_data       = json_encode($area_data, JSON_UNESCAPED_UNICODE);
			//防刷
			$fsData->is_brush = $is_brush;
			if (!empty($is_brush)) {
				$brush_rule         = ['brush_time' => $brush_time, 'brush_num' => $brush_num];
				$fsData->brush_rule = json_encode($brush_rule, JSON_UNESCAPED_UNICODE);
			}

			//基础设置
			$pic_rule         = [
				'back_pic_url' => $back_pic_url,
				'is_avatar'    => $is_avatar,
				'avatar'       => $avatar,
				'shape'        => $shape,
				'is_nickname'  => $is_nickname,
				'nickName'     => $nickName,
				'qrCode'       => $qrCode,
				'color'        => $color,
				'font_size'    => $font_size,
				'align'        => $align
			];
			$fsData->pic_rule = json_encode($pic_rule, JSON_UNESCAPED_UNICODE);;

			//成员
			$fsData->is_option = $is_option;
			$fsData->user_key  = json_encode($user_key);
			$userIdJson        = json_encode($userId);
			$is_change         = 0;
			if (!empty($id)) {
				$is_change = ($fsData->user == $userIdJson) ? 0 : 1;
			}
			$fsData->user    = $userIdJson;
			$fsData->tag_ids = $tag_ids;

			//欢迎语
			$welcome         = [
				'text_content'     => $text_content,
				'link_start_title' => $link_start_title,
				'link_end_title'   => $link_end_title,
				'link_desc'        => $link_desc,
				'link_pic_url'     => $link_pic_url
			];
			$fsData->welcome = json_encode($welcome, JSON_UNESCAPED_UNICODE);
			$transaction     = \Yii::$app->mdb->beginTransaction();
			try {
				if (!$fsData->validate() || !$fsData->save()) {
					throw new InvalidDataException($error_msg . SUtils::modelError($fsData));
				}
				if (empty($id) || empty($fsData->config_id)) {
					$configArr         = static::addConfigId($fsData);
					$fsData->config_id = $configArr['config_id'];
					$fsData->qr_code   = $configArr['qr_code'];
					$fsData->state     = self::FISSION_HEAD . '_' . $fsData->id . '_0';
					if (!$fsData->validate() || !$fsData->save()) {
						throw new InvalidDataException($error_msg . SUtils::modelError($fsData));
					}
				} elseif (!empty($is_change)) {
					static::updateConfigId($fsData);
				}
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return $fsData->id;
		}

		//生成config_id
		public static function addConfigId ($fission, $state = '')
		{
			if (empty($state)) {
				$state = self::FISSION_HEAD . '_' . $fission->id . '_0';
			}
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => $state,
				'user'        => json_decode($fission->user, 1),
				'party'       => [],
			];
			try {
				$workApi = WorkUtils::getWorkApi($fission->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECAddContactWay($sendData);
					\Yii::error($wayResult, 'wayResult');
					if ($wayResult['errcode'] != 0) {
						throw new InvalidDataException($wayResult['errmsg']);
					}
					$wayInfo        = $workApi->ECGetContactWay($wayResult['config_id']);
					$wayInfo        = SUtils::Object2Array($wayInfo);
					$contactWayInfo = $wayInfo['contact_way'];

					return ['config_id' => $contactWayInfo['config_id'], 'qr_code' => $contactWayInfo['qr_code']];
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				} elseif (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				} elseif (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				} elseif (strpos($message, '-1') !== false) {
					$message = '系统繁忙，建议重试';
				}
				\Yii::error(json_decode($fission->user, 1), 'err-user');
				\Yii::error($message, 'err-user');

				throw new InvalidDataException($message);
			}

			return [];
		}

		//根据config_id进行修改
		public static function updateConfigId ($fission)
		{
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => self::FISSION_HEAD . '_' . $fission->id . '_0',
				'user'        => json_decode($fission->user, 1),
				'party'       => [],
				'config_id'   => $fission->config_id,
			];
			try {
				$workApi = WorkUtils::getWorkApi($fission->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
					$workApi->ECUpdateContactWay($sendData);
					//查询助力表中的config_id
					$joinList = FissionJoin::find()->select('config_status,config_id,state')->where(['fid' => $fission->id])->all();
					if (!empty($joinList)) {
						foreach ($joinList as $join) {
							if (!empty($join->config_id) && ($join->config_status == 1)) {
								$contactWayInfo['config_id'] = $join->config_id;
								$contactWayInfo['state']     = $join->state;
								$sendData                    = ExternalContactWay::parseFromArray($contactWayInfo);
								try {
									$workApi->ECUpdateContactWay($sendData);
								} catch (\Exception $e) {

								}
							}
						}
					}
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				if (strpos($message, '84074') !== false) {
					$message = '没有外部联系人权限';
				} elseif (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				}
				throw new InvalidDataException($message);
			}

			return [];
		}

		//任务结束时删除config_id
		public static function delConfigId ($fission)
		{
			try {
				$workApi = WorkUtils::getWorkApi($fission->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					//任务
					if (!empty($fission->config_id)) {
						try {
							$workApi->ECDelContactWay($fission->config_id);
						} catch (\Exception $e) {

						}
					}
					//查询助力表中的config_id
					$joinList = FissionJoin::find()->select('id,config_status,config_id')->where(['uid' => $fission->uid, 'fid' => $fission->id])->andWhere(['!=', 'config_id', ''])->all();
					if (!empty($joinList)) {
						foreach ($joinList as $join) {
							if ($join->config_status == 1) {
								try {
									$workApi->ECDelContactWay($join->config_id);
								} catch (\Exception $e) {

								}
								$join->config_status = 0;
								$join->update();
							}
						}
					}
				}
			} catch (\Exception $e) {

			}
		}

		//活动结束时处理数据
		public static function handleData ($fission, $status = 0)
		{
			/** @var Fission $fission * */
			if (empty($fission)) {
				return '';
			}

			if (!empty($status)) {
				//删除config
				static::delConfigId($fission);
				//更改状态
				$fission->status = $status;
				$fission->update();
			}
			//自动发送红包
			if ($fission->prize_type == 1) {
				$joinList = FissionJoin::find()->where(['fid' => $fission->id, 'status' => 2, 'prize_status' => [0, 2], 'is_black' => 0])->all();
				if (!empty($joinList)) {
					$is_send = 0;
					foreach ($joinList as $join) {
						/** @var FissionJoin $join * */
						try {
							$remark      = '裂变成功，' . $join->amount . '元红包拿走，不谢~~~';
							$contactInfo = WorkExternalContact::findOne($join->external_id);
							$helpData    = [
								'uid'         => $fission->uid,
								'corp_id'     => $fission->corp_id,
								'rid'         => $fission->id,
								'jid'         => $join->id,
								'external_id' => $join->external_id,
								'openid'      => $contactInfo->openid,
								'amount'      => $join->amount,
								'remark'      => $remark,
							];

							$res = RedPackOrder::sendRedPack($helpData, 2);
							if (!empty($res)) {
								$join->prize_status = 1;
								$join->update();
								$is_send = 1;
							}
						} catch (InvalidDataException $e) {
							$is_send = 0;
							\Yii::error($e->getMessage(), 'handSendJoin');
							break;
						}
					}

					//补发剩余的
					if (!empty($is_send)) {
						\Yii::$app->queue->delay(10)->push(new SyncFissionJob([
							'fission_id' => $fission->id,
							'sendData'   => ['is_all' => 1, 'uid' => $fission->uid]
						]));
					}
				}
			}
		}

		//如果微信支付有钱，补发之前没发的红包
		public static function supplySend ($uid)
		{
			$cacheSendKey = 'supplySend_fission_' . $uid;
			$cacheSend    = \Yii::$app->cache->get($cacheSendKey);
			if (!empty($cacheSend)) {
				return '';
			}
			\Yii::$app->cache->set($cacheSendKey, 1, 600);

			$fissionJoin = FissionJoin::find()->alias('fj');
			$fissionJoin = $fissionJoin->leftJoin('{{%fission}} f', '`fj`.`fid` = `f`.`id`');
			$fissionJoin = $fissionJoin->where(['fj.uid' => $uid, 'fj.status' => 2, 'fj.prize_status' => 0, 'f.prize_type' => 1, 'f.status' => [3, 4, 5], 'fj.is_black' => 0]);
			$fissionJoin = $fissionJoin->select('fj.id,fj.uid,fj.fid,fj.amount,fj.external_id');
			$fissionJoin = $fissionJoin->all();
			if (!empty($fissionJoin)) {
				foreach ($fissionJoin as $join) {
					/** @var FissionJoin $join * */
					try {
						$corp_id     = $join->f->corp_id;
						$amount      = $join->amount;
						$remark      = '裂变成功，' . $amount . '元红包拿走，不谢~~~';
						$contactInfo = WorkExternalContact::findOne($join->external_id);
						$helpData    = [
							'uid'         => $join->uid,
							'corp_id'     => $corp_id,
							'rid'         => $join->fid,
							'jid'         => $join->id,
							'external_id' => $join->external_id,
							'openid'      => $contactInfo->openid,
							'amount'      => $amount,
							'remark'      => $remark,
						];
						$res         = RedPackOrder::sendRedPack($helpData, 2);
						if (!empty($res)) {
							$join->prize_status = 1;
							$join->update();
						}
					} catch (InvalidDataException $e) {
						\Yii::error($e->getMessage(), 'supplySend_fission');
						break;
					}
				}
			}

			\Yii::$app->cache->delete($cacheSendKey);
		}
	}
