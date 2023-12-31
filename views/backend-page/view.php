<?php

use chieff\modules\Cms\CmsModule;

use yii\widgets\DetailView;
use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var yii\web\View $this
 * @var chieff\modules\Cms\models\Page $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => CmsModule::t('back', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <p>
            <?= Html::a(CmsModule::t('back', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a(CmsModule::t('back', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger pull-right',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'active:boolean',
                    'active_from:datetime',
                    'active_to:datetime',
                    'sort',
                    [
                        'attribute' => 'category_id',
                        'value' => function($model) {
                            $category = $model->category;
                            if ($category) {
                                return Html::a($category->name, ['/cms/backend-category/view', 'id' => $category->id, 'categoryId' => $category->parentId], ['data-pjax' => 0]);
                            }
                        },
                        'format' => 'raw'
                    ],
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