<?php

use chieff\modules\Cms\CmsModule;

use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var chieff\modules\Cms\models\Page $model
 */

$this->title = CmsModule::t('back', 'Category creation');
foreach ($backPath as $path) {
    $this->params['breadcrumbs'][] = $path;
}

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