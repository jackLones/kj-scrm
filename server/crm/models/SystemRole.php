<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%system_role}}".
 *
 * @property int $id
 * @property int $parent_id 父类id
 * @property string $title
 * @property int $status 是否有效 1是0否
 * @property int $is_city 是否分配城市 1是0否
 * @property string $authority 权限
 */
class SystemRole extends \yii\db\ActiveRecord
{
	const AGENT_ROLE = [
		'1' => ['id' => 1, 'title' => '销售'],
		'2' => ['id' => 2, 'title' => '财务']
	];//代理商员工角色
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
	    return '{{%system_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'status', 'is_city'], 'integer'],
            [['authority'], 'string'],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'title' => 'Title',
            'status' => 'Status',
            'is_city' => 'Is City',
            'authority' => 'Authority',
        ];
    }

	/**
	 * @param string $roleData
	 *
	 * @return User|null
	 * @throws InvalidDataException
	 */
	public static function create ($roleData)
	{
		if (!empty($roleData['roleId'])) {
			$role = static::findOne($roleData['roleId']);

			if (empty($role)) {
				throw new InvalidDataException('角色数据错误！');
			}

			if ($roleData['status']){
				$hasRole = static::find()->andWhere(['title' => $roleData['title'], 'parent_id' => $roleData['parent_id'], 'status' => 1])->andWhere(['!=', 'id', $roleData['roleId']])->one();
				if (!empty($hasRole)) {
					throw new InvalidDataException('角色名称已存在，请更换！');
				}
			}
		} else {
			if ($roleData['status']){
				$hasRole = static::findOne(['title' => $roleData['title'], 'parent_id' => $roleData['parent_id'], 'status' => 1]);
				if (!empty($hasRole)) {
					throw new InvalidDataException('角色名称已存在，请更换！');
				}
			}

			$role            = new SystemRole();
			$role->authority = '';
		}

		$role->title     = $roleData['title'];
		$role->parent_id = $roleData['parent_id'];
		$role->status    = $roleData['status'];
		$role->is_city   = $roleData['is_city'];
		$role->authority = implode(',', $roleData['authority']);

		if ($role->validate() && $role->save()) {
			return true;
		} else {
			throw new InvalidDataException(SUtils::modelError($role));
		}
	}
}
