<?php

use chieff\modules\Cms\CmsModule;
use webvimark\modules\UserManagement\UserManagementModule;

use webvimark\extensions\GridPageSize\GridPageSize;
use webvimark\extensions\GridBulkActions\GridBulkActions;
use webvimark\extensions\DateRangePicker\DateRangePicker;

use kartik\switchinput\SwitchInput;

use chieff\modules\Cms\models\Category;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var chieff\modules\Cms\models\search\PageSearch $searchModel
 */

$this->title = CmsModule::t('back', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="cms-backend-index">
        <div class="panel panel-default">
            <div class="row">
                <div class="col-sm-6">
                    <p>
                        <?= Html::a(
                            '<i class="fa fa-plus-circle"></i> ' . CmsModule::t('back', 'Create'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'role-grid-pjax']) ?>
                    <p></p>
                </div>
            </div>
            <div class="panel-body">
                <?php Pjax::begin([
                    'id' => 'cms-backend-grid-pjax',
                ]) ?>
                <?= GridView::widget([
                    'id' => 'cms-backend-grid',
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\bootstrap4\LinkPager',
                        'hideOnSinglePage' => true,
                        'lastPageLabel' => '>>',
                        'firstPageLabel' => '<<',
                    ],
                    'layout' => '
                        {items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}' .
                        GridBulkActions::widget([
                            'gridId' => 'cms-backend-grid',
                            'actions' => [
                                Url::to(['bulk-activate', 'attribute' => 'active']) => GridBulkActions::t('app', 'Activate'),
                                Url::to(['bulk-deactivate', 'attribute' => 'active']) => GridBulkActions::t('app', 'Deactivate'),
                                Url::to(['bulk-delete']) => GridBulkActions::t('app', 'Delete'),
                            ],
                        ]) .
                        '</div></div>',
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width: 10px']],
                        [
                            'attribute' => 'active',
                            'filter' => Html::activeDropDownList(
                                $searchModel,
                                'active',
                                [0 => 'Нет', 1 => 'Да'],
                                ['class' => 'form-control', 'prompt' => 'Все']
                            ),
                            'value' => function($model) {
                                return SwitchInput::widget([
                                    'id' => 'active-' . $model->id,
                                    'name' => 'active-' . $model->id,
                                    'value' => $model->active,
                                    'pluginOptions' => [
                                        'size' => 'mini',
                                    ],
                                    'pluginEvents' => [
                                        'switchChange.bootstrapSwitch' => 'function() {
                                            var _this_id = this.id;
                                            $.ajax({
                                                "url": "' . Url::to(['switch-status', 'id' => $model->id]) . '",
                                                "cache": false,
                                                "success": function(data) {
                                                    if (data != 1) {
                                                        $("#" + _this_id).bootstrapSwitch("toggleState", 1);
                                                    }
                                                } 
                                            })
                                        }',
                                    ]
                                ]);
                            },
                            'format' => 'raw',
                        ],
                        'sort',
                        [
                            'attribute' => 'category_id',
                            'filter' => Html::activeDropDownList(
                                $searchModel,
                                'category_id',
                                Category::getTree(),
                                ['class' => 'form-control', 'prompt' => 'Все']
                            ),
                            'value' => function($model) {
                                $category = $model->category;
                                if ($category) {
                                    return Html::a($category->name, ['/cms/backend-category/view', 'id' => $category->id, 'categoryId' => $category->parentId], ['data-pjax' => 0]);
                                }
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'name',
                            'value' => function($model) {
                                return Html::a($model->name . ' ' . \rmrevin\yii\fontawesome\FAS::icon('edit'), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                            },
                            'format' => 'raw'
                        ],
                        'slug',
                        [
                            'attribute' => 'menuhide',
                            'filter' => Html::activeDropDownList(
                                $searchModel,
                                'menuhide',
                                [0 => 'Нет', 1 => 'Да'],
                                ['class' => 'form-control', 'prompt' => 'Все']
                            ),
                            'value' => function($model) {
                                return SwitchInput::widget([
                                    'id' => 'menuhide-' . $model->id,
                                    'name' => 'menuhide-' . $model->id,
                                    'value' => $model->menuhide,
                                    'pluginOptions' => [
                                        'size' => 'mini',
                                    ],
                                    'pluginEvents' => [
                                        'switchChange.bootstrapSwitch' => 'function() {
                                            var _this_id = this.id;
                                            $.ajax({
                                                "url": "' . Url::to(['switch-menu-status', 'id' => $model->id]) . '",
                                                "cache": false,
                                                "success": function(data) {
                                                    if (data != 1) {
                                                        $("#" + _this_id).bootstrapSwitch("toggleState", 1);
                                                    }
                                                } 
                                            })
                                        }',
                                    ]
                                ]);
                            },
                            'format' => 'raw',
                        ],
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
                        [
                            'attribute' => 'preview_text',
                            'value' => function($model) {
                                if ($model->preview_text && ($text = strip_tags($model->preview_text))) {
                                    $tag = '<p style="width: 200px; word-wrap: break-word">';
                                    if (mb_strlen($text) > 100) {
                                        $tag .= substr($text, 0, 97) . '...';
                                    } else {
                                        $tag .= $text;
                                    }
                                    $tag .= '</p>';
                                    return $tag;
                                }
                            },
                            'format' => 'raw'
                        ],
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
                        [
                            'attribute' => 'detail_text',
                            'value' => function($model) {
                                if ($model->detail_text && ($text = strip_tags($model->detail_text))) {
                                    $tag = '<p style="width: 200px; word-wrap: break-word">';
                                    if (mb_strlen($text) > 100) {
                                        $tag .= substr($text, 0, 97) . '...';
                                    } else {
                                        $tag .= $text;
                                    }
                                    $tag .= '</p>';
                                    return $tag;
                                }
                            },
                            'format' => 'raw'
                        ],
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
                        ['class' => 'yii\grid\CheckboxColumn'],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'contentOptions' => ['style' => 'width: 70px; text-align: center;'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
<?php DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'created_at',
]) ?>
<?php DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'updated_at',
]) ?>