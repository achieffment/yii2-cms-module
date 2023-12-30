<?php

use chieff\modules\Cms\CmsModule;

use kartik\switchinput\SwitchInput;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;

use chieff\modules\Cms\models\Category;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

use yii\helpers\Url;
use Yii;

/**
 * @var yii\widgets\ActiveForm $form
 * @var chieff\modules\Cms\models\Page $model
 */
?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissable">
        <?= Yii::$app->session->getFlash('error') ?>
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
<?php endif; ?>

<?php $form = ActiveForm::begin([
    'id' => 'role-form',
    'layout' => 'horizontal',
    'validateOnBlur' => false,
]) ?>

    <?= $form->field($model, 'parent_id_field')->dropDownList(Category::getTree($model->id), ['prompt' => 'No Parent (saved as root)']) ?>

    <?= $form->field($model, 'active')->widget(SwitchInput::classname(), []) ?>

    <?= $form->field($model, 'active_from')->widget(DateTimePicker::classname(), [
        'options' => [
            'value' => $model->active_from ? (is_numeric($model->active_from) ? date('d-m-Y H:i', $model->active_from) : $model->active_from) : null,
        ],
        'pluginOptions' => [
            'todayBtn' => true,
            'todayHighlight' => true,
            'autoclose' => true,
            'format' => 'dd-m-yyyy HH:ii',
        ]
    ]); ?>

    <?= $form->field($model, 'active_to')->widget(DateTimePicker::classname(), [
        'options' => [
            'value' => $model->active_to ? (is_numeric($model->active_to) ? date('d-m-Y H:i', $model->active_to) : $model->active_to) : null,
        ],
        'pluginOptions' => [
            'todayBtn' => true,
            'todayHighlight' => true,
            'autoclose' => true,
            'format' => 'dd-m-yyyy HH:ii',
            'startDate' => date('d-m-Y H:i'),
        ]
    ]); ?>

    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'menutitle')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'menuhide')->widget(SwitchInput::classname(), []) ?>
    <?= $form->field($model, 'h1')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

    <?php
        $previewImage = ['showCaption' => false];
        if ($model->preview_image) {
            $previewImage['initialPreview'] = $model->getImage('preview_image');
            $previewImage['initialPreviewAsData'] = true;
            $previewImage['overwriteInitial'] = true;
        }
    ?>
    <?= $form->field($model, 'preview_image_file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*', 'multiple' => false],
        'pluginOptions' => $previewImage
    ]); ?>

    <?= $form->field($model, 'preview_text')->widget(\dosamigos\tinymce\TinyMce::className(), [
        'clientOptions' => [
            'plugins' => [
                'accordion',
                'advlist',
                'anchor',
                'autolink',
                'autoresize',
                'autosave',
                'charmap',
                'code',
                'codesample',
                'directionality',
                'emoticons',
                'fullscreen',
                'help',
                'importcss',
                'insertdatetime',
                'link',
                'lists',
                'nonbreaking',
                'pagebreak',
                'preview',
                'searchreplace',
                'table',
                'visualblocks',
                'visualchars',
                'wordcount'
            ],
            'toolbar' => 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | accordion | bullist numlist | anchor | restoredraft | charmap | code | codesample | ltr rtl | emoticons | fullscreen | help | insertdatetime | link | nonbreaking pagebreak | preview | searchreplace | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | visualblocks | visualchars | wordcount',
            'min_height' => 500
        ]
    ]);?>

    <?php
        $detailImage = ['showCaption' => false];
        if ($model->detail_image) {
            $detailImage['initialPreview'] = $model->getImage('detail_image');
            $detailImage['initialPreviewAsData'] = true;
            $detailImage['overwriteInitial'] = true;
        }
    ?>
    <?= $form->field($model, 'detail_image_file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*', 'multiple' => false],
        'pluginOptions' => $detailImage
    ]); ?>

    <?= $form->field($model, 'detail_text')->widget(\dosamigos\tinymce\TinyMce::className(), [
        'clientOptions' => [
            'plugins' => [
                'accordion',
                'advlist',
                'anchor',
                'autolink',
                'autoresize',
                'autosave',
                'charmap',
                'code',
                'codesample',
                'directionality',
                'emoticons',
                'fullscreen',
                'help',
                'importcss',
                'insertdatetime',
                'link',
                'lists',
                'nonbreaking',
                'pagebreak',
                'preview',
                'searchreplace',
                'table',
                'visualblocks',
                'visualchars',
                'wordcount'
            ],
            'toolbar' => 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | accordion | bullist numlist | anchor | restoredraft | charmap | code | codesample | ltr rtl | emoticons | fullscreen | help | insertdatetime | link | nonbreaking pagebreak | preview | searchreplace | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | visualblocks | visualchars | wordcount',
            'min_height' => 500
        ]
    ]);?>

    <div class="form-group">
        <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton(
                '<i class="fa fa-plus-circle"></i> ' . CmsModule::t('back', 'Create'),
                ['class' => 'btn btn-success']
            ) ?>
        <?php else: ?>
            <?= Html::submitButton(
                '<i class="fa fa-check"></i> ' . CmsModule::t('back', 'Save'),
                ['class' => 'btn btn-primary']
            ) ?>
        <?php endif; ?>
    </div>

<?php ActiveForm::end() ?>