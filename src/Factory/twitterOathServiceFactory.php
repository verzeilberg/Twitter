<?php
namespace Twitter\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Twitter\Service\twitterOathService;

class twitterOathServiceFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {    
        $config = $container->get('config');
        
        die('test');
        
        return new twitterOathService($config);
    }

}