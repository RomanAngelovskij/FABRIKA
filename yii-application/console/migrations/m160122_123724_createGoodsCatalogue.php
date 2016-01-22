<?php

use yii\db\Schema;
use yii\db\Migration;

class m160122_123724_createGoodsCatalogue extends Migration
{
    public function up()
    {
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('categories', [
			'id' => $this->primaryKey(),
			'name' => $this->string(60)->notNull(),
			'slug' => $this->string(100)->notNull()->unique(),
			'parentId' => $this->integer(11)->defaultValue(0)
		]);

		$this->createTable('items', [
			'id' => $this->primaryKey(),
			'catId' => $this->integer(11)->notNull(),
			'name' => $this->string(250)->notNull(),
			'images' => $this->text(),
			'description' => $this->text(),
			'quantity' => $this->integer(11)->notNull()->defaultValue(0)
		]);

		$this->createIndex('indx-categories-parent_id', 'categories', 'parentId');
		$this->createIndex('indx-items-cat_id', 'items', 'catId');

		$this->addForeignKey('fk-items-cat_id', 'items', 'catId', 'categories', 'id', 'CASCADE');
    }

    public function down()
    {
        echo "m160122_123724_createGoodsCatalogue cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
