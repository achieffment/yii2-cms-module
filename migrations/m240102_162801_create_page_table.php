<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%page}}`.
 */
class m240102_162801_create_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(\Yii::$app->getModule('cms')->page_table, array(
            'id' => 'pk',
            'active' => 'tinyint not null default 1',
            'active_from' => 'int default null',
            'active_to' => 'int default null',
            'sort' => 'int not null default 500',
            'name' => 'varchar(250) not null',
            'slug' => 'varchar(100) not null',
            'menutitle' => 'varchar(250)',
            'menuhide' => 'tinyint not null default 0',
            'h1' => 'varchar(250)',
            'title' => 'varchar(250)',
            'description' => 'varchar(500)',
            'preview_image' => 'varchar(100)',
            'preview_text' => 'varchar(500)',
            'detail_image' => 'varchar(100)',
            'detail_text' => 'varchar(5000)',
            'created_at' => 'int',
            'updated_at' => 'int',
            'created_by' => 'int',
            'updated_by' => 'int',
            'category_id' => 'int',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(\Yii::$app->getModule('cms')->page_table);
    }
}
