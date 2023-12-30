<?php

namespace chieff\modules\Cms\components;

use creocoder\nestedsets\NestedSetsQueryBehavior;

class CategoryQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}