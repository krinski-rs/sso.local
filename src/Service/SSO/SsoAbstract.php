<?php
namespace App\Service\SSO;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

abstract class SsoAbstract implements SsoInterface {
    
    protected $objContainer = NULL;
    protected $objSession = NULL;
    protected $objLogger = NULL;
    private $objRequestStack = NULL;
    
    public function __construct(Container $objContainer, LoggerInterface $objLoggerInterface){
        $this->objContainer = $objContainer;
        $this->objLogger = $objLoggerInterface;
    }
    
    public function isLoggedIn():bool
    {
        $this->objLogger->info('sessionData', [$this->getUserSession()->all()]);
        return !empty($this->getUserData());
    }
    
    public function invalidate():bool
    {
        if($this->isLoggedIn() && $this->objSession instanceof Session)
        {
            if(!$this->objSession->isStarted()){
                $this->objSession->start();
            }
            $this->objSession->clear();
            $this->objSession->invalidate();
            $this->objSession = NULL;
        }
        return true;
    }
    
    public function getUserSession():Session
    {
        if (!($this->objSession instanceof Session)) {
            $this->objSession = $this->objContainer->get('session');
        }
        return $this->objSession;
    }
    
    public function getUserData():array
    {
        return (is_array($this->getUserSession()->get('userData')) ? $this->getUserSession()->get('userData') : array());
    }
    
    public function setUserData(array $userData):SsoInterface
    {
        $this->getUserSession();
        $userData['AccessToken'] = $this->objSession->getId();
        $this->objSession->set('userData', $userData);
        return $this;
    }
    
    public function getRequest():Request
    {
        return $this->objRequestStack->getMasterRequest();
    }
    
    public function setRequestStack(RequestStack $objRequestStack):SsoInterface
    {
        $this->objRequestStack = $objRequestStack;
        return $this;
    }
    
    public function getCredentials():array
    {
        if($this->isLoggedIn()){
            return $this->getUserData();
        }
        return array();
    }
}