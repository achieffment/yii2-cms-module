<?php

use chieff\modules\Cms\CmsModule;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\Cms\models\Page $model
 */

$this->title = $model->title ? $model->title : $model->name;
foreach ($backPath as $path) {
    $this->params['breadcrumbs'][] = $path;
}

if ($model->description) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->description]);
}

$h1 = $model->h1 ? $model->h1 : $model->name;

$date = $model->active_from ? $model->active_from : $model->created_at;
$date = date('d-m-Y', $date);

$imagePreview = $model->preview_image ? $model->getImage('preview_image') : null;
$imageDetail  = $model->detail_image  ? $model->getImage('detail_image')  : null;

?>

<h1><?= $h1 ?></h1>
<p class="badge badge-secondary"><?= $date ?></p>
<? if ($model->detail_text): ?>
    <p><?= $model->detail_text ?></p>
<? endif; ?>
<? if ($imageDetail): ?>
    <img class="img-fluid mt-4 mb-4" src="<?= $imageDetail ?>" loading="lazy">
<? endif; ?>
<div>
    <?= $model->detail_text ?>
</div>