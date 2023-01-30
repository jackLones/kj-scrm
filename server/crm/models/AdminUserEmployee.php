<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\StringUtil;
use app\util\SUtils;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%admin_user_employee}}".
 *
 * @property int $id
 * @property int $uid 帐号id
 * @property int $pid 父级id
 * @property int $role_id 角色id
 * @property string $account 帐号
 * @property string $pwd 加密后的密码
 * @property string $salt 加密校验码
 * @property string $phone 电话
 * @property string $name 姓名
 * @property int $status  0不可用 1可用
 * @property int $add_time 添加日期
 * @property int $upt_time 添加日期
 * @property int $city_all 是否全国
 */
class AdminUserEmployee extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
	    return '{{%admin_user_employee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'pwd', 'salt', 'name'], 'required'],
            [['uid', 'pid', 'role_id', 'status', 'add_time', 'upt_time', 'city_all'], 'integer'],
            [['account'], 'string', 'max' => 100],
            [['pwd'], 'string', 'max' => 255],
            [['salt'], 'string', 'max' => 10],
            [['phone'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'       => 'ID',
			'uid'      => 'Uid',
			'pid'      => 'Pid',
			'role_id'  => 'Role ID',
			'account'  => 'Account',
			'pwd'      => 'Pwd',
			'salt'     => 'Salt',
			'phone'    => 'Phone',
			'name'     => 'Name',
			'status'   => 'Status',
			'add_time' => 'Add Time',
			'upt_time' => 'Upt Time',
			'city_all' => 'City All',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentity ($id)
	{
		return static::findOne($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentityByAccessToken ($token, $type = NULL)
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId ()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthKey ()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateAuthKey ($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

	/**
	 * Finds user by username
	 *
	 * @param string $identifier
	 *
	 * @return User|array|ActiveRecord|null
	 */
	public static function findIdentityByIdentifier ($identifier, $uid)
	{
		return static::findOne(['account' => $identifier, 'uid' => $uid]);
	}

	/**
	 * @param integer $eData
	 *
	 * @return User|null
	 * @throws InvalidDataException
	 */
	public static function create ($eData)
	{
		if (!empty($eData['id'])) {
			$employee = static::findOne($eData['id']);

			if (empty($employee)) {
				throw new InvalidDataException('员工数据错误！');
			}

			$employee->upt_time = time();
		} else {
			$hasEmployee = static::findOne(['account' => $eData['account'], 'uid' => $eData['uid']]);
			if (!empty($hasEmployee)) {
				throw new InvalidDataException('员工帐号已存在，请更换！');
			}

			$employee           = new AdminUserEmployee();
			$employee->uid      = $eData['uid'];
			$employee->pid      = $eData['pid'];
			$employee->account  = $eData['account'];
			$employee->add_time = time();
		}

		$employee->role_id = $eData['role_id'];
		if ($eData['pwd']) {
			$employee->salt = StringUtil::randomStr(6, true);
			$employee->pwd  = StringUtil::encodePassword($employee->salt, $eData['pwd']);
		}
		$employee->phone    = $eData['phone'];
		$employee->name     = $eData['name'];
		$employee->status   = $eData['status'];
		$employee->city_all = $eData['city_all'];

		if ($employee->validate() && $employee->save()) {
			return true;
		} else {
			throw new InvalidDataException(SUtils::modelError($employee));
		}
	}

	/**
	 * 员工菜单权限
	 * @param integer $eid
	 *
	 * @return User|null
	 * @throws InvalidDataException
	 */
	public static function getEmployeeAuthority ($eid)
	{
		$employeeRole = AdminUserEmployee::find()->alias('e');
		$employeeRole = $employeeRole->leftJoin('{{%system_role}} r', '`e`.`role_id` = `r`.`id`');
		$employeeRole = $employeeRole->select('r.authority');
		$employeeRole = $employeeRole->where(['e.id' => $eid]);
		$employeeRole = $employeeRole->asArray()->one();

		$authorityData = [];
		if (!empty($employeeRole) && !empty($employeeRole['authority'])) {
			$authorityArr = explode(',', $employeeRole['authority']);

			$authorityChildren = SystemAuthority::find()->andWhere(['status' => 1])->andWhere(['id' => $authorityArr])->andWhere(['>', 'pid', 0])->all();
			foreach ($authorityChildren as $c){
				if (!in_array($c->pid, $authorityArr)){
					array_push($authorityArr, strval($c->pid));
				}
			}

			$authorityData = SystemAuthority::find()->andWhere(['pid' => 0, 'status' => 1, 'nav_display' => 1]);
			$authorityData = $authorityData->andWhere(['id' => $authorityArr])->asArray()->all();
			foreach ($authorityData as $k => $v) {
				$children = SystemAuthority::find()->andWhere(['pid' => $v['id'], 'status' => 1, 'nav_display' => 1])->andWhere(['id' => $authorityArr])->asArray()->all();

				if (!empty($children)) {
					$authorityData[$k]['children'] = $children;
				} else {
					unset($authorityData[$k]);
				}
			}
		}

		return $authorityData;
	}

	/**
	 * 员工功能权限
	 * @param integer $eid
	 *
	 * @return User|null
	 * @throws InvalidDataException
	 */
	public static function getEmployeeFunctionAuthority ($eid)
	{
		$employeeRole = AdminUserEmployee::find()->alias('e');
		$employeeRole = $employeeRole->leftJoin('{{%system_role}} r', '`e`.`role_id` = `r`.`id`');
		$employeeRole = $employeeRole->select('r.authority');
		$employeeRole = $employeeRole->where(['e.id' => $eid]);
		$employeeRole = $employeeRole->asArray()->one();

		$authorityData = [];
		if (!empty($employeeRole) && !empty($employeeRole['authority'])) {
			$authorityArr = explode(',', $employeeRole['authority']);

			$authority = SystemAuthority::find()->andWhere(['status' => 1, 'nav_type' => 1])->andWhere(['id' => $authorityArr])->all();
			foreach ($authority as $c){
				array_push($authorityData, $c->method);
			}
		}

		return $authorityData;
	}
}
