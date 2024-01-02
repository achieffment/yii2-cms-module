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

?>