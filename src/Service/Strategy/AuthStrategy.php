<?php
namespace App\Service\Strategy;

use Symfony\Component\HttpFoundation\Request;
use App\Service\SSO\SsoInterface;
use App\Service\SSO\SsoUser;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class AuthStrategy
{
    const V1 = 'V1';
    private $objContainer = NULL;
    private $objLoggerInterface = NULL;
    
    public function __construct(ContainerInterface $objContainer, LoggerInterface $objLoggerInterface)
    {
        $this->objContainer = $objContainer;
        $this->objLoggerInterface = $objLoggerInterface;
    }
    
    public function getSSO(Request $objRequest):SsoInterface
    {
        try {
            
            $objSsoInterface = NULL;
            $haders = $objRequest->headers;
            switch ($haders->get('AuthVersion', 'V1'))
            {
                case self::V1:
                    $objSsoInterface = new SsoUser($this->objContainer, $this->objLoggerInterface);
                    break;
                default:
                    throw new \RuntimeException('Invalid authentication type.');
            }
            return $objSsoInterface;
        } catch (\RuntimeException $ex) {
            throw $ex;
        } catch (\Exception $ex){
            throw $ex;
        }
    }
}

