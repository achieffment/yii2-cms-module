<?php

use chieff\modules\Cms\CmsModule;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\Cms\models\Category $model
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

<? if ($pages): ?>
    <div class="row mb-3">
        <h2 class="col-12 mb-3"><?= CmsModule::t('back', 'Pages') ?></h2>
        <?
        foreach ($pages as $page) {
            $pageDate = $page->active_from ? $page->active_from : $page->created_at;
            $pageDate = date('d-m-Y', $pageDate);

            $pageImagePreview = $page->preview_image ? $page->getImage('preview_image') : null;
            $pageImageDetail  = $page->detail_image  ? $page->getImage('detail_image')  : null;

            $name = $page->menutitle ? $page->menutitle : $page->name;
            ?>
            <div class="col-3">
                <div class="p-3 d-flex flex-column bg-light rounded h-100">
                    <a class="h-100" href="/page/<?= $page->slug ?>">
                        <? if ($pageImagePreview || $pageImageDetail): ?>
                            <img class="img-fluid" src="<?= $pageImagePreview ? $subCategoryImagePreview : $pageImageDetail ?>" loading="lazy">
                        <? endif; ?>
                        <p class="badge badge-secondary mb-1" style="width: fit-content"><?= $pageDate ?></p>
                        <p class="mb-0"><?= $name ?></p>
                        <? if ($page->preview_text): ?>
                            <div class="mt-1"><?= $page->preview_text ?></div>
                        <? endif; ?>
                    </a>
                </div>
            </div>
        <? } ?>
    </div>
<? endif; ?>

<? if ($subCategories): ?>
    <div class="row">
        <h2 class="col-12 mb-3"><?= CmsModule::t('back', 'Sections') ?></h2>
        <?
        foreach ($subCategories as $subCategory) {
            $subCategoryDate = $subCategory->active_from ? $subCategory->active_from : $subCategory->created_at;
            $subCategoryDate = date('d-m-Y', $subCategoryDate);

            $subCategoryImagePreview = $subCategory->preview_image ? $subCategory->getImage('preview_image') : null;
            $subCategoryImageDetail  = $subCategory->detail_image  ? $subCategory->getImage('detail_image')  : null;

            $name = $subCategory->menutitle ? $subCategory->menutitle : $subCategory->name;

            $pages = $subCategory->pages;
            $pagesCount = 0;
            if ($pages) {
                $pagesCount = count($pages);
            }
            ?>
            <div class="col-3">
                <div class="p-3 d-flex flex-column bg-light rounded h-100">
                    <a class="h-100" href="/category/<?= $subCategory->slug ?>">
                        <? if ($subCategoryImagePreview || $subCategoryImageDetail): ?>
                            <img class="img-fluid" src="<?= $subCategoryImagePreview ? $subCategoryImagePreview : $subCategoryImageDetail ?>" loading="lazy">
                        <? endif; ?>
                        <p class="badge badge-secondary mb-1" style="width: fit-content"><?= $subCategoryDate ?></p>
                        <p class="mb-0"><?= $name ?> (<?= $pagesCount ?>)</p>
                        <? if ($subCategory->preview_text): ?>
                            <div class="mt-1"><?= $subCategory->preview_text ?></div>
                        <? endif; ?>
                    </a>
                </div>
            </div>
        <? } ?>
    </div>
<? endif; ?>