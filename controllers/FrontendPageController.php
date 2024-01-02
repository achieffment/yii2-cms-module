<?php

namespace chieff\modules\Cms\controllers;

use chieff\modules\Cms\CmsModule;
use chieff\modules\Cms\models\Page;

use yii\web\NotFoundHttpException;
use Yii;

class FrontendPageController extends \yii\web\Controller
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
        $model = Page::findOne(['slug' => $slug]);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        if (!$model->pageActivity) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $category = $model->category;

        $parents = [];
        if ($category && $category->depth > 0) {
            $parents = $category->parents()->all();
        }

        $backPath = $this->getBackPath($category, $parents, $model);

        return $this->render('view', compact('model', 'category', 'parents', 'backPath'));
    }

    public function getBackPath($category, $parents, $page)
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
        if ($category) {
            $path[] = [
                'label' => $category->menutitle ? $category->menutitle : $category->name,
                'url' => ['index', 'slug' => $category->slug]
            ];
        }
        $path[] = [
            'label' => $page->menutitle ? $page->menutitle : $page->name,
        ];
        return $path;
    }

}