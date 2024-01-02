<?php

namespace chieff\modules\Cms\controllers;

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
        $page = Page::findOne(['slug' => $slug]);
        if (!$page) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        return $this->render('view', compact('page'));
    }

}