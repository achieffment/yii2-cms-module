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

Migrations
```
./yii migrate --migrationPath=vendor/chieff/yii2-cms-module/migrations/
```

If you want delete tables later and didn't migrate another tables, use:
```
./yii migrate/down 4 --migrationPath=vendor/chieff/yii2-cms-module/migrations/
```