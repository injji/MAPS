<?php

namespace App\Menu;

class Menu {
    public $parent = null;
    public $sub = [];
    protected $permission = false;
    protected $icon = null;
    protected $name = null;
    protected $level = [];
    protected $display = true;
    protected $target = '_self';

    /**
     * 상위 메뉴 설정
     *
     * @param App\Menu\Menu $parent
     * @return void
     */
    public function parent($parent)
    {
        $this->parent = MenuRepository::findMenu($parent);
    }

    /**
     * set display
     *
     * @param boolean $display
     * @return void
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    }

    /**
     * set target
     *
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * get target
     *
     * @param string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * set icon
     *
     * @param string
     * @return void
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * return icon
     *
     * @return void
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * set name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set defult level
     *
     * @param array $level
     * @return void
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * return defult level
     *
     * @return array
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * set defult permission
     *
     * @param bool $permission
     * @return void
     */
    public function setPermission($val)
    {
        $this->permission = $val;
    }

    /**
     * return defult id
     *
     * @return array
     */
    public function getPermission()
    {
        return $this->permission;
    }    

    /**
     * array 로 변환
     *
     * @return array
     */
    public function toArray()
    {
        $sub = [];
        foreach ($this->sub as $row) {
            $sub[$row->getName()] = $row->toArray();
        }
        return [
            'permission' => $this->permission,
            'display' => $this->display,
            'level' => $this->level,
            'sub' => $sub,
            'icon' => $this->icon,
            'target' => $this->target,
        ];
    }

    /**
     * 서브메뉴 추가
     *
     * @param App\Menu\Menu $menu
     * @return void
     */
    public function addSub($menu)
    {
        $this->sub[] = $menu;
    }

    /**
     * return 최상위 메뉴부터 순차적으로 return
     *
     * @return array[App\Menu\Menu]
     */
    public function getParents()
    {
        $result = [$this];
        $menu = $this;
        while ($menu = $menu->parent) {
            $result[] = $menu;
        }
        return array_reverse($result);
    }
}
