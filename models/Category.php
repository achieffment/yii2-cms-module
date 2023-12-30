<?php

namespace chieff\modules\Cms\models;

use creocoder\nestedsets\NestedSetsBehavior;

use chieff\modules\Cms\components\CategoryQuery;

use yii\helpers\ArrayHelper;

use Yii;

class Category extends \chieff\modules\Cms\models\Page
{

    const TYPE = 'category';

    public $parent_id_field;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return Yii::$app->getModule('cms')->category_table;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ]);
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['parent_id_field', 'integer'],
            [['tree', 'lft', 'rgt', 'depth'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'parent_id_field' => 'Parent Id',
            'tree'  => 'Tree',
            'lft'   => 'Lft',
            'rgt'   => 'Rgt',
            'depth' => 'Depth',
        ]);
    }

    /**
     * Get parent's ID
     * @return \yii\db\ActiveQuery
     */
    public function getParentId()
    {
        $parent = $this->parent;
        return $parent ? $parent->id : null;
    }

    /**
     * Get parent's node
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->parents(1)->one();
    }

    /**
     * Get a full tree as a list, except the node and its children
     * @param  integer $node_id node's ID
     * @return array array of node
     */
    public static function getTree($node_id = 0)
    {
        // don't include children and the node
        $children = [];

        if (!empty($node_id))
            $children = array_merge(
                self::findOne($node_id)->children()->column(),
                [$node_id]
            );

        $rows = self::find()->select('id, name, depth')->where(['NOT IN', 'id', $children])->orderBy('tree, lft, sort')->all();

        $return = [];
        foreach ($rows as $row)
            $return[$row->id] = str_repeat('-', $row->depth) . ' ' . $row->name;

        return $return;
    }

}