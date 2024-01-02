<?php

use chieff\modules\Cms\CmsModule;

use yii\widgets\DetailView;
use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\Cms\models\Category $model
 */

$this->title = $model->name;
foreach ($backPath as $path) {
    $this->params['breadcrumbs'][] = $path;
}
$this->params['breadcrumbs'][] = ['label' => $model->name];

?>
<div class="user-view">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <p>
            <?
            $paramsCreate = ['create'];
            $paramsUpdate = ['update', 'id' => $model->id];
            $paramsDelete = ['delete', 'id' => $model->id];
            if ($categoryId) {
                $paramsCreate['categoryId'] = $categoryId;
                $paramsUpdate['categoryId'] = $categoryId;
                $paramsDelete['categoryId'] = $categoryId;
            }
            ?>
            <?= Html::a(CmsModule::t('back', 'Create'), $paramsCreate, ['class' => 'btn btn-success']) ?>
            <?= Html::a(CmsModule::t('back', 'Edit'), $paramsUpdate, ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('yii', 'Delete'), $paramsDelete, [
                'class' => 'btn btn-danger pull-right',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= $backLink ?>
        </p>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'active:boolean',
                    'active_from:datetime',
                    'active_to:datetime',
                    'sort',
                    'name',
                    'slug',
                    'menutitle',
                    'menuhide:boolean',
                    'h1',
                    'title',
                    'description',
                    [
                        'attribute' => 'preview_image',
                        'value' => function($model) {
                            if (
                                $model->preview_image &&
                                ($img = $model->getImage('preview_image'))
                            ) {
                                return Html::img($img, ['style' => 'max-width: 150px;']);
                            }
                        },
                        'format' => 'raw'
                    ],
                    'preview_text:html',
                    [
                        'attribute' => 'detail_image',
                        'value' => function($model) {
                            if (
                                $model->detail_image &&
                                ($img = $model->getImage('detail_image'))
                            ) {
                                return Html::img($img, ['style' => 'max-width: 150px;']);
                            }
                        },
                        'format' => 'raw'
                    ],
                    'detail_text:html',
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'value' => function($model) {
                            $user = $model->createdBy;
                            if ($user) {
                                return Html::a($user->username, ['/user-management/user/view', 'id' => $user->id], ['data-pjax' => 0]);
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function($model) {
                            $user = $model->updatedBy;
                            if ($user) {
                                return Html::a($user->username, ['/user-management/user/view', 'id' => $user->id], ['data-pjax' => 0]);
                            }
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>