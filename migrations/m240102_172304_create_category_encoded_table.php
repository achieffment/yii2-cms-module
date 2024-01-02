<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m240102_172304_create_category_encoded_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (\Yii::$app->getModule('cms')->dataEncodeMigration === false) {
            return true;
        }
        $this->createTable(\Yii::$app->getModule('cms')->category_table, array(
            'id' => 'pk',
            'active' => 'tinyint not null default 1',
            'active_from' => 'int default null',
            'active_to' => 'int default null',
            'sort' => 'int not null default 500',
            'name' => 'varchar(750) not null',
            'slug' => 'varchar(300) not null',
            'menutitle' => 'varchar(750)',
            'menuhide' => 'tinyint not null default 0',
            'h1' => 'varchar(750)',
            'title' => 'varchar(750)',
            'description' => 'varchar(1500)',
            'preview_image' => 'varchar(100)',
            'preview_text' => 'varchar(1500)',
            'detail_image' => 'varchar(100)',
            'detail_text' => 'text',
            'created_at' => 'int',
            'updated_at' => 'int',
            'created_by' => 'int',
            'updated_by' => 'int',
            'tree' => 'int not null',
            'lft' => 'int not null',
            'rgt' => 'int not null',
            'depth' => 'int not null',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (\Yii::$app->getModule('cms')->dataEncodeMigration === false) {
            return true;
        }
        $this->dropTable(\Yii::$app->getModule('cms')->category_table);
    }
}
