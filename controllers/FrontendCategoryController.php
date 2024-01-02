<?php

namespace chieff\modules\Cms\controllers;

use chieff\helpers\SecurityHelper;
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
        if (Yii::$app->getModule('cms')->dataEncode) {
            $slug = SecurityHelper::encode($slug, 'aes-256-ctr', Yii::$app->getModule('cms')->passphrase);
        }
        $model = Category::findOne(['slug' => $slug]);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Category not found.'));
        }
        if (!$model->activity) {
            throw new NotFoundHttpException(Yii::t('yii', 'Category not found.'));
        }

        // getting encoded values
        $model->name = $model->getAttributeValue('name');
        $model->slug = $model->getAttributeValue('slug');
        $model->menutitle = $model->getAttributeValue('menutitle');
        $model->h1 = $model->getAttributeValue('h1');
        $model->title = $model->getAttributeValue('title');
        $model->description = $model->getAttributeValue('description');
        $model->preview_text = $model->getAttributeValue('preview_text');
        $model->detail_text = $model->getAttributeValue('detail_text');

        $parents = [];
        if ($model->depth > 0) {
            $parents = $model->parents()->all();
        }

        $siblings = $model->getSiblings(true);

        $children = $model->getChildrenExtended(true);

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

        $pages = $model->getPagesActive(true);

        $backPath = $this->getBackPath($model, $parents);

        return $this->render('view', compact('model', 'parents', 'siblings', 'children', 'subCategories', 'pages', 'backPath'));
    }

    public function getBackPath($category, $parents)
    {
        $path = [];
        if ($parents) {
            foreach ($parents as $parent) {
                $path[] = [
                    'label' => $parent->menutitle ? $parent->getAttributeValue('menutitle') : $parent->getAttributeValue('name'),
                    'url' => '/category/' . $parent->getAttributeValue('slug')
                ];
            }
        }
        $path[] = [
            'label' => $category->menutitle ? $category->menutitle : $category->name,
        ];
        return $path;
    }

}