<?php

namespace chieff\modules\Cms\controllers;

use chieff\helpers\SecurityHelper;
use chieff\modules\Cms\CmsModule;

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
            if (Yii::$app->getModule('cms')->dataEncode) {
                $model->scenario = 'encodedPage';
            } else {
                $model->scenario = 'defaultPage';
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if ($model->imageUploadComplex()) {
                    $redirect = $this->getRedirectPage('create', $model);
                    return $redirect === false ? '' : $this->redirect($redirect);
                } else {
                    if (!Yii::$app->session->getFlash('error')) {
                        Yii::$app->session->setFlash('error', CmsModule::t('back', 'Unknown error on uploading images'));
                    }
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
            if (Yii::$app->getModule('cms')->dataEncode) {
                $model->scenario = 'encodedPage';
            } else {
                $model->scenario = 'defaultPage';
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if ($model->imageUploadComplex()) {
                    $redirect = $this->getRedirectPage('update', $model);
                    return $redirect === false ? '' : $this->redirect($redirect);
                } else {
                    if (!Yii::$app->session->getFlash('error')) {
                        Yii::$app->session->setFlash('error', CmsModule::t('back', 'Unknown error on uploading images'));
                    }
                }
            }
        } else {

            $model->decodeAttributes();

            $model->preview_image_hidden = $model->preview_image;
            $model->detail_image_hidden = $model->detail_image;

        }
        return $this->renderIsAjax('update', compact('model'));
    }

    public function actionView($id) {
        $model = $this->findModel($id);

        $model->decodeAttributes();

        return $this->renderIsAjax('view', compact('model'));
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