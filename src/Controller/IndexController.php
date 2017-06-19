<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Twitter\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \viewhelpermanager
     */
    protected $vhm;
    
    protected $tos;

    public function __construct($entityManager, $viewHelperManager, $twitterOathService) {
        $this->em = $entityManager;
        $this->vhm = $viewHelperManager;
        $this->tos = $twitterOathService;
    }

    public function indexAction() {
       $tweets = $this->tos->getTwitterUserTimeline(0,11);
       
       
        return new ViewModel();
    }

}
