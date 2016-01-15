<?php

namespace Ndv\NewsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Ndv\EntityBuilderBundle\Classes\ExtendedController;

class NewsController extends ExtendedController
{
    public function indexAction(Request $request, $theme='default')
    {
        $store = $this->initDataStore(['theme'=>$theme]);

        //проверка кэша
        $cached = $this->getCachedTpl($store);
        if($cached) {
            return $cached;
        }
        //Получение объектов        
        $items = $this->get('Objects')->getEntityObjectsBy(['conditions'=>['code'=>'news']]);

        //рендерим
        return $this->renderTheme('index', ['items'=>$items], $store);
    }
    
    public function listAction($params=[])
    {
        $store = $this->initDataStore($params, ['expirePolicy' => 'entries']);
        
        //проверка кэша
        $cached = $this->getCachedTpl($store);
        if($cached) {
            return $cached;
        }
        
        $store->meta->appendTitle('И новости тоже', ' | ');
        
        //Получение объектов        
        $items = $this->get('Objects')->getEntityObjectsBy(['conditions'=>['code'=>'news']]);
        
        //рендерим
        return $this->renderTheme('list', ['items'=>$items], $store);
    }
}
