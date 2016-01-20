<?php

namespace Rcms\NewsBundle\Controller;

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

        $em = $this->getDoctrine()->getManager();
        $repoObjects = $em->getRepository('NdvEntityBuilderBundle:Object');

        $entityNews = $this->get('Entities')->getEntityBy(['code'=>'news']);

        $items = $repoObjects->findByEntityIdAndValues($entityNews->getId());

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
        $entityNews = $this->get('Entities')->getEntityBy(['code'=>'news']);

        $em = $this->getDoctrine()->getManager();
        $repoObjects = $em->getRepository('NdvEntityBuilderBundle:Object');
        $items = $repoObjects->findByEntityIdAndValues($entityNews->getId());

        //рендерим
        return $this->renderTheme('list', ['items'=>$items], $store);
    }

    public function showAction($slug, $theme)
    {
        $store = $this->initDataStore(['slug'=>$slug, 'theme'=>$theme], ['expirePolicy' => 'entries']);

        //проверка кэша
        $cached = $this->getCachedTpl($store);
        if($cached) {
            return $cached;
        }

        $store->meta->appendTitle('И новости тоже', ' | ');

        //Получение объектов
        $entityNews = $this->get('Entities')->getEntityBy(['code'=>'news']);

        $em = $this->getDoctrine()->getManager();
        $repoObjects = $em->getRepository('NdvEntityBuilderBundle:Object');
        $item = $repoObjects->findOneByEntityIdAndValues($entityNews->getId(), ['id'=>$slug]);

        //рендерим
        return $this->renderTheme('show', ['item'=>$item], $store);
    }
}
