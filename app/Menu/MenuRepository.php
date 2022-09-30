<?php

namespace App\Menu;

class MenuRepository {

    protected static $menu = [];
    
    public static function append($name, $makeMenu = null)
    {
        $menu = (new Menu);
        $menu->setName($name);
        if ($makeMenu != null) {
            $makeMenu($menu);
        }
        if ($menu->parent != null) {
            $menu->parent->addSub($menu);
            self::$menu[$menu->parent->getName()] = $menu->parent;
        }
        self::$menu[$name] = $menu;
    }
    public static function findMenu($name)
    {
        return self::$menu[$name];
    }
    public static function getMenu()
    {
        return self::$menu;
    }
    public static function toArray()
    {
        $result = [];
        foreach (self::$menu as $name => $row) {
            if ($row->parent == null) {
                $result[$name] = $row->toArray();
            }
        }
        return $result;
    }
}
