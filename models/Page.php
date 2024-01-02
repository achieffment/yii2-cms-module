<?php

namespace chieff\modules\Cms\models;

use chieff\modules\Cms\CmsModule;
use webvimark\modules\UserManagement\models\User;

use chieff\modules\Cms\models\Category;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use yii\helpers\Url;
use Yii;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property int|null $active
 * @property int|null $active_from
 * @property int|null $active_to
 * @property int $sort
 * @property string $name
 * @property string $slug
 * @property string|null $menutitle
 * @property int|null $menuhide
 * @property string|null $h1
 * @property string|null $title
 * @property string|null $description
 * @property string|null $preview_image
 * @property string|null $preview_text
 * @property string|null $detail_image
 * @property string|null $detail_text
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $category_id
 */
class Page extends \yii\db\ActiveRecord
{

    const TYPE = 'page';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const MENU_STATUS_ACTIVE = 0;
    const MENU_STATUS_HIDDEN = 1;

    public $preview_image_file;
    public $preview_image_hidden;

    public $detail_image_file;
    public $detail_image_hidden;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return Yii::$app->getModule('cms')->page_table;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'menutitle', 'h1', 'title', 'description', 'preview_text', 'detail_text'], 'trim'],
            [['name', 'slug'], 'required'],
            ['slug', 'unique'],
            ['slug', 'validateSlug'],
            [['active', 'sort', 'menuhide', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            ['category_id', 'integer', 'on' => 'page'],
            ['category_id', 'validateCategoryId', 'on' => 'page'],
            [['active_from', 'active_to'], 'validateDate'],
            [['name', 'menutitle', 'h1', 'title'], 'string', 'max' => 250],
            [['description', 'preview_text'], 'string', 'max' => 500],
            [['slug', 'preview_image', 'detail_image', 'preview_image_hidden', 'detail_image_hidden'], 'string', 'max' => 100],
            [['detail_text'], 'string', 'max' => 5000],
            ['active', 'default', 'value' => 1],
            ['sort', 'default', 'value' => 500],
            ['menuhide', 'default', 'value' => 0],
            [['preview_image_file', 'detail_image_file'], 'file']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'active_from' => 'Active from',
            'active_to' => 'Active to',
            'sort' => 'Sort',
            'name' => 'Name',
            'slug' => 'Slug',
            'menutitle' => 'Menutitle',
            'menuhide' => 'Menuhide',
            'h1' => 'H1',
            'title' => 'Title',
            'description' => 'Description',
            'preview_image' => 'Preview Image',
            'preview_text' => 'Preview Text',
            'detail_image' => 'Detail Image',
            'detail_text' => 'Detail Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'category_id' => 'Category Id',
            'preview_image_file' => 'Preview Image File',
            'preview_image_hidden' => 'Preview Image',
            'detail_image_file' => 'Detail Image File',
            'preview_image_file' => 'Preview Image',
        ];
    }

    public function validateSlug()
    {
        if (!$this->slug || preg_match('/^[0-9A-Za-zА-Яа-яЁё\-_]+$/u', $this->slug) !== 1) {
            $this->addError('slug', 'Incorrect slug');
        }
    }

    public function validateDate($attribute)
    {
        if ($this->$attribute && !is_numeric($this->$attribute)) {
            $date = strtotime($this->$attribute);
            if ($date === false) {
                $this->addError($attribute, 'Incorrect date');
            }
        }
    }

    public function validateCategoryId()
    {
        if ($this->category_id) {
            $category = Category::findOne($this->category_id);
            if ($category === null) {
                $this->addError('category_id', 'Incorrect category');
            }
        }
    }

    public function beforeSave($insert)
    {
        // Active from validating
        if ($this->active_from && !is_numeric($this->active_from)) {
            $date = strtotime($this->active_from);
            if ($date === false) {
                $this->addError('active_from', 'Incorrect date');
                return false;
            }
            $this->active_from = $date;
        }

        // Active to validating
        if ($this->active_to && !is_numeric($this->active_to)) {
            $date = strtotime($this->active_to);
            if ($date === false) {
                $this->addError('active_to', 'Incorrect date');
                return false;
            }
            $this->active_to = $date;
        }

        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        if (!$this->deleteImage('preview_image')) {
            Yii::$app->session->setFlash('error', CmsModule::t('back', 'Can not delete preview image'));
            return false;
        }
        if (!$this->deleteImage('detail_image')) {
            Yii::$app->session->setFlash('error', CmsModule::t('back', 'Can not delete preview image'));
            return false;
        }
        return parent::beforeDelete();
    }

    public function switchStatus()
    {
        if ($this->active == self::STATUS_ACTIVE) {
            $this->active = self::STATUS_INACTIVE;
        } else {
            $this->active = self::STATUS_ACTIVE;
        }
        return $this->save();
    }

    public function switchMenuStatus()
    {
        if ($this->menuhide == self::MENU_STATUS_ACTIVE) {
            $this->menuhide = self::MENU_STATUS_HIDDEN;
        } else {
            $this->menuhide = self::MENU_STATUS_ACTIVE;
        }
        return $this->save();
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getImage($attribute)
    {
        if (static::TYPE == 'page') {
            $path = Yii::$app->getModule('cms')->imagesPathRelative;
        } else if (static::TYPE == 'category') {
            $path = Yii::$app->getModule('cms')->imagesCategoriesPathRelative;
        }
        if (isset($this->$attribute) && $this->$attribute) {
            return $path . $this->$attribute;
        }
        return '';
    }

    public function imageUpload($id, $attribute, $image)
    {
        $path = $this->preparePath();
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                Yii::$app->session->setFlash('error', CmsModule::t('back', 'Can not create directory'));
                return '';
            }
        }
        $attribute_file = $attribute . '_file';
        if (isset($this->$attribute_file)) {
            $this->$attribute_file = UploadedFile::getInstance($this, $attribute_file);
            if ($this->$attribute_file && $this->validate($attribute_file)) {
                if (static::TYPE == 'page') {
                    $file_name = 'page_';
                } else if (static::TYPE == 'category') {
                    $file_name = 'category_';
                }
                $file_name = $file_name . $id . '_' . $attribute .  '.' . $this->$attribute_file->getExtension();
                $file_path = $path . $file_name;
                if ($this->$attribute_file->saveAs($file_path)) {
                    return $file_name;
                } else {
                    Yii::$app->session->setFlash('error', CmsModule::t('back', 'Can not save image'));
                }
            }
        }
        // prevent delete exists image if we won't download or it has errors
        return $image;
    }

    public function imageUploadComplex()
    {
        $prevPreview = $this->preview_image;

        $this->preview_image = $this->imageUpload($this->id, 'preview_image', $this->preview_image);

        if (Yii::$app->session->getFlash('error')) {
            return false;
        }

        // if hidden field is empty and current preview is equal previos, it means that we are deleting image
        if (
            !$this->preview_image_hidden &&
            ($this->preview_image == $prevPreview)
        ) {
            if (!$this->deleteImage('preview_image')) {
                Yii::$app->session->setFlash('error', CmsModule::t('back', 'Can not delete preview image'));
                return false;
            }
            $this->preview_image = '';
        }

        $prevDetail = $this->detail_image;

        $this->detail_image = $this->imageUpload($this->id, 'detail_image', $this->detail_image);

        // if hidden field is empty and current detail is equal previos, it means that we are deleting image
        if (
            !$this->detail_image_hidden &&
            ($this->detail_image == $prevDetail)
        ) {
            if (!$this->deleteImage('detail_image')) {
                Yii::$app->session->setFlash('error', CmsModule::t('back', 'Can not delete detail image'));
                return false;
            }
            $this->detail_image = '';
        }

        return $this->save();
    }

    public function deleteImage($attribute)
    {
        $path = $this->preparePath();
        if (isset($this->$attribute) && $this->$attribute) {
            $path .= $this->$attribute;
            return unlink($path);
        }
        return true;
    }

    public function preparePath()
    {
        if (static::TYPE == 'page') {
            $path = Yii::$app->getModule('cms')->imagesPath;
        } else if (static::TYPE == 'category') {
            $path = Yii::$app->getModule('cms')->imagesCategoriesPath;
        }
        $path = str_replace('\\\\', '/', $path);
        $path = str_replace('\\', '/', $path);
        $path = str_replace('//', '/', $path);
        if (Yii::getAlias($path) == $path) {
            if (substr($path, 0, 1) != '/') {
                $path = '/' . $path;
            }
            if (substr($path, -1, 1)) {
                $path = $path . '/';
            }
            $path = $_SERVER['DOCUMENT_ROOT'] . $path;
        } else {
            $path = Yii::getAlias($path);
            if (substr($path, -1, 1)) {
                $path = $path . '/';
            }
        }
        return $path;
    }

    public function getModelActivity($menuhide = false, $model = null)
    {
        // if giving a model
        if ($model) {
            if ($model->active != self::STATUS_ACTIVE)
                return false;
            if ($menuhide && $model->menuhide != self::MENU_STATUS_ACTIVE)
                return false;
            if (
                $model->active_from && !$model->active_to && time() < $model->active_from
            ) {
                return false;
            } else if (
                !$model->active_from && $model->active_to && time() > $model->active_to
            ) {
                return false;
            } else if (
                ($model->active_from && $model->active_to) &&
                (
                    (time() < $this->active_from) ||
                    (time() > $this->active_to)
                )
            ) {
                return false;
            }
            return true;
        }

        // if not giving a model, check self
        if ($this->active != self::STATUS_ACTIVE)
            return false;
        if ($menuhide && $this->menuhide != self::MENU_STATUS_ACTIVE)
            return false;
        if (
            $this->active_from && !$this->active_to && time() < $this->active_from
        ) {
            return false;
        } else if (
            !$this->active_from && $this->active_to && time() > $this->active_to
        ) {
            return false;
        } else if (
            ($this->active_from && $this->active_to) &&
            (
                (time() < $this->active_from) ||
                (time() > $this->active_to)
            )
        ) {
            return false;
        }
        return true;
    }

    public function getCategoryActivity()
    {
        $category = $this->category;
        if ($category) {
            return $category->activity;
        }
        return true;
    }

    public function getActivity()
    {
        if (!$this->getCategoryActivity())
            return false;
        if (!$this->getModelActivity())
            return false;
        return true;
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

}