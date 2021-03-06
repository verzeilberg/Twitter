<?php
namespace Twitter\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Twitter\Controller\IndexController;
use Twitter\Service\twitterOathService;
use Twitter\Service\twitterService;

class IndexControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {    
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $viewHelperManager = $container->get('ViewHelperManager');
        $config = $container->get('config');
        $twitterService = new twitterService($config);
        $twitterOathService = new twitterOathService($config, $twitterService);
        return new IndexController($entityManager, $viewHelperManager, $twitterOathService);
    }

}