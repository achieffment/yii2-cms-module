<?php

namespace chieff\modules\Cms\models\search;

use chieff\modules\Cms\models\Page;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use Yii;

class PageSearch extends Page
{

    public function rules()
    {
        return [
            [['id', 'sort', 'category_id'], 'integer'],
            [['active', 'menuhide'], 'boolean'],
            [['slug', 'preview_text', 'detail_text'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Page::find();
        $query->joinWith(['createdBy', 'updatedBy']);

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
        $query->andFilterWhere(['category_id' => $this->category_id]);
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