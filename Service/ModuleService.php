<?php

namespace Modules\NewsBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;

class ModuleService  {

    public $oEm;
    public $oContainer;
    public $oEntities;
    public $oObjects;
    
    //разделитель в пути, win|linux
    public $pathSeparator = '/';

    /**
     * Конструктор
     *
     * @param EntityManager $entityManager
     * @param Container $oContainer
     */
    public function __construct(EntityManager $entityManager, Container $oContainer, $entities, $objects) {
        $this->oEm = $entityManager;
        $this->oContainer = $oContainer;
        $this->oEntities = $entities;
        $this->oObjects = $objects;
        
        //separator
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $this->pathSeparator = '\\';
        }
    }
    
    public function getModules()
    {
        $return = [];
        
        $searchPath = __DIR__.'/../../../../src/';
        $finder = new Finder();
        
        $finder->files()
            ->in($searchPath)
            ->name('*Module.php');
        
        foreach ($finder as $file)
        {   
            $path = $file->getRealpath();
            
            
            $namespace = $this->getNamespaceByPath($path);
            $classname = $this->getClassnameByPath($path);
            
            $class     = $namespace.'\\'.$classname;
            
            //путь разобрали верно, класс найден
            if(class_exists($class))
            {
                $return[] = new $class($this->oEm, $this->oContainer);
            }
        }
        
        return $return;
    }
    
    /**
     * Собираем меню
     * 
     * пока только Top-меню
     * 
     * @return type
     */
    public function getModulesMenu()
    {
        $return = $modulesMenu = [];
        
        //известные типы меню
        $menuTypes = ['top'];
        
        //инит переменных
        foreach($menuTypes as $menuType) {
            $return[$menuType] = [];
        }
        
        //меню из модулей
        foreach($this->getModules() as $module)
        {
            $modulesMenu[] = $module->getMenu($this->oEntities, $this->oObjects);
        }
        
        //поудобнее перекладываем пункты меню
        foreach($menuTypes as $menuType) {
            foreach($modulesMenu as $moduleMenu) {
                if(isset($moduleMenu[$menuType]) && !empty($moduleMenu[$menuType])) {
                    foreach($moduleMenu[$menuType] as $menuKey => $menuItem) {
                        //для путей из конфига сохраняеи название маршрута
                        $menuItem['route'] = $menuKey;
                        $return[$menuType][] = $menuItem;
                    }
                }
            }
        }
        
        //sort
        foreach($menuTypes as $menuType) {
            usort($return[$menuType], function($a, $b){
                if(!isset($a['sort'])) {
                    return -1;
                }
                if(!isset($b['sort'])) {
                    return 1;
                }
                return ((int)$a['sort'] < (int)$b['sort']) ? -1 : 1;
                
            });
        }
        return $return;
    }
    
    /**
     * Собрать неймспайс по пути
     * 
     * @param string $path
     * @return string
     */
    public function getNamespaceByPath($path)
    {
        $namespace = '';
        
        $parts = explode($this->pathSeparator, $path);
                
        $fix = false;
        foreach($parts as $idx => $part)
        {
            if($fix && $idx+1 != count($parts))
            {
                $namespace .= '\\' . $part;
            }
            
            //зашли в src - все, что попадется дальше - в неймпасе
            if('src' == $part)
            {
                $fix = true;
            }
        }
        
        return $namespace;
    }
    
    /**
     * Доставть имя класса из пути
     * 
     * @param string $path
     * @return string
     */
    public function getClassnameByPath($path)
    {
        $classname = '';
        
        $parts = explode($this->pathSeparator, $path);
                
        foreach($parts as $idx => $part)
        {
            if($idx+1 == count($parts))
            {
                $classname = str_replace('.php', '', $part);
            }
        }
        
        return $classname;
    }
}

?>