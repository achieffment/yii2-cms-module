<?php

namespace chieff\modules\Cms\models;

use creocoder\nestedsets\NestedSetsBehavior;

use chieff\modules\Cms\models\Page;
use chieff\modules\Cms\components\CategoryQuery;

use yii\db\Query;
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

    public function beforeDelete()
    {
        $pages = $this->pages;
        if ($pages) {
            foreach ($pages as $page) {
                if ($page->delete() === false) {
                    return false;
                }
            }
        }

        return parent::beforeDelete();
    }

    /**
     * Extended version of deleteWithChildren function of NestedSetsBehavior
     * Standart function don't fires beforeDelete and afterDelete event on each category that makes problems
     * @return bool
     */
    public function deleteWithChildrenExtended()
    {
        $query = self::find()
            ->where([
                'tree' => $this->tree
            ])
            ->andWhere([
                '>=', 'lft', $this->lft
            ])
            ->andWhere([
                '<=', 'rgt', $this->rgt
            ])->orderBy([
                'depth' => SORT_DESC,
            ])
            ->all();

        if ($query) {
            foreach ($query as $category) {
                if ($category->isRoot()) {
                    if ($category->deleteWithChildren() === false) {
                        return false;
                    } else {
                        continue;
                    }
                }
                if ($category->delete() === false) {
                    return false;
                }
            }
        }

        return true;
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

    public function getCategoryActivity()
    {
        // check cur category
        if (
            $this->active_from && !$this->active_to && time() < $this->active_from
        ) {
            return false;
        } else if (
            !$this->active_from && $this->active_to && time() > $this->active_to
        ) {
            return false;
        } else if (
            ($this->active_from && $this->active_to) &&
            (
                (time() < $this->active_from) ||
                (time() > $this->active_to)
            )
        ) {
            return false;
        }
        // check parents
        if ($this->depth > 0) {
            $categories = $this->parents()->all();
            if ($categories) {
                foreach ($categories as $category) {
                    if (
                        $category->active_from && !$category->active_to && time() < $category->active_from
                    ) {
                        return false;
                    } else if (
                        !$category->active_from && $category->active_to && time() > $category->active_to
                    ) {
                        return false;
                    } else if (
                        ($category->active_from && $category->active_to) &&
                        (
                            (time() < $category->active_from) ||
                            (time() > $category->active_to)
                        )
                    ) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * prevent error from extended page method
     *
     * @return null
     */
    public function getCategory()
    {
        return null;
    }

    public function getPages()
    {
        return $this->hasMany(Page::className(), ['category_id' => 'id']);
    }

}