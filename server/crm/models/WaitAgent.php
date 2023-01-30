<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%wait_agent}}".
 *
 * @property int $id
 * @property int $corp_id 企业ID
 * @property int $agent_id 应用ID
 * @property int $create_time 创建时间
 *                            		*
 * @property WorkCorpAgent $agent
 * @property WorkCorp $corp
 */
class WaitAgent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wait_agent}}';
    }

    /**
     * {@inheritdoc}
     */
	public function rules ()
	{
		return [
			[['corp_id', 'agent_id', 'create_time'], 'integer'],
			[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
			[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
		];
	}

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'          => 'ID',
			'corp_id'     => '企业ID',
			'agent_id'    => '应用ID',
			'create_time' => '创建时间',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAgent ()
	{
		return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCorp ()
	{
		return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
	}

	/**
	 * @param $corpId
	 * @param $agentId
	 *
	 * @return bool
	 *
	 */
	public static function addGent ($corpId, $agentId)
	{
		$waitAgent = self::findOne(['corp_id' => $corpId]);
		if (empty($waitAgent)) {
			$waitAgent              = new WaitAgent();
			$waitAgent->create_time = time();
		}
		$waitAgent->corp_id  = $corpId;
		$waitAgent->agent_id = $agentId;
		$waitAgent->save();

		return true;
	}

	/**
	 * @param $corpId
	 *
	 * @return int
	 *
	 */
	public static function getData ($corpId)
	{
		$agentId = 0;
		$data    = self::findOne(['corp_id' => $corpId]);
		if (!empty($data)) {
			$agentId = $data->agent_id;
		}

		if (empty($agentId)) {
			$workAgent = WorkCorpAgent::find()->where(['corp_id' => $corpId, 'is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => [WorkCorpAgent::CUSTOM_AGENT, WorkCorpAgent::AUTH_AGENT]]);

			$workAgent = $workAgent->andWhere(['!=', 'agent_type', WorkCorpAgent::MINIAPP_AGENT]);

			$workAgent = $workAgent->select('name,id,agent_type,square_logo_url')->asArray()->all();

			$agentId = $workAgent[0]['id'];
		}

		return $agentId;
	}


}
