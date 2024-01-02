<?php

namespace chieff\modules\Cms\controllers;

use chieff\modules\Cms\CmsModule;
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
        $model = Category::findOne(['slug' => $slug]);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Category not found.'));
        }
        if (!$model->activity) {
            throw new NotFoundHttpException(Yii::t('yii', 'Category not found.'));
        }

        $parents = [];
        if ($model->depth > 0) {
            $parents = $model->parents()->all();
        }

        $siblings = $model->siblings;

        $children = $model->getChildrenExtended();

        $subCategories = [];
        if ($parents) {
            $subCategories[] = $parents[0];
        }
        if ($siblings) {
            $subCategories = array_merge($subCategories, $siblings);
        }
        if ($children) {
            $subCategories = array_merge($subCategories, $children);
        }

        $pages = $model->pages;

        $backPath = $this->getBackPath($model, $parents);

        return $this->render('view', compact('model', 'parents', 'siblings', 'children', 'subCategories', 'pages', 'backPath'));
    }

    public function getBackPath($category, $parents)
    {
        $path = [
            ['label' => CmsModule::t('back', 'Index'), 'url' => '/']
        ];
        if ($parents) {
            foreach ($parents as $parent) {
                $path[] = [
                    'label' => $parent->menutitle ? $parent->menutitle : $parent->name,
                    'url' => '/category/' . $parent->slug
                    // 'url' => ['category', 'slug' => $parent->slug]
                ];
            }
        }
        $path[] = [
            'label' => $category->menutitle ? $category->menutitle : $category->name,
            // 'url' => ['index', 'slug' => $category->slug]
        ];
        return $path;
    }

}