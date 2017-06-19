<?php
namespace Twitter\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Twitter\Controller\IndexController;
use Twitter\Service\twitterOathService;

class IndexControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {    
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $viewHelperManager = $container->get('ViewHelperManager');
        $twitterOathService = new twitterOathService();
        return new IndexController($entityManager, $viewHelperManager, $twitterOathService);
    }

}