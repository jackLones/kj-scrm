<?php

namespace app\models;

use app\components\InvalidDataException;
use app\queue\WorkImportCustomerSendingJob;
use app\util\SUtils;
use app\util\WebsocketUtil;
use Yii;

/**
 * This is the model class for table "{{%work_import_customer}}".
 *
 * @property int $id
 * @property int $corp_id 企业微信id
 * @property int $agentid 应用id
 * @property string $title 导入表格名称
 * @property string $user_ids 分配员工集合
 * @property int $all_num 总条数
 * @property int $snum 导入客户数
 * @property int $is_del 是否删除1是0否
 * @property int $add_time 创建时间
 * @property int $upt_time 修改时间
 * @property string $tag_ids 客户标签
 * @property string $distribution_records 客户导入分配记录
 */
class WorkImportCustomer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_import_customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id'], 'required'],
            [['corp_id', 'agentid', 'all_num', 'snum', 'is_del', 'add_time', 'upt_time'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['user_ids'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'       => 'ID',
			'corp_id'  => 'Corp ID',
			'agentid'  => 'Agentid',
			'title'    => 'Title',
			'user_ids' => 'User Ids',
			'all_num'  => 'All Snum',
			'snum'     => 'Snum',
			'is_del'   => 'Is Del',
			'add_time' => 'Add Time',
			'upt_time' => 'Upt Time',
		];
	}

	/**
	 * @param $data
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	public static function create ($data)
	{
		$insertNum  = $skipNum = $skipPhoneNum = 0;
		$importData = $data['importData'];
		$snum       = count($importData) - 1;
		$Tuser_ids  = explode(',', $data['user_ids']);
		$user_ids   = [];
		if (!empty($Tuser_ids)) {
			$Temp = WorkDepartment::GiveUserIdsReturnDepartmentAndUserIds($Tuser_ids);
			[$AgentDepartmentTemp, $AgentUserIdsTemp,$AgentDepartmentOld] = WorkDepartment::GiveAgentIdReturnDepartmentOrUser($data['corp_id'], $data["agentid"], 0, 0);
			$AgentDepartment = $Temp["department"];
			$AgentUserIds    = $Temp["user"];
			if (!empty($Temp["department"])) {
				$AgentDepartment = array_intersect($Temp["department"], $AgentDepartmentOld);
			}
			if (!empty($Temp["user"])) {
				$AgentUserIds = array_intersect($Temp["user"], $AgentUserIdsTemp);
			}
			$user_ids = WorkDepartment::GiveDepartmentReturnUserData($data['corp_id'],$AgentDepartment, $AgentUserIds, 0, true,0,[]);
		}
        $distributionRecords = [['distribution_time'=>date('Y-m-d H:i:s'),'user_ids'=>$Tuser_ids]];
		$sub_id           = $data['sub_id'];
		$import           = new WorkImportCustomer();
		$import->corp_id  = $data['corp_id'];
		$import->agentid  = $data['agentid'];
		$import->title    = $data['title'];
		$import->user_ids = json_encode($Tuser_ids);
        $import->distribution_records = json_encode($distributionRecords);
		$import->all_num  = $snum;
		$import->snum     = $insertNum;
		$import->is_del   = 0;
		$import->add_time = time();
		$import->tag_ids = is_array($data['tag_ids']) ? implode(',',$data['tag_ids']) : $data['tag_ids'];

		if (!$import->validate() || !$import->save()) {
			throw new InvalidDataException(SUtils::modelError($import));
		}

		$userCount  = count($user_ids);
		$userNum    = 0;
		$allotNum   = [];

		$corpId   = $import->corp_id;
		$userCorp = UserCorpRelation::findOne(['corp_id' => $corpId]);
		static::importNumWebsocket($userCorp->uid, $data['corpid'], $snum, 0,0, 0, $sub_id);
		if ($snum > 1000) {
			$perKey = ceil($snum / 50);
		} else {
			$perKey = 10;
		}

		$importPhone = [];
		foreach ($importData as $k => $v) {
			if ($k == 1) {
				continue;
			}
			//分配员工
			$user_id = $user_ids[$userNum];

			$detailData = [
				'corp_id'   => $import->corp_id,
				'import_id' => $import->id,
				'user_id'   => $user_id,
				'phone'     => trim(strval($v['A'])),
				'nickname'  => strval($v['B']),
				'name'      => strval($v['C']),
				'sex'       => strval($v['D']),
				'area'      => strval($v['E']),
				'des'       => strval($v['F']),
			];
			//过滤重复手机号
			if (empty($detailData['phone'])) {
				$type = 'skipPhone';
			} else {
				if (in_array($detailData['phone'], $importPhone)) {
					$type = 'skip';
				} else {
					$importPhone[] = $detailData['phone'];
					$type          = WorkImportCustomerDetail::setCustomer($detailData);
				}
			}

			switch ($type) {
				case 'insert':
					$insertNum++;

					$userNum++;
					if ($userNum >= $userCount) {
						$userNum = 0;
					}
					if (!isset($allotNum[$user_id])) {
						$allotNum[$user_id] = 1;
					} else {
						$allotNum[$user_id] += 1;
					}

					break;
				case 'skip':
					$skipNum++;
					break;
				case 'skipPhone':
					$skipPhoneNum++;
					break;
			}

			if (($k - 1) % $perKey == 0){
				static::importNumWebsocket($userCorp->uid, $data['corpid'], $snum, $k - 1, $insertNum, $skipNum + $skipPhoneNum, $sub_id);
			}
		}

		if ($insertNum > 0) {
			$import->snum = $insertNum;
			$import->save();

			//发提醒消息
			foreach ($user_ids as $user_id) {
				if (isset($allotNum[$user_id]) && $allotNum[$user_id] > 0) {
					$msgSend            = new WorkImportCustomerMsgSend();
					$msgSend->corp_id   = $import->corp_id;
					$msgSend->import_id = $import->id;
					$msgSend->user_id   = $user_id;
					$msgSend->add_num   = $allotNum[$user_id];
					$msgSend->status    = 0;

					if ($msgSend->save()) {
						\Yii::$app->work->push(new WorkImportCustomerSendingJob([
							'work_import_customer_send_id' => $msgSend->id
						]));
					}
				}
			}
		}else{
            $import->delete();
        }

		$textHtml = '本次导入成功' . $insertNum . '条，';
		if (!empty($skipNum)) {
			$textHtml .= '忽略' . $skipNum . '条（已有的），';
		}
		if (!empty($skipPhoneNum)) {
			$textHtml .= $skipPhoneNum . '条手机号格式不正确，';
		}
		$textHtml = trim($textHtml, '，');

		static::importNumWebsocket($userCorp->uid, $data['corpid'], $snum, $snum, $insertNum, $skipNum + $skipPhoneNum, $sub_id, $textHtml);

		return true;
	}

	/**
	 * 导入数量发送
	 *
	 * @param $uid
	 * @param $corpid
	 * @param $snum
	 * @param $import_num
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 * @throws \app\components\InvalidParameterException]
	 */
	private static function importNumWebsocket ($uid, $corpid, $snum, $import_num, $insertNum = 0, $skipNum = 0, $sub_id = 0, $msg = '', $error = 0)
	{
		\Yii::$app->websocket->send([
			'channel' => 'push-message',
			'to'      => $uid,
			'type'    => WebsocketUtil::IMPORT_TYPE,
			'info'    => [
				'type'         => 'import_customer',
				'from'         => $uid,
				'corpid'       => $corpid,
				'sub_id'       => $sub_id,
				'snum'         => $snum,
				'import_num'   => $import_num,
				'notImportNum' => $snum - $import_num,
				'successNum'   => $insertNum,
				'failNum'      => $skipNum,
				'textHtml'     => $msg,
				'error'        => $error,
			]
		]);

		return true;
	}
}
