<?php

namespace chieff\modules\Cms;

use chieff\modules\Cms\models\Category;
use chieff\modules\Cms\models\Page;

use yii\helpers\ArrayHelper;

use Yii;

class CmsModule extends \yii\base\Module
{

    public $imagesPath = '@webroot/uploads/pages/';

    public $imagesPathRelative = '/backend/uploads/pages/';

    public $imagesCategoriesPath = '@webroot/uploads/categories/';

    public $imagesCategoriesPathRelative = '/backend/uploads/categories/';

    public $page_table = '{{%page}}';
    
    public $category_table = '{{%category}}';

    /**
     * If set true, images will be encoded
     *
     * @var bool
     */
    public $imageEncode = false;

    /**
     * If set true, data will be encoded
     *
     * @var array
     */
    public $dataEncode = false;

    /**
     * If set true, migration will create special table
     *
     * @var array
     */
    public $dataEncodeMigration = true;

    /**
     * Secure passphrase for encoding
     *
     * @var string
     */
    public $passphrase = '';

    public $controllerNamespace = 'chieff\modules\Cms\controllers';

    /**
     * @p
     */
    public function init()
    {
        parent::init();
    }

    /**
     * I18N helper
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (!isset(Yii::$app->i18n->translations['modules/cms/*'])) {
            Yii::$app->i18n->translations['modules/cms/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'ru',
                'basePath' => '@vendor/chieff/yii2-cms-module/messages',
                'fileMap' => [
                    'modules/cms/back' => 'back.php',
                    'modules/cms/front' => 'front.php',
                ],
            ];
        }
        return Yii::t('modules/cms/' . $category, $message, $params, $language);
    }

}