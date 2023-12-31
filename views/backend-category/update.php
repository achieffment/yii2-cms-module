<?php

use chieff\modules\Cms\CmsModule;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\Cms\models\Category $model
 */

$this->title = CmsModule::t('back', 'Editing category: ') . ' ' . $model->name;
foreach ($backPath as $path) {
    $this->params['breadcrumbs'][] = $path;
}
$this->params['breadcrumbs'][] = ['label' => $model->name];

?>
<h2 class="lte-hide-title"><?= $this->title ?></h2>
<div class="panel panel-default">
    <p>
        <?= $backLink ?>
    </p>
    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>