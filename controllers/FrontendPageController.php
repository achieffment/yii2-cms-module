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

        // getting encoded values
        $model->name = $model->getAttributeValue('name');
        $model->slug = $model->getAttributeValue('slug');
        $model->menutitle = $model->getAttributeValue('menutitle');
        $model->h1 = $model->getAttributeValue('h1');
        $model->title = $model->getAttributeValue('title');
        $model->description = $model->getAttributeValue('description');
        $model->preview_text = $model->getAttributeValue('preview_text');
        $model->detail_text = $model->getAttributeValue('detail_text');

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
                $path[] = [
                    'label' => $parent->menutitle ? $parent->getAttributeValue('menutitle') : $parent->getAttributeValue('slug'),
                    'url' => '/category/' . $parent->getAttributeValue('slug')
                ];
            }
        }
        if ($category) {
            $path[] = [
                'label' => $category->menutitle ? $category->getAttributeValue('menutitle') : $category->getAttributeValue('name'),
                'url' => '/category/' . $category->getAttributeValue('slug')
            ];
        }
        $path[] = [
            'label' => $page->menutitle ? $page->menutitle : $page->name,
        ];
        return $path;
    }

}