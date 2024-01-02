<?php

use chieff\modules\Cms\CmsModule;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\Cms\models\Page $model
 */

$this->title = CmsModule::t('back', 'Page creation');
$this->params['breadcrumbs'][] = ['label' => CmsModule::t('back', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h2 class="lte-hide-title"><?= $this->title ?></h2>
<div class="panel panel-default">
    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>