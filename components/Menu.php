<?php

namespace chieff\modules\Cms\components;

use chieff\modules\Cms\CmsModule;
use chieff\modules\Cms\models\Category;

use yii\base\Component;

class Menu extends Component {

    public static function buildBackendMenu()
    {
        return [
            [
                'label' => CmsModule::t('back', 'CMS'),
                'url' => ['/cms/backend/'],
                'items' => [
                    [
                        'label' => CmsModule::t('back', 'Pages'),
                        'url' => ['/cms/backend-page/'],
                    ],
                    [
                        'label' => CmsModule::t('back', 'Categories'),
                        'url' => ['/cms/backend-category/']
                    ]
                ]
            ]
        ];
    }

    public static function buildFrontendMenu($depth = 1)
    {
        if ($depth <= 0)
            return [];
        $menu = [];
        // Get first level
        $result = Category::find()
            ->select(['id', 'name', 'slug', 'tree', 'lft', 'rgt', 'depth'])
            ->where([
                'active' => Category::STATUS_ACTIVE,
                'menuhide' => Category::MENU_STATUS_ACTIVE,
                'depth' => 0
            ])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
        if ($result) {
            foreach ($result as $item) {
                $menuItem = [
                    'item' => $item,
                    'items' => [],
                    'label' => $item->name,
                    'url' => ['/' . $item->slug  . '/'],
                ];
                $menu[] = $menuItem;
            }
        } else {
            return $menu;
        }
        // Get another levels
        if ($depth > 1) {
            $menu = self::getTree($menu, 1, $depth);
        }
        // Clean item key in array
        $menu = self::cleanTree($menu, 0, $depth);
        return $menu;
    }

    public static function getTree(&$menuLevel, $curLevel, $maxLevel)
    {
        if (!$menuLevel)
            return [];
        $arMenuLevel = $menuLevel;
        // Get cur children
        foreach ($arMenuLevel as $key => $menuItem) {
            if (!isset($menuItem['item']) || !$menuItem['item']) {
                $arMenuLevel[$key] = [
                    'item' => [],
                    'items' => []
                ];
                continue;
            }
            $item = $menuItem['item'];
            $children = Category::find()
                ->select(['id', 'name', 'slug', 'tree', 'lft', 'rgt', 'depth'])
                ->where([
                    'active' => Category::STATUS_ACTIVE,
                    'menuhide' => Category::MENU_STATUS_ACTIVE,
                    'tree' => $item->tree,
                    'depth' => $curLevel
                ])
                ->andWhere(['>=', 'lft', $item->lft])
                ->andWhere(['<=', 'rgt', $item->rgt])
                ->orderBy(['sort' => SORT_ASC])
                ->all();
            if ($children) {
                $arMenuLevel[$key]['items'] = [];
                foreach ($children as $childItem) {
                    $child = [
                        'item' => $childItem,
                        'items' => [],
                        'label' => $childItem->name,
                        'url' => ['/' . $childItem->slug . '/']
                    ];
                    $arMenuLevel[$key]['items'][] = $child;
                }
            } else {
                $arMenuLevel[$key]['items'] = [];
            }
        }
        // Get children of childrens recursively
        if ($curLevel < $maxLevel - 1) {
            $curLevel++;
            foreach ($arMenuLevel as $key => $item) {
                if (isset($item['items']) && $item['items']) {
                    $arMenuLevel[$key]['items'] = self::getTree($item['items'], $curLevel, $maxLevel);
                }
            }
        }
        return $arMenuLevel;
    }

    public static function cleanTree(&$menuLevel, $curLevel, $maxLevel)
    {
        if (!$menuLevel)
            return [];
        $arMenuLevel = $menuLevel;
        // Clean cur level
        foreach ($arMenuLevel as $key => $menuItem) {
            unset($arMenuLevel[$key]['item']);
        }
        // Clean another levels
        if ($curLevel < $maxLevel - 1) {
            $curLevel++;
            foreach ($arMenuLevel as $key => $item) {
                if (isset($item['items']) && $item['items']) {
                    $arMenuLevel[$key]['items'] = self::cleanTree($item['items'], $curLevel, $maxLevel);
                }
            }
        }
        return $arMenuLevel;
    }

}