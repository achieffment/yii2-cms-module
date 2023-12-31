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

<div class="row">

    <div class="col-8">

        <div class="d-flex flex-column">
            <h1><?= $h1 ?></h1>
            <p class="badge badge-secondary" style="width: fit-content"><?= $date ?></p>
            <? if ($imageDetail): ?>
                <img class="img-fluid mb-4" src="<?= $imageDetail ?>" loading="lazy" style="object-fit: cover; object-position: left; max-height: 300px">
            <? endif; ?>
            <? if ($model->detail_text): ?>
                <div>
                    <?= $model->detail_text ?>
                </div>
            <? endif; ?>
        </div>

        <? if ($pages): ?>
            <h2 class="mb-3"><?= CmsModule::t('back', 'Pages') ?></h2>
            <div class="row mb-3">
                <?
                foreach ($pages as $page) {
                    $pageDate = $page->active_from ? $page->active_from : $page->created_at;
                    $pageDate = date('d-m-Y', $pageDate);

                    $pageImagePreview = $page->preview_image ? $page->getImage('preview_image') : null;
                    $pageImageDetail  = $page->detail_image  ? $page->getImage('detail_image')  : null;

                    $page->decodeAttributes(['menutitle', 'name', 'slug', 'preview_text']);

                    $name = $page->menutitle ? $page->menutitle : $page->name;
                    ?>
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="p-3 bg-light rounded h-100">
                            <a class="d-flex flex-column h-100 text-dark" href="/page/<?= $page->slug ?>">
                                <? if ($pageImagePreview || $pageImageDetail): ?>
                                    <img class="img-fluid mb-2" src="<?= $pageImagePreview ? $pageImagePreview : $pageImageDetail ?>" loading="lazy">
                                <? endif; ?>
                                <p class="badge badge-secondary mb-1 mt-auto" style="width: fit-content"><?= $pageDate ?></p>
                                <p class="font-weight-bold mb-0"><?= $name ?></p>
                                <? if ($page->preview_text): ?>
                                    <div class="mt-1"><?= $page->preview_text ?></div>
                                <? endif; ?>
                            </a>
                        </div>
                    </div>
                <? } ?>
            </div>
        <? endif; ?>

    </div>

    <div class="col-4">
        <? if ($subCategories): ?>
            <h2 class="h1"><?= CmsModule::t('back', 'Sections') ?></h2>
            <div class="row">
                <?
                foreach ($subCategories as $subCategory) {
                    $subCategoryDate = $subCategory->active_from ? $subCategory->active_from : $subCategory->created_at;
                    $subCategoryDate = date('d-m-Y', $subCategoryDate);

                    $subCategoryImagePreview = $subCategory->preview_image ? $subCategory->getImage('preview_image') : null;
                    $subCategoryImageDetail  = $subCategory->detail_image  ? $subCategory->getImage('detail_image')  : null;

                    $name = $subCategory->menutitle ? $subCategory->menutitle : $subCategory->name;

                    $pages = $subCategory->getPages(true);
                    ?>
                    <div class="col-12 mb-3">
                        <div class="p-3 bg-light rounded h-100">
                            <a class="d-flex flex-column h-100 text-dark" href="/category/<?= $subCategory->slug ?>">
                                <? if ($subCategoryImagePreview || $subCategoryImageDetail): ?>
                                    <img class="img-fluid mb-2" src="<?= $subCategoryImagePreview ? $subCategoryImagePreview : $subCategoryImageDetail ?>" loading="lazy">
                                <? endif; ?>
                                <p class="badge badge-secondary mb-1 mt-auto" style="width: fit-content"><?= $subCategoryDate ?></p>
                                <p class="font-weight-bold mb-0"><?= $name ?> (<?= $pages ?>)</p>
                                <? if ($subCategory->preview_text): ?>
                                    <div class="mt-1"><?= $subCategory->preview_text ?></div>
                                <? endif; ?>
                            </a>
                        </div>
                    </div>
                <? } ?>
            </div>
        <? endif; ?>

    </div>

</div>