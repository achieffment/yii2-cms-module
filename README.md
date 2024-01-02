CMS module for Yii 2
=====

Creating page
```php
$modelClass = 'chieff\modules\Cms\models\Page';
$model = new $modelClass;
$model->scenario = 'page';
```

Routes
```php
'components' => [
    'urlManager' => [
        'rules' => [
            '/category/<slug>' => 'cms/frontend-category/view',
            '/page/<slug>' => 'cms/frontend-page/view'
        ]
    ]
]
```