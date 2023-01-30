<?php

	use app\models\WorkDepartment;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkUser;
	use yii\db\Migration;

	/**
	 * Class m201221_020149_unqiu_key
	 */
	class m201221_020149_unqiu_key_t extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$temp = true;
			while ($temp) {
				//员工
				$data1 = Yii::$app->db->createCommand("SELECT
							        max( id ) AS ids,
							        id,
							        corp_id,
							        userid,
							        `name`,
							        count( id ) AS cnt 
							FROM pig_work_user GROUP BY corp_id,userid HAVING cnt > 1 
							ORDER BY corp_id DESC,userid ASC")->queryAll();
				if (!empty($data1)) {
					foreach ($data1 as $value) {
						WorkUser::deleteAll(['id' => $value["ids"]]);
					}
				}
				//部门
				$data2 = Yii::$app->db->createCommand("SELECT
								        max( id ) AS ids,
								        id,
								        corp_id,
								        department_id,
								        `name`,
								        count( id ) AS cnt
								FROM
								        pig_work_department
								GROUP BY
								        corp_id,
								        department_id
								HAVING
								        cnt > 1
								ORDER BY
								        corp_id DESC,
								        department_id ASC")->queryAll();
				if (!empty($data2)) {
					foreach ($data2 as $value) {
						WorkDepartment::deleteAll(['id' => $value["ids"]]);
					}
				}
//				//外部联系人
				$data3 = Yii::$app->db->createCommand("SELECT
								        max( id ) AS ids,
								        external_userid,
								        user_id,
								        count( id ) AS cnt
								FROM
								        pig_work_external_contact_follow_user
								GROUP BY
								        external_userid,
								        user_id
								HAVING
								        cnt > 1
								ORDER BY
								        cnt DESC,
								        external_userid ASC")->queryAll();
				if (!empty($data3)) {
					foreach ($data3 as $value) {
						WorkExternalContactFollowUser::deleteAll(['id' => $value["ids"]]);
					}
				}
//				//外部联系人
				$data4 = Yii::$app->db->createCommand("SELECT
										        max( id ) AS ids,
										        corp_id,
										        external_userid,
										        count( id ) AS cnt
										FROM
										        pig_work_external_contact
										GROUP BY
										        corp_id,
										        external_userid
										HAVING
										        cnt > 1
										ORDER BY
										        cnt DESC,
										        corp_id ASC")->queryAll();
				if (!empty($data4)) {
					foreach ($data4 as $value) {
						WorkExternalContact::deleteAll(['id' => $value["ids"]]);
					}
				}
				if(empty($data1) && empty($data2) && empty($data3) && empty($data4)){
					$temp = false;
				}
			}
			$this->createIndex("WORK_USER_USER_ID_CORP_ID_UNIQUE", "{{%work_user}}", ["corp_id", "userid"], true);
			$this->createIndex("WORK_DEPARTMENT_DEPARTMENT_ID_CORP_ID_UNIQUE", "{{%work_department}}", ["corp_id", "department_id"], true);
			$this->createIndex("WORK_EXTERNAL_CONTACT_FOLLOW_USER_EXTERNAL_USERID_CORP_ID_UNIQUE", "{{%work_external_contact_follow_user}}", ["external_userid", "user_id"], true);
			$this->createIndex("WORK_EXTERNAL_CONTACT_EXTERNAL_USERID_CORP_ID_UNIQUE", "{{%work_external_contact}}", ["external_userid", "corp_id"], true);

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201221_020149_unqiu_key cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201221_020149_unqiu_key cannot be reverted.\n";

			return false;
		}
		*/
	}
