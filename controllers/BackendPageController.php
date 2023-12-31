<?php

namespace chieff\modules\Cms\controllers;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class BackendPageController extends \webvimark\components\AdminDefaultController
{

    public $modelClass = 'chieff\modules\Cms\models\Page';

    public $modelSearchClass = 'chieff\modules\Cms\models\search\PageSearch';

    public function actionCreate()
    {
        $model = new $this->modelClass;
        if ($this->scenarioOnCreate) {
            $model->scenario = $this->scenarioOnCreate;
        } else {
            $model->scenario = 'page';
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if ($model->imageUploadComplex()) {
                    $redirect = $this->getRedirectPage('create', $model);
                    return $redirect === false ? '' : $this->redirect($redirect);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось загрузить изображение");
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->renderIsAjax('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->scenarioOnUpdate) {
            $model->scenario = $this->scenarioOnUpdate;
        } else {
            $model->scenario = 'page';
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if ($model->imageUploadComplex()) {
                    $redirect = $this->getRedirectPage('update', $model);
                    return $redirect === false ? '' : $this->redirect($redirect);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось загрузить изображение");
                }
            }
        }
        return $this->renderIsAjax('update', compact('model'));
    }

    public function actionSwitchStatus($id)
    {
        $model = $this->findModel($id);
        return $model->switchStatus();
    }

    public function actionSwitchMenuStatus($id)
    {
        $model = $this->findModel($id);
        return $model->switchMenuStatus();
    }

}