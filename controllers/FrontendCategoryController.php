<?php

namespace chieff\modules\Cms\controllers;

use chieff\modules\Cms\models\Category;

use yii\web\NotFoundHttpException;
use Yii;

class FrontendCategoryController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    public function actionView($slug)
    {
        $category = Category::findOne(['slug' => $slug]);
        if (!$category) {
            throw new NotFoundHttpException(Yii::t('yii', 'Category not found.'));
        }
        if (!$category->categoryActivity) {
            throw new NotFoundHttpException(Yii::t('yii', 'Category not found.'));
        }
        return $this->render('view', compact('category'));
    }

}