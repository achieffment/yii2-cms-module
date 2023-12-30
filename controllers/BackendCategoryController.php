<?php

namespace chieff\modules\Cms\controllers;

use chieff\modules\Cms\CmsModule;
use chieff\modules\Cms\models\Category;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

class BackendCategoryController extends \chieff\modules\Cms\controllers\BackendPageController
{

    public $modelClass = 'chieff\modules\Cms\models\Category';

    public $modelSearchClass = 'chieff\modules\Cms\models\search\CategorySearch';

    public function actionIndex($categoryId = null)
    {
        $modelClass = $this->modelClass;

        $query = $modelClass::find();
        $queryParams = [];
        $model = null;
        if ($categoryId) {
            $model = $this->findModel(['id' => $categoryId]);
            if ($model->isRoot()) {
                $queryParams = [
                    'tree' => $model->id,
                    'depth' => $model->depth + 1
                ];
            } else {
                $parentId = $model->parentId;
                $queryParams = [
                    'tree' => $parentId,
                    'depth' => $model->depth + 1
                ];
            }
        } else {
            $queryParams = [
                'depth' => 0
            ];
        }
        $query->where($queryParams);

        $backPath = $this->getBackPath($categoryId);
        $backLink = $this->getBackLink($categoryId);

        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;
        if ($searchModel) {
            $dataProvider = $searchModel->search($query, Yii::$app->request->getQueryParams());
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'categoryId', 'backPath', 'backLink'));
    }

    public function actionCreate($categoryId = null)
    {
        $model = new $this->modelClass;
        if ($this->scenarioOnCreate) {
            $model->scenario = $this->scenarioOnCreate;
        }

        $backPath = $this->getBackPath($categoryId, true, true);
        $backLink = $this->getBackLink($categoryId, true);

        if ($model->load(Yii::$app->request->post())) {

            $result = false;
            if ($model->parent_id_field) {
                $parent = Category::findOne($model->parent_id_field);
                $result = $model->appendTo($parent);
            } else {
                $result = $model->makeRoot();
            }

            if ($result && $model->save()) {
                if ($model->imageUploadComplex()) {

                    $redirect = $this->getRedirectPage('create', $model);
                    if ($redirect !== false && $categoryId) {
                        $redirect['categoryId'] = $categoryId;
                    }

                    return $redirect === false ? '' : $this->redirect($redirect);
                } else {

                    Yii::$app->session->setFlash('error', "Не удалось загрузить изображение");
                    $params = ['update', 'id' => $model->id];
                    if ($categoryId) {
                        $params['categoryId'] = $categoryId;
                    }

                    return $this->redirect($categoryId);
                }
            }

        } else {

            $model->loadDefaultValues();
            if ($categoryId) {
                $model->parent_id_field = $categoryId;
            }

        }

        return $this->renderIsAjax('create', compact('model', 'categoryId', 'backPath', 'backLink'));
    }

    public function actionUpdate($id, $categoryId = null)
    {
        $model = $this->findModel($id);
        if ($this->scenarioOnUpdate) {
            $model->scenario = $this->scenarioOnUpdate;
        }

        $backPath = $this->getBackPath($categoryId, true, true);
        $backLink = $this->getBackLink($categoryId, true);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {

                if (empty($model->parent_id_field)) {
                    if (!$model->isRoot())
                        $model->makeRoot();
                } else {
                    if ($model->id != $model->parent_id_field) {
                        $parent = Category::findOne($model->parent_id_field);
                        $model->appendTo($parent);
                    }
                }

                if ($model->imageUploadComplex()) {

                    $redirect = $this->getRedirectPage('update', $model);
                    if ($redirect !== false && $categoryId) {
                        $redirect['categoryId'] = $categoryId;
                    }

                    return $redirect === false ? '' : $this->redirect($redirect);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось загрузить изображение");
                }

            }

        } else {

            $model->parent_id_field = $model->parentId;

        }
        return $this->renderIsAjax('update', compact('model', 'categoryId', 'backPath', 'backLink'));
    }

    public function actionDelete($id, $categoryId = null)
    {
        $model = $this->findModel($id);

        $result = $model->deleteWithChildren();

        $redirect = false;
        if ($result) {
            $redirect = $this->getRedirectPage('delete', $model);
            if ($redirect !== false && $categoryId) {
                $redirect['categoryId'] = $categoryId;
            }
        }

        return $redirect === false ? '' : $this->redirect($redirect);
    }

    public function actionView($id, $categoryId = null) {
        $model = $this->findModel($id);

        $backPath = $this->getBackPath($categoryId, true, true);
        $backLink = $this->getBackLink($categoryId, true);

        return $this->renderIsAjax('view', compact('model', 'categoryId', 'backPath', 'backLink'));
    }

    public function actionBulkDelete()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelClass;
            foreach (Yii::$app->request->post('selection', []) as $id) {
                $model = $modelClass::findOne($id);
                if ($model) {
                    $model->deleteWithChildren();
                }
            }
        }
    }

    public function getBackPath($categoryId = null, $main = false, $current = false)
    {
        $path = [
            [
                'label' => CmsModule::t('back', 'Categories')
            ]
        ];
        if ($categoryId) {
            $path = [
                [
                    'label' => CmsModule::t('back', 'Categories'),
                    'url' => ['index']
                ]
            ];
            $model = $this->findModel($categoryId);
            $parents = $model->parents()->all();
            if ($parents) {
                foreach ($parents as $parent) {
                    $path[] = [
                        'label' => $parent->name,
                        'url' => ['index', 'categoryId' => $parent->id]
                    ];
                }
            }
            if ($current) {
                $path[] = [
                    'label' => $model->name,
                    'url' => ['index', 'categoryId' => $model->id]
                ];
            } else {
                $path[] = [
                    'label' => $model->name,
                ];
            }
        } else if ($main) {
            $path = [
                [
                    'label' => CmsModule::t('back', 'Categories'),
                    'url' => ['index']
                ]
            ];
        }
        return $path;
    }

    public function getBackLink($categoryId = null, $current = false)
    {
        if ($categoryId) {
            $model = $this->findModel($categoryId);
            if ($current) {
                $backLinkParams = ['index', 'categoryId' => $model->id];
            } else if (!$model->isRoot()) {
                $backLinkParams = ['index', 'categoryId' => $model->parentId];
            } else {
                $backLinkParams = ['index'];
            }
            return Html::a(
                '<i class="fa fa-arrow-left"></i> ' . CmsModule::t('back', 'Back'),
                $backLinkParams,
                ['class' => 'btn btn-secondary']
            );
        }
        return '';
    }

}