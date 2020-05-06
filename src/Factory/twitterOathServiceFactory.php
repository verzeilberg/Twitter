<?php
namespace Twitter\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Twitter\Service\twitterOathService;
use Twitter\Service\twitterService;

class twitterOathServiceFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {    
        $config = $container->get('config');
        $twitterService = new twitterService($config);
        return new twitterOathService($config, $twitterService);
    }

}