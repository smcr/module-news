<?php

namespace Ndv\NewsBundle;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class NdvNewsModule
{
    public $oEm;
    public $oContainer;
    
    /**
     * Конструктор
     *
     * @param EntityManager $entityManager
     * @param Container $oContainer
     */
    public function __construct(EntityManager $entityManager, Container $oContainer) {
        $this->oEm = $entityManager;
        $this->oContainer = $oContainer;
    }
    
    public function getName()
    {
        return 'Сайт';
    }
    
    public function getMenu()
    {
        //@todo хардкод
        $menu = $this->oContainer->get('Tools')->getBundleConfig('NewsBundle', 'menu.yml');
        
        return $menu;
    }

}
