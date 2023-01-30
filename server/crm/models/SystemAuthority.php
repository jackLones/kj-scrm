<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%system_authority}}".
 *
 * @property int $id
 * @property int $pid 父类id
 * @property string $url url
 * @property string $title
 * @property int $nav_display  是否菜单显示 1显示，0不显示 
 * @property int $nav_type 菜单类型 0 菜单，1 url 
 * @property int $status
 * @property int $sort 排序
 * @property string $module
 * @property string $controller
 * @property string $method
 */
class SystemAuthority extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system_authority}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'nav_display', 'nav_type', 'status', 'sort'], 'integer'],
            [['module', 'controller'], 'required'],
            [['url'], 'string', 'max' => 255],
            [['title', 'module', 'controller', 'method'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'url' => 'Url',
            'title' => 'Title',
            'nav_display' => 'Nav Display',
            'nav_type' => 'Nav Type',
            'status' => 'Status',
            'sort' => 'Sort',
            'module' => 'Module',
            'controller' => 'Controller',
            'method' => 'Method',
        ];
    }

	/**
	 * @param string $data
	 *
	 * @return User|null
	 * @throws InvalidDataException
	 */
	public static function create ($data)
	{
		if (!empty($data['id'])) {
			$authority = static::findOne($data['id']);

			if (empty($authority)) {
				throw new InvalidDataException('权限数据错误！');
			}
		} else {
			$hasAuthority = static::findOne(['url' => $data['url'], 'title' => $data['title'], 'status' => 1]);
			if (!empty($hasAuthority)) {
				throw new InvalidDataException('方法已存在于pid=' . $hasAuthority->pid . '权限下');
			}

			$authority = new SystemAuthority();
		}

		$authority->pid         = $data['pid'];
		$authority->url         = $data['url'];
		$authority->title       = $data['title'];
		$authority->nav_display = $data['nav_display'];
		$authority->nav_type    = $data['nav_type'];
		$authority->status      = $data['status'];
		$authority->module      = $data['module'];
		$authority->controller  = $data['controller'];
		$authority->method      = $data['method'];

		if ($authority->validate() && $authority->save()) {
			return $authority->id;
		} else {
			throw new InvalidDataException(SUtils::modelError($authority));
		}
	}

	/**
	 * 获取全部权限菜单
	 * $isFunction 是否显示功能按钮 1是0否
	 *
	 * @return User|null
	 * @throws InvalidDataException
	 */
	public static function getAllAuthority ($isFunction = 0)
	{
		$authorityData = static::find()->andWhere(['pid' => 0, 'status' => 1, 'nav_display' => 1])->asArray()->all();
		if ($isFunction == 0){
			$whereArr = ['nav_display' => 1];
		}else{
			$whereArr = ['or', ['nav_display' => 1], ['nav_type' => 1]];
		}
		foreach ($authorityData as $k => $v) {
			$children                      = static::find()->andWhere(['pid' => $v['id'], 'status' => 1])->andWhere($whereArr)->asArray()->all();
			$authorityData[$k]['children'] = !empty($children) ? $children : [];
		}

		return $authorityData;
	}
}
