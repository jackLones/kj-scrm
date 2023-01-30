<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%youzan_shop}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%youzan_config}}`
	 */
	class m200527_083454_add_youzan_id_columns_to_youzan_shop_table extends Migration
	{
		/**./
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%youzan_shop}}', 'youzan_id', $this->integer(11)->unsigned()->after('id')->comment('有赞云配置表'));

			// creates index for column `youzan_id`
			$this->createIndex(
				'{{%idx-youzan_shop-youzan_id}}',
				'{{%youzan_shop}}',
				'youzan_id'
			);

			// add foreign key for table `{{%youzan_config}}`
			$this->addForeignKey(
				'{{%fk-youzan_shop-youzan_id}}',
				'{{%youzan_shop}}',
				'youzan_id',
				'{{%youzan_config}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%youzan_config}}`
			$this->dropForeignKey(
				'{{%fk-youzan_shop-youzan_id}}',
				'{{%youzan_shop}}'
			);

			// drops index for column `youzan_id`
			$this->dropIndex(
				'{{%idx-youzan_shop-youzan_id}}',
				'{{%youzan_shop}}'
			);

			$this->dropColumn('{{%youzan_shop}}', 'youzan_id');
		}
	}
