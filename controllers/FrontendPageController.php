<?php

namespace chieff\modules\Cms\controllers;

use chieff\helpers\SecurityHelper;
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
        if (Yii::$app->getModule('cms')->dataEncode) {
            $slug = SecurityHelper::encode($slug, 'aes-256-ctr', Yii::$app->getModule('cms')->passphrase);
        }
        $model = Page::findOne(['slug' => $slug]);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        if (!$model->activity) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model->decodeAttributes();

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
        $path = [];
        if ($parents) {
            foreach ($parents as $parent) {
                $parent->decodeAttributes(['menutitle', 'name', 'slug']);
                $path[] = [
                    'label' => $parent->menutitle ? $parent->menutitle : $parent->name,
                    'url' => '/category/' . $parent->slug
                ];
            }
        }
        if ($category) {
            $category->decodeAttributes(['menutitle', 'name', 'slug']);
            $path[] = [
                'label' => $category->menutitle ? $category->menutitle : $category->name,
                'url' => '/category/' . $category->slug
            ];
        }
        $path[] = [
            'label' => $page->menutitle ? $page->menutitle : $page->name,
        ];
        return $path;
    }

}