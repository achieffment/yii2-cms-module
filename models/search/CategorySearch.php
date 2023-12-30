<?php

namespace chieff\modules\Cms\models\search;

use chieff\modules\Cms\models\Category;

use yii\data\ActiveDataProvider;

use Yii;
use yii\web\NotFoundHttpException;

class CategorySearch extends \chieff\modules\Cms\models\search\PageSearch
{

    public function search($query, $params)
    {
        $query->joinWith(['createdBy', 'updatedBy']);

        /*
        $query->joinWith(['user']);
        // Don't let non-superadmin view superadmin activity
        if (!Yii::$app->user->isSuperadmin) {
            $query->andWhere([User::tableName() . '.superadmin' => 0]);
        }
        */

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['active' => $this->active]);
        $query->andFilterWhere(['sort' => $this->sort]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'slug', $this->slug]);
        $query->andFilterWhere(['menuhide' => $this->menuhide]);

        $query->andFilterWhere(['like', 'preview_text', $this->preview_text]);
        $query->andFilterWhere(['like', 'detail_text', $this->detail_text]);

        if ($this->created_at) {
            $tmp = explode(' - ', $this->created_at);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.created_at', strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        if ($this->updated_at) {
            $tmp = explode(' - ', $this->updated_at);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.updated_at', strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }

        $query->andFilterWhere([
            'or',
            ['like', Yii::$app->getModule('cms')->page_table . '.created_by', $this->created_by],
            ['like', Yii::$app->getModule('user-management')->user_table . '.username', $this->created_by]
        ]);
        $query->andFilterWhere([
            'or',
            ['like', Yii::$app->getModule('cms')->page_table . '.updated_by', $this->updated_by],
            ['like', Yii::$app->getModule('user-management')->user_table . '.username', $this->updated_by]
        ]);

        return $dataProvider;
    }

}