<?php

	namespace app\models;

	use app\components\InvalidDataException;
    use app\queue\SyncAwardJob;
    use app\queue\SyncRedPackJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%red_pack}}".
	 *
	 * @property int                 $id
	 * @property int                 $uid                      账户ID
	 * @property int                 $corp_id                  企业ID
	 * @property int                 $agent_id                 应用ID
	 * @property string              $config_id                联系方式的配置id
	 * @property string              $qr_code                  联系二维码的URL
	 * @property string              $state                    企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值
	 * @property string              $title                    活动标题
	 * @property string              $start_time               开始日期
	 * @property string              $end_time                 结束日期
	 * @property string              $activity_rule            活动规则
	 * @property string              $contact_phone            联系电话
	 * @property string              $redpack_price            裂变红包金额
	 * @property int                 $redpack_num              裂变红包个数
	 * @property int                 $complete_num             裂变完成数量
	 * @property int                 $help_limit               好友助力次数限制
	 * @property int                 $first_detach_type        用户首次拆领类型：1、随机金额，2、固定金额，3、百分比金额
	 * @property string              $min_random_amount        最小随机金额
	 * @property string              $max_random_amount        最大随机金额
	 * @property string              $fixed_amount             固定金额
	 * @property int                 $min_random_amount_per    最小随机金额百分比
	 * @property int                 $max_random_amount_per    最大随机金额百分比
	 * @property int                 $invite_amount            裂变人数数量
	 * @property int                 $friend_detach_type       好友拆领类型：1、随机金额，2、固定金额
	 * @property string              $min_friend_random_amount 最小随机金额
	 * @property string              $max_friend_random_amount 最大随机金额
	 * @property string              $fixed_friend_amount      固定金额
	 * @property string              $total_amount             活动总金额
	 * @property string              $give_out                 已发放金额
	 * @property int                 $send_type                发放红包类型：1、活动时间内自动发送，2、活动结束后自动发放
	 * @property int                 $sex_type                 性别类型：1、不限制，2、男性，3、女性，4、未知
	 * @property int                 $area_type                区域类型：1、不限制，2、部分地区
	 * @property string              $area_data                区域数据
	 * @property string              $tag_ids                  给客户打的标签
	 * @property string              $pic_rule                 图片规则
	 * @property string              $user_key                 引流成员
	 * @property string              $user                     用户userID列表
	 * @property string              $welcome                  欢迎语
	 * @property int                 $status                   状态0删除、1未发布、2已发布、3到期结束、4裂变红包个数已用完、5、手动提前结束
	 * @property string              $create_time              创建时间
	 * @property string              $update_time              修改时间
	 * @property string              $success_tags             完成后打上指定标签
	 *
	 * @property WorkCorp            $corp
	 * @property User                $u
	 * @property RedPackHelpDetail[] $redPackHelpDetails
	 * @property RedPackJoin[]       $redPackJoins
	 */
	class RedPack extends \yii\db\ActiveRecord
	{
		const RED_HEAD = 'red';
		const H5_URL = '/h5/pages/redFission/index';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%red_pack}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'agent_id', 'redpack_num', 'complete_num', 'help_limit', 'first_detach_type', 'min_random_amount_per', 'max_random_amount_per', 'invite_amount', 'friend_detach_type', 'send_type', 'sex_type', 'area_type', 'status'], 'integer'],
				[['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
				[['activity_rule', 'area_data', 'pic_rule', 'user_key', 'user', 'welcome'], 'string'],
				[['redpack_price', 'min_random_amount', 'max_random_amount', 'fixed_amount', 'min_friend_random_amount', 'max_friend_random_amount', 'fixed_friend_amount', 'give_out'], 'number'],
				[['config_id', 'state'], 'string', 'max' => 64],
				[['qr_code', 'success_tags'], 'string', 'max' => 255],
				[['title', 'contact_phone'], 'string', 'max' => 25],
				[['total_amount'], 'string', 'max' => 32],
				[['tag_ids'], 'string', 'max' => 250],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                       => Yii::t('app', 'ID'),
				'uid'                      => Yii::t('app', '账户ID'),
				'corp_id'                  => Yii::t('app', '企业ID'),
				'agent_id'                 => Yii::t('app', '应用ID'),
				'config_id'                => Yii::t('app', '联系方式的配置id'),
				'qr_code'                  => Yii::t('app', '联系二维码的URL'),
				'state'                    => Yii::t('app', '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'title'                    => Yii::t('app', '活动标题'),
				'start_time'               => Yii::t('app', '开始日期'),
				'end_time'                 => Yii::t('app', '结束日期'),
				'activity_rule'            => Yii::t('app', '活动规则'),
				'contact_phone'            => Yii::t('app', '联系电话'),
				'redpack_price'            => Yii::t('app', '裂变红包金额'),
				'redpack_num'              => Yii::t('app', '裂变红包个数'),
				'complete_num'             => Yii::t('app', '裂变完成数量'),
				'help_limit'               => Yii::t('app', '好友助力次数限制'),
				'first_detach_type'        => Yii::t('app', '用户首次拆领类型：1、随机金额，2、固定金额，3、百分比金额'),
				'min_random_amount'        => Yii::t('app', '最小随机金额'),
				'max_random_amount'        => Yii::t('app', '最大随机金额'),
				'fixed_amount'             => Yii::t('app', '固定金额'),
				'min_random_amount_per'    => Yii::t('app', '最小随机金额百分比'),
				'max_random_amount_per'    => Yii::t('app', '最大随机金额百分比'),
				'invite_amount'            => Yii::t('app', '裂变人数数量'),
				'friend_detach_type'       => Yii::t('app', '好友拆领类型：1、随机金额，2、固定金额'),
				'min_friend_random_amount' => Yii::t('app', '最小随机金额'),
				'max_friend_random_amount' => Yii::t('app', '最大随机金额'),
				'fixed_friend_amount'      => Yii::t('app', '固定金额'),
				'total_amount'             => Yii::t('app', '活动总金额'),
				'give_out'                 => Yii::t('app', '已发放金额'),
				'send_type'                => Yii::t('app', '发放红包类型：1、活动时间内自动发送，2、活动结束后自动发放'),
				'sex_type'                 => Yii::t('app', '性别类型：1、不限制，2、男性，3、女性，4、未知'),
				'area_type'                => Yii::t('app', '区域类型：1、不限制，2、部分地区'),
				'area_data'                => Yii::t('app', '区域数据'),
				'tag_ids'                  => Yii::t('app', '给客户打的标签'),
				'pic_rule'                 => Yii::t('app', '图片规则'),
				'user_key'                 => Yii::t('app', '引流成员'),
				'user'                     => Yii::t('app', '用户userID列表'),
				'welcome'                  => Yii::t('app', '欢迎语'),
				'status'                   => Yii::t('app', '状态0删除、1未发布、2已发布、3到期结束、4裂变红包个数已用完、5、手动提前结束'),
				'create_time'              => Yii::t('app', '创建时间'),
				'update_time'              => Yii::t('app', '修改时间'),
				'success_tags'             => Yii::t('app', '完成后打上指定标签'),
			];
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
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

			$userKeyArr = json_decode($this->user_key, 1);
			$result     = [
				'id'                       => $this->id,
				'uid'                      => $this->uid,
				'corp_id'                  => $this->corp_id,
				'agent_id'                 => $this->agent_id,
				'title'                    => $this->title,
				'start_time'               => substr($this->start_time, 0, 16),
				'end_time'                 => substr($this->end_time, 0, 16),
				'activity_rule'            => $this->activity_rule,
				'contact_phone'            => $this->contact_phone,
				'redpack_price'            => $this->redpack_price,
				'redpack_num'              => $this->redpack_num,
				'help_limit'               => !empty($this->help_limit) ? $this->help_limit : '',
				'first_detach_type'        => $this->first_detach_type,
				'min_random_amount'        => $this->min_random_amount,
				'max_random_amount'        => $this->max_random_amount,
				'fixed_amount'             => $this->fixed_amount,
				'min_random_amount_per'    => $this->min_random_amount_per,
				'max_random_amount_per'    => $this->max_random_amount_per,
				'invite_amount'            => $this->invite_amount,
				'friend_detach_type'       => $this->friend_detach_type,
				'min_friend_random_amount' => $this->min_friend_random_amount,
				'max_friend_random_amount' => $this->max_friend_random_amount,
				'fixed_friend_amount'      => $this->fixed_friend_amount,
				'total_amount'             => $this->total_amount,
				'send_type'                => $this->send_type,
				'sex_type'                 => $this->sex_type,
				'area_type'                => $this->area_type,
				'area_data'                => json_decode($this->area_data, 1),
				'tag_ids'                  => !empty($this->tag_ids) ? explode(',', $this->tag_ids) : [],
				'qr_code'                  => $this->qr_code,
				'status'                   => $this->status,
				'create_time'              => $this->create_time,
				'update_time'              => $this->update_time,
				'success_tags'             => empty($this->success_tags) ? [] : explode(",",$this->success_tags),
			];
			if ($isList) {
				$first_str = '首拆领';
				if ($this->first_detach_type == 1) {
					$first_str .= $this->min_random_amount . '元~' . $this->max_random_amount . '元';
				} elseif ($this->first_detach_type == 2) {
					$first_str .= $this->fixed_amount . '元';
				} elseif ($this->first_detach_type == 3) {
					$first_str .= $this->min_random_amount_per . '%~' . $this->max_random_amount_per . '%';
				}
				$result['first_str'] = $first_str;
				$limit_str           = '需邀请' . $this->invite_amount . '人拆领';
				if ($this->friend_detach_type == 1) {
					$limit_str .= '，随机领取' . $this->min_friend_random_amount . '元~' . $this->max_friend_random_amount . '元';
				} elseif ($this->friend_detach_type == 2) {
					$limit_str .= '，每人领取' . $this->fixed_friend_amount . '元';
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
				$result['member_str']   = $member_str;
				$result['date_str']     = substr($this->start_time, 0, 16) . '~' . substr($this->end_time, 0, 16);
				$result['red_pack_str'] = $this->redpack_price . '元（' . $first_str . '）';
				$give_out               = static::getGiveOut($this->id);
				$result['give_out']     = $give_out;
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
						$reason_str = '裂变红包个数已用完';
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
				$picRule                = json_decode($this->pic_rule, 1);
				$welcome                = json_decode($this->welcome, 1);
				$result['back_pic_url'] = $picRule['back_pic_url'];
				$result['is_avatar']    = (boolean) $picRule['is_avatar'];
				$result['avatar']       = $picRule['avatar'];
				$result['shape']        = $picRule['shape'];
				$result['is_nickname']  = (boolean) $picRule['is_nickname'];
				$result['nickName']     = $picRule['nickName'];
				$result['qrCode']       = $picRule['qrCode'];
				$result['color']        = $picRule['color'];
				$result['font_size']    = $picRule['font_size'];
				$result['align']        = $picRule['align'];
				$result['text_content'] = $welcome['text_content'];
				$result['link_title']   = $welcome['link_title'];
				$result['link_desc']    = $welcome['link_desc'];
				$result['link_pic_url'] = $welcome['link_pic_url'];

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
			//第一步
			$id            = !empty($postData['id']) ? $postData['id'] : 0;
			$uid           = !empty($postData['uid']) ? $postData['uid'] : 0;
			$corp_id       = !empty($postData['corp_id']) ? $postData['corp_id'] : 0;
			$agent_id      = !empty($postData['agent_id']) ? $postData['agent_id'] : 0;
			$title         = !empty($postData['title']) ? trim($postData['title']) : '';
			$start_time    = !empty($postData['start_time']) ? $postData['start_time'] : '';
			$end_time      = !empty($postData['end_time']) ? $postData['end_time'] : '';
			$activity_rule = !empty($postData['activity_rule']) ? trim($postData['activity_rule']) : '';
			$contact_phone = !empty($postData['contact_phone']) ? trim($postData['contact_phone']) : '';
			$success_tags  = !empty($postData['success_tags']) ? $postData['success_tags'] : '';

			if (empty($uid) || empty($corp_id) || empty($agent_id)) {
				throw new InvalidDataException('参数不正确');
			}
			if (empty($title)) {
				throw new InvalidDataException('请填写活动标题');
			} elseif (mb_strlen($title, 'utf-8') > 20) {
				throw new InvalidDataException('活动标题最多20个字符');
			}
			if (empty($start_time) || empty($end_time)) {
				throw new InvalidDataException('请选择活动时间');
			}
			if ($start_time >= $end_time) {
				throw new InvalidDataException('开始时间不能大于结束时间');
			}
			if (empty($activity_rule)) {
				throw new InvalidDataException('请填写活动规则');
			}
			if (empty($contact_phone)) {
				throw new InvalidDataException('请填写联系电话');
			} else {
				$pattern1 = '/^1\d{10}$/';
				$pattern2 = '/^0\d{2,3}-?\d{7,8}$/';
				if (!preg_match($pattern1, $contact_phone) && !preg_match($pattern2, $contact_phone)) {
					throw new InvalidDataException('请填写正确的联系电话');
				}
			}

			//第二步
			$redpack_price            = !empty($postData['redpack_price']) ? $postData['redpack_price'] : '';
			$redpack_num              = !empty($postData['redpack_num']) ? $postData['redpack_num'] : 0;
			$help_limit               = !empty($postData['help_limit']) ? $postData['help_limit'] : 0;
			$first_detach_type        = !empty($postData['first_detach_type']) ? $postData['first_detach_type'] : 0;
			$min_random_amount        = !empty($postData['min_random_amount']) ? $postData['min_random_amount'] : 0;
			$max_random_amount        = !empty($postData['max_random_amount']) ? $postData['max_random_amount'] : 0;
			$fixed_amount             = !empty($postData['fixed_amount']) ? $postData['fixed_amount'] : 0;
			$min_random_amount_per    = !empty($postData['min_random_amount_per']) ? $postData['min_random_amount_per'] : 0;
			$max_random_amount_per    = !empty($postData['max_random_amount_per']) ? $postData['max_random_amount_per'] : 0;
			$invite_amount            = !empty($postData['invite_amount']) ? $postData['invite_amount'] : 0;
			$friend_detach_type       = !empty($postData['friend_detach_type']) ? $postData['friend_detach_type'] : 0;
			$min_friend_random_amount = !empty($postData['min_friend_random_amount']) ? $postData['min_friend_random_amount'] : 0;
			$max_friend_random_amount = !empty($postData['max_friend_random_amount']) ? $postData['max_friend_random_amount'] : 0;
			$fixed_friend_amount      = !empty($postData['fixed_friend_amount']) ? $postData['fixed_friend_amount'] : 0;
			$total_amount             = !empty($postData['total_amount']) ? $postData['total_amount'] : 0;
			$send_type                = !empty($postData['send_type']) ? $postData['send_type'] : 0;
			if (empty($redpack_price)) {
				throw new InvalidDataException('请填写裂变红包金额');
			} elseif ($redpack_price < 1) {
				throw new InvalidDataException('裂变红包金额不能小于1元');
			} elseif ($redpack_price > 5000) {
				throw new InvalidDataException('裂变红包金额不能大于5000元');
			}
			if (empty($redpack_num)) {
				throw new InvalidDataException('请填写裂变红包个数');
			} elseif ($redpack_num > 999999999) {
				throw new InvalidDataException('裂变红包个数不能大于999999999个');
			}
			if (empty($first_detach_type)) {
				throw new InvalidDataException('请选择用户首次拆领类型');
			} else {
				if ($first_detach_type == 1) {
					if (empty($min_random_amount) || empty($max_random_amount)) {
						throw new InvalidDataException('请填写用户首次拆领随机金额');
					}
					if ($min_random_amount >= $max_random_amount) {
						throw new InvalidDataException('用户首次拆领随机金额最大值必须大于最小值');
					}
					if ($min_random_amount < 0.3) {
						throw new InvalidDataException('用户首次拆领红包金额不能小于0.3元');
					}
					if ($max_random_amount > $redpack_price - 0.3) {
						throw new InvalidDataException('首次拆领后剩余的发放金额不能小于0.3元');
					}
				} elseif ($first_detach_type == 2) {
					if (empty($fixed_amount)) {
						throw new InvalidDataException('请填写用户首次拆领固定金额');
					}
					if ($fixed_amount < 0.3) {
						throw new InvalidDataException('首次拆领固定金额不能小于0.3元');
					}
					if ($fixed_amount > $redpack_price - 0.3) {
						throw new InvalidDataException('首次拆领后剩余的发放金额不能小于0.3元');
					}
				} elseif ($first_detach_type == 3) {
					if (empty($min_random_amount_per) || empty($max_random_amount_per)) {
						throw new InvalidDataException('请填写用户首次拆领随机金额');
					}
					if ($min_random_amount_per >= $max_random_amount_per) {
						throw new InvalidDataException('首次拆领最小随机金额百分比不能大于等于最大随机金额百分比');
					}
					if ($min_random_amount_per * $redpack_price < 30) {
						throw new InvalidDataException('首次拆领最小随机金额百分比计算的金额不能小于0.3元');
					}
					if ($max_random_amount_per >= 100) {
						throw new InvalidDataException('首次拆领最大随机金额百分比要小于100%');
					}
					if ($redpack_price * (100 - $max_random_amount_per) < 30) {
						throw new InvalidDataException('首次拆领后剩余的发放金额不能小于0.3元');
					}
				}
			}
			if (empty($invite_amount)) {
				throw new InvalidDataException('请填写裂变人数');
			} elseif ($invite_amount > 999999999) {
				throw new InvalidDataException('裂变人数不能大于999999999人');
			}
			if (empty($friend_detach_type)) {
				throw new InvalidDataException('请选择好友拆领类型');
			} else {
				if ($friend_detach_type == 1) {
					if (empty($min_friend_random_amount) || empty($max_friend_random_amount)) {
						throw new InvalidDataException('请填写好友拆领随机金额');
					}
					if ($min_friend_random_amount >= $max_friend_random_amount) {
						throw new InvalidDataException('好友拆领最小随机金额不能大于等于最大随机金额');
					}
					if ($min_friend_random_amount < 0.3) {
						throw new InvalidDataException('好友拆领最小随机金额不能小于0.3元');
					}
					if ($max_friend_random_amount > 5000) {
						throw new InvalidDataException('好友拆领最大随机金额不能大于5000元');
					}
				} elseif ($friend_detach_type == 2) {
					if (empty($fixed_friend_amount)) {
						throw new InvalidDataException('请填写好友拆领固定金额');
					}
					if ($fixed_friend_amount < 0.3) {
						throw new InvalidDataException('好友拆领固定金额不能小于0.3元');
					}
					if ($fixed_friend_amount > 5000) {
						throw new InvalidDataException('好友拆领固定金额不能大于5000元');
					}
				}
			}
			if (empty($total_amount)) {
				throw new InvalidDataException('请设置活动总金额');
			}
			if (empty($send_type)) {
				throw new InvalidDataException('请选择红包发放类型');
			}

			//第三步
			$sex_type  = !empty($postData['sex_type']) ? $postData['sex_type'] : 0;
			$area_type = !empty($postData['area_type']) ? $postData['area_type'] : 0;
			$area_data = !empty($postData['area_data']) ? $postData['area_data'] : [];
			$tag_ids   = !empty($postData['tag_ids']) ? $postData['tag_ids'] : '';
			if (empty($sex_type)) {
				throw new InvalidDataException('请选择类别类型');
			}
			if (empty($area_type)) {
				throw new InvalidDataException('请选择地区类型');
			} elseif ($area_type == 2) {
				if (empty($area_data)) {
					throw new InvalidDataException('请选择参与地区');
				}
			}

			//第四步
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
			}

			//第五步
			$text_content = !empty($postData['text_content']) ? trim($postData['text_content']) : '';
			$link_title   = !empty($postData['link_title']) ? trim($postData['link_title']) : '';
			$link_desc    = !empty($postData['link_desc']) ? trim($postData['link_desc']) : '';
			$link_pic_url = !empty($postData['link_pic_url']) ? $postData['link_pic_url'] : '';
			if (empty($link_title)) {
				throw new InvalidDataException('请填写标题');
			}
			if (empty($link_pic_url)) {
				throw new InvalidDataException('请选择封面图片');
			}
			$user_key = !empty($postData['user']) ? $postData['user'] : '';
			$userId   = [];
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
			if (empty($userId)) {
				throw new InvalidDataException('请选择引流成员');
			} elseif (count($userId) > 100) {
				throw new InvalidDataException('引流成员最多只能选择100个');
			}

			if (!empty($id)) {
				$redData = static::findOne($id);
				if (in_array($redData->status, [0, 3, 4, 5])) {
					throw new InvalidDataException('活动已结束，不允许再修改');
				}
				$error_msg = '修改失败';
				if ($redData->status == 2) {
					if ($redpack_num < $redData->redpack_num) {
						throw new InvalidDataException('红包个数只能增加不能减少');
					}
				} else {
					//活动标题是否重复
					$titleInfo = static::find()->where(['uid' => $uid, 'title' => $title, 'status' => [1, 2, 3, 4, 5]])->andWhere(['!=', 'id', $id])->one();
					if (!empty($titleInfo)) {
						throw new InvalidDataException('活动标题已经存在，请更改');
					}
				}
				$redData->update_time = DateUtil::getCurrentTime();
			} else {
				//活动标题是否重复
				$titleInfo = static::findOne(['uid' => $uid, 'title' => $title, 'status' => [1, 2, 3, 4, 5]]);
				if (!empty($titleInfo)) {
					throw new InvalidDataException('活动标题已经存在，请更改');
				}
				$error_msg            = '创建失败';
				$redData              = new RedPack();
				$redData->uid         = $uid;
				$redData->corp_id     = $corp_id;
				$redData->create_time = DateUtil::getCurrentTime();
			}
			$redData->title                    = $title;
			$redData->agent_id                 = $agent_id;
			$redData->start_time               = $start_time;
			$redData->end_time                 = $end_time;
			$redData->activity_rule            = $activity_rule;
			$redData->contact_phone            = (string) $contact_phone;
			$redData->redpack_price            = $redpack_price;
			$redData->redpack_num              = $redpack_num;
			$redData->help_limit               = $help_limit;
			$redData->first_detach_type        = $first_detach_type;
			$redData->min_random_amount        = $min_random_amount;
			$redData->max_random_amount        = $max_random_amount;
			$redData->fixed_amount             = $fixed_amount;
			$redData->min_random_amount_per    = $min_random_amount_per;
			$redData->max_random_amount_per    = $max_random_amount_per;
			$redData->invite_amount            = $invite_amount;
			$redData->friend_detach_type       = $friend_detach_type;
			$redData->min_friend_random_amount = $min_friend_random_amount;
			$redData->max_friend_random_amount = $max_friend_random_amount;
			$redData->fixed_friend_amount      = $fixed_friend_amount;
			$redData->total_amount             = (string) $total_amount;
			$redData->sex_type                 = $sex_type;
			$redData->success_tags             = $success_tags;
			$redData->send_type                = $send_type;
			$redData->area_type                = $area_type;
			$redData->area_data                = json_encode($area_data, JSON_UNESCAPED_UNICODE);
			$redData->tag_ids                  = $tag_ids;
			//海报设置
			$pic_rule          = [
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
			$redData->pic_rule = json_encode($pic_rule, JSON_UNESCAPED_UNICODE);
			//成员
			$redData->user_key = json_encode($user_key, JSON_UNESCAPED_UNICODE);
			$userIdJson        = json_encode($userId, JSON_UNESCAPED_UNICODE);
			$is_change         = 0;
			if (!empty($id)) {
				$is_change = ($redData->user == $userIdJson) ? 0 : 1;
			}
			$redData->user = $userIdJson;
			//欢迎语
			$welcome          = [
				'text_content' => $text_content,
				'link_title'   => $link_title,
				'link_desc'    => $link_desc,
				'link_pic_url' => $link_pic_url
			];
			$redData->welcome = json_encode($welcome, JSON_UNESCAPED_UNICODE);

			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				if (!$redData->validate() || !$redData->save()) {
					throw new InvalidDataException($error_msg . SUtils::modelError($redData));
				}
				if (empty($id) || empty($redData->config_id)) {
					$configArr          = static::addConfigId($redData);
					$redData->config_id = $configArr['config_id'];
					$redData->qr_code   = $configArr['qr_code'];
					$redData->state     = self::RED_HEAD . '_' . $redData->id . '_0';
					if (!$redData->validate() || !$redData->save()) {
						throw new InvalidDataException($error_msg . SUtils::modelError($redData));
					}
				} elseif (!empty($is_change)) {
					static::updateConfigId($redData);
				}

				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return $redData->id;
		}

		//生成config_id
		public static function addConfigId ($redData, $state = '')
		{
			/** @var RedPack $redData * */
			if (empty($state)) {
				$state = self::RED_HEAD . '_' . $redData->id . '_0';
			}
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => $state,
				'user'        => json_decode($redData->user, 1),
				'party'       => [],
			];
			try {
				$workApi = WorkUtils::getWorkApi($redData->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData  = ExternalContactWay::parseFromArray($contactWayInfo);
					$wayResult = $workApi->ECAddContactWay($sendData);
					\Yii::error($wayResult, 'redwayResult');
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
				} elseif (strpos($message, '40001') !== false) {
					$message = '不合法的secret参数,请检查';
				} elseif (strpos($message, '40096') !== false) {
					$message = '不合法的外部联系人userid';
				} elseif (strpos($message, '40098') !== false) {
					$message = '接替成员尚未实名认证';
				} elseif (strpos($message, '40100') !== false) {
					$message = '用户的外部联系人已经在转移流程中';
				} elseif (strpos($message, '41054') !== false) {
					$message = '引流成员必须是已激活的成员（已登录过APP的才算作完全激活）';
				} elseif (strpos($message, '-1') !== false) {
					$message = '系统繁忙，建议重试';
				}
				throw new InvalidDataException($message);
			}

			return [];
		}

		//根据config_id进行修改
		public static function updateConfigId ($redData)
		{
			/** @var RedPack $redData * */
			$contactWayInfo = [
				'type'        => 2,
				'scene'       => 2,
				'style'       => 1,
				'remark'      => '',
				'skip_verify' => true,
				'state'       => self::RED_HEAD . '_' . $redData->id . '_0',
				'user'        => json_decode($redData->user, 1),
				'party'       => [],
				'config_id'   => $redData->config_id,
			];

			try {
				$workApi = WorkUtils::getWorkApi($redData->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					$sendData = ExternalContactWay::parseFromArray($contactWayInfo);
					$workApi->ECUpdateContactWay($sendData);
					//查询助力表中的config_id
					$joinList = RedPackJoin::find()->select('config_id,state')->where(['rid' => $redData->id])->all();
					if (!empty($joinList)) {
						foreach ($joinList as $join) {
							/** @var RedPackJoin $join * */
							if (!empty($join->config_id)) {
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
		public static function delConfigId ($redData)
		{
			/** @var RedPack $redData * */
			try {
				$workApi = WorkUtils::getWorkApi($redData->corp_id, WorkUtils::EXTERNAL_API);
				if (!empty($workApi)) {
					//任务
					if (!empty($redData->config_id)) {
						try {
							$workApi->ECDelContactWay($redData->config_id);
						} catch (\Exception $e) {

						}
					}
					//查询助力表中的config_id
					$joinList = RedPackJoin::find()->select('config_id')->where(['uid' => $redData->uid, 'rid' => $redData->id])->andWhere(['!=', 'config_id', ''])->all();
					if (!empty($joinList)) {
						foreach ($joinList as $join) {
							/** @var RedPackJoin $join * */
							try {
								$workApi->ECDelContactWay($join->config_id);
							} catch (\Exception $e) {

							}
						}
					}
				}
			} catch (\Exception $e) {

			}
		}

		//活动结束时处理数据
		public static function handleData ($redPack, $status = 0)
		{
			/** @var RedPack $redPack * */
			if (empty($redPack)) {
				return '';
			}

			//删除config
			static::delConfigId($redPack);
			//更改助力者状态
			RedPackHelpDetail::updateStatus($redPack);

			//参与者发放
			$joinList = RedPackJoin::find()->where(['rid' => $redPack->id])->andWhere(['or', ['first_send_status' => 0], ['status' => 2, 'send_status' => 0]])->all();
			if (!empty($joinList)) {
				$is_send = 0;
				foreach ($joinList as $join) {
					/** @var RedPackJoin $join * */
					try {
						$remark    = '';
						$send_type = $amount = 0;
						if ($join->first_send_status == 0 && ($join->status == 2 && $join->send_status == 0)) {
							$remark                  = $join->invite_amount . '位好友已全部拆完，' . $join->redpack_price . '元红包拿走，不谢~~~';
							$amount                  = $join->redpack_price;
							$send_type               = 4;
							$join->first_send_status = 1;
							$join->first_send_type   = 1;
							$join->send_status       = 1;
							$join->send_type         = 1;
						} elseif ($join->first_send_status == 0) {
							$remark                  = '还有' . $join->rest_amount . '元正在路上，快召唤' . $join->invite_amount . '位好友一起拆红包，TA有，你也有~~';
							$amount                  = $join->first_amount;
							$send_type               = 1;
							$join->first_send_status = 1;
							$join->first_send_type   = 1;
						} elseif ($join->status == 2 && $join->send_status == 0) {
							$remark            = $join->invite_amount . '位好友已全部拆完，剩下的' . $join->rest_amount . '元红包拿走，不谢~~~';
							$amount            = $join->rest_amount;
							$send_type         = 2;
							$join->send_status = 1;
							$join->send_type   = 1;
						}
						if (!empty($send_type)) {
							$joinData = [
								'uid'         => $redPack->uid,
								'corp_id'     => $redPack->corp_id,
								'rid'         => $redPack->id,
								'jid'         => $join->id,
								'external_id' => $join->external_id,
								'openid'      => $join->openid,
								'amount'      => $amount,
								'remark'      => $remark,
								'send_type'   => $send_type,
							];
							$res      = RedPackOrder::sendRedPack($joinData);
							if (!empty($res)) {
								$join->update();
								$is_send = 1;
							}
						}
					} catch (InvalidDataException $e) {
						$is_send = 0;
						break;
						\Yii::error($e->getMessage(), 'handSendJoin');
					}

					//助力者发放
					$contact = WorkExternalContact::findOne($join->external_id);
					if (!empty($contact)) {
						$name = !empty($contact->name) ? rawurldecode($contact->name) : $contact->name_convert;
					} else {
						$name = ' ';
					}

					$helpList = RedPackHelpDetail::find()->where(['jid' => $join->id, 'status' => 1, 'send_status' => 0])->all();
					if (!empty($helpList)) {
						foreach ($helpList as $help) {
							/** @var RedPackHelpDetail $help * */
							try {
								$remark   = '恭喜您，你帮“' . $name . '”拆红包，获得' . $help->amount . '元红包';
								$helpData = [
									'uid'         => $redPack->uid,
									'corp_id'     => $redPack->corp_id,
									'rid'         => $redPack->id,
									'jid'         => $join->id,
									'hid'         => $help->id,
									'external_id' => $help->external_id,
									'openid'      => $help->openid,
									'amount'      => $help->amount,
									'remark'      => $remark,
									'send_type'   => 3,
								];

								$res = RedPackOrder::sendRedPack($helpData);
								if (!empty($res)) {
									$help->send_status = 1;
									$help->send_type   = 1;
									$help->update();
									$is_send = 1;
								}
							} catch (InvalidDataException $e) {
								$is_send = 0;
								break 2;
								\Yii::error($e->getMessage(), 'handSendHelp');
							}
						}
					}
				}
				//补发剩余的
				if (!empty($is_send)) {
					\Yii::$app->queue->delay(10)->push(new SyncRedPackJob([
						'red_pack_id' => $redPack->id,
						'sendData'    => ['is_all' => 1, 'uid' => $redPack->uid]
					]));
				}
			}

			//更改状态
			if (!empty($status)) {
				$redPack->status = $status;
				//$redPack->give_out = RedPackOrder::getGiveOut($redPack->id);
				$redPack->update();
			}
		}

		//每天执行脚本
		public static function syncRedPack ()
		{
			\Yii::error('start', 'red_pack');
			$date       = date('Y-m-d');
			$start_date = $date . ' 00:00:00';
			$end_date   = $date . ' 23:59:59';
			$redPack    = static::find()->where(['status' => [1, 2]])->andWhere(['between', 'end_time', $start_date, $end_date])->select('id,end_time')->all();
			$time       = time();
			if (!empty($redPack)) {
				foreach ($redPack as $pack) {
					/** @var RedPack $pack * */
					\Yii::error($pack->id, 'red_pack_id');
					$end_time = strtotime($pack->end_time);
					$second   = $end_time - $time;
					if ($second > 0) {
						\Yii::$app->queue->delay($second)->push(new SyncRedPackJob([
							'red_pack_id' => $pack->id,
							'red_status'  => 3
						]));
					} else {
						\Yii::$app->queue->push(new SyncRedPackJob([
							'red_pack_id' => $pack->id,
							'red_status'  => 3
						]));
					}
				}
			}
		}

		//根据经纬度获取地址
		public static function getAddress ($lat, $lng)
		{
			if (empty($lat) || empty($lng)) {
				return [];
			}
			$location = $lat . ',' . $lng;
			$key      = \Yii::$app->params['tx_key'];
			$url      = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $location . '&key=' . $key;
			$result   = SUtils::postUrl($url, []);
			if (!empty($result['result']['address_component'])) {
				return $result['result']['address_component'];
			} else {
				\Yii::error($result['message'], 'tx_address');

				return [];
			}
		}

		//检查地区限制
		public static function checkArea ($address, $areaData)
		{
//			$address  = ['province' => '北京市', 'city' => '北京市', 'district' => '沙河区'];
//			$areaData = [
//				['北京市', '北京市', '朝阳区'],
//				['北京市', '北京市', '']
//			];
			\Yii::error($address, '$address');
			$is_limit = 1;
			if (!empty($address)) {
				foreach ($areaData as $area) {
					if ($area[0] == $address['province']) {
						if (empty($area[1])) {
							$is_limit = 0;
							break;
						} elseif ($area[1] == $address['city']) {
							if (empty($area[2])) {
								$is_limit = 0;
								break;
							} elseif ($area[2] == $address['district']) {
								$is_limit = 0;
								break;
							}
						}
					}
				}
			}

			return $is_limit;
		}

		//检查性别
		public static function checkSex ($external_id, $sex_type)
		{
			$is_limit    = 1;
			$customField = CustomField::findOne(['key' => 'sex', 'uid' => 0]);
			if (!empty($customField)) {
				//先查询高级属性字段
				//$fieldValue = CustomFieldValue::findOne(['type' => 1, 'cid' => $external_id, 'fieldid' => $customField->id]);
				$fieldValue = CustomFieldValue::find()->where(['type' => 1, 'cid' => $external_id, 'fieldid' => $customField->id])->orderBy(['id' => SORT_DESC])->one();
				if (!empty($fieldValue)) {
					if ($fieldValue->value == '男' && $sex_type == 2) {
						$is_limit = 0;
					} elseif ($fieldValue->value == '女' && $sex_type == 3) {
						$is_limit = 0;
					} elseif ($fieldValue->value == '未知' && $sex_type == 4) {
						$is_limit = 0;
					}
				} else {
					$contact = WorkExternalContact::findOne($external_id);
					if (!empty($contact)) {
						if ($contact->gender == 0 && $sex_type == 4) {
							$is_limit = 0;
						} elseif ($contact->gender == 1 && $sex_type == 2) {
							$is_limit = 0;
						} elseif ($contact->gender == 2 && $sex_type == 3) {
							$is_limit = 0;
						}
					}
				}
			}

			return $is_limit;
		}

		//获取活动的已发放金额
		public static function getGiveOut ($rid)
		{
			$amount = '0';
			//参与者发放金额
			$joinList = RedPackJoin::find()->where(['rid' => $rid])->andWhere(['or', ['first_send_status' => 1], ['status' => 2, 'send_status' => 1]])->all();
			foreach ($joinList as $join) {
				/**@var RedPackJoin $join * */
				if ($join->first_send_status == 1 && $join->send_status == 1) {
					$amount += $join->redpack_price;
				} elseif ($join->first_send_status == 1) {
					$amount += $join->first_amount;
				} elseif ($join->send_status == 1) {
					$amount += $join->rest_amount;
				}
			}

			//助力者发放金额
			$select     = new Expression('sum(amount) amount');
			$detailInfo = RedPackHelpDetail::find()->where(['rid' => $rid])->andWhere(['send_status' => 1])->select($select)->one();
			if (!empty($detailInfo)) {
				$amount += $detailInfo['amount'];
			}

			return (string) $amount;
		}

		//如果微信支付有钱，补发之前没发的红包
		public static function supplySend ($uid)
		{
			$cacheSendKey = 'supplySend_redPack_' . $uid;
			$cacheSend    = \Yii::$app->cache->get($cacheSendKey);
			if (!empty($cacheSend)) {
				return '';
			}
			\Yii::$app->cache->set($cacheSendKey, 1, 600);

			$joinList = RedPackJoin::find()->where(['uid' => $uid])->andWhere(['or', ['first_send_status' => 0], ['status' => 2, 'send_status' => 0]])->all();
			if (!empty($joinList)) {
				foreach ($joinList as $join) {
					/** @var RedPackJoin $join * */
					$redPack = RedPack::findOne($join->rid);
					if (empty($redPack) || in_array($redPack->status, [0, 1, 2])) {
						continue;
					}
					try {
						$remark    = '';
						$send_type = $amount = 0;
						if ($join->first_send_status == 0 && ($join->status == 2 && $join->send_status == 0)) {
							$remark                  = $join->invite_amount . '位好友已全部拆完，' . $join->redpack_price . '元红包拿走，不谢~~~';
							$amount                  = $join->redpack_price;
							$send_type               = 4;
							$join->first_send_status = 1;
							$join->first_send_type   = 1;
							$join->send_status       = 1;
							$join->send_type         = 1;
						} elseif ($join->first_send_status == 0) {
							$remark                  = '还有' . $join->rest_amount . '元正在路上，快召唤' . $join->invite_amount . '位好友一起拆红包，TA有，你也有~~';
							$amount                  = $join->first_amount;
							$send_type               = 1;
							$join->first_send_status = 1;
							$join->first_send_type   = 1;
						} elseif ($join->status == 2 && $join->send_status == 0) {
							$remark            = $join->invite_amount . '位好友已全部拆完，剩下的' . $join->rest_amount . '元红包拿走，不谢~~~';
							$amount            = $join->rest_amount;
							$send_type         = 2;
							$join->send_status = 1;
							$join->send_type   = 1;
						}
						if (!empty($send_type)) {
							$joinData = [
								'uid'         => $redPack->uid,
								'corp_id'     => $redPack->corp_id,
								'rid'         => $redPack->id,
								'jid'         => $join->id,
								'external_id' => $join->external_id,
								'openid'      => $join->openid,
								'amount'      => $amount,
								'remark'      => $remark,
								'send_type'   => $send_type,
							];

							$res = RedPackOrder::sendRedPack($joinData);
							if (!empty($res)) {
								$join->update();
							}
						}
					} catch (InvalidDataException $e) {
						break;
						\Yii::error($e->getMessage(), 'handSendJoin');
					}

					//助力者发放
					$contact = WorkExternalContact::findOne($join->external_id);
					if (!empty($contact)) {
						$name = !empty($contact->name) ? rawurldecode($contact->name) : $contact->name_convert;
					} else {
						$name = ' ';
					}

					$helpList = RedPackHelpDetail::find()->where(['jid' => $join->id, 'status' => 1, 'send_status' => 0])->all();
					if (!empty($helpList)) {
						foreach ($helpList as $help) {
							/** @var RedPackHelpDetail $help * */
							try {
								$remark   = '恭喜您，你帮“' . $name . '”拆红包，获得' . $help->amount . '元红包';
								$helpData = [
									'uid'         => $redPack->uid,
									'corp_id'     => $redPack->corp_id,
									'rid'         => $redPack->id,
									'jid'         => $join->id,
									'hid'         => $help->id,
									'external_id' => $help->external_id,
									'openid'      => $help->openid,
									'amount'      => $help->amount,
									'remark'      => $remark,
									'send_type'   => 3,
								];

								$res = RedPackOrder::sendRedPack($helpData);
								if (!empty($res)) {
									$help->send_status = 1;
									$help->send_type   = 1;
									$help->update();
								}
							} catch (InvalidDataException $e) {
								break 2;
								\Yii::error($e->getMessage(), 'handSendHelp');
							}
						}
					}
				}
			}
			\Yii::$app->cache->delete($cacheSendKey);
		}

        /*
         * 将活动置为结束
         * */
        public static function setActivityOver($id) {
            if (!$id) {
                return false;
            }
            $redData = RedPack::findOne($id);
            if (empty($redData)) {
                return false;
            }
            $redData->status = 5;
            $redData->save();
            \Yii::$app->queue->push(new SyncRedPackJob([
                'red_pack_id' => $redData->id,
                'red_status'  => 5
            ]));
        }
	}
