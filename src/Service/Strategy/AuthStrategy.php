<?php
namespace App\Service\Strategy;

use Symfony\Component\HttpFoundation\Request;
use App\Service\SSO\SsoInterface;
use App\Service\SSO\SsoUser;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Service\SSO\SsoApi;

class AuthStrategy
{
    const GRANT_CLIENT_CREDENTIALS  = 'client_credentials';
    const GRANT_PASSWORD            = 'password';
    private $objContainer           = NULL;
    private $objLoggerInterface     = NULL;
    
    public function __construct(ContainerInterface $objContainer, LoggerInterface $objLoggerInterface)
    {
        $this->objContainer = $objContainer;
        $this->objLoggerInterface = $objLoggerInterface;
    }
    
    public function getSSO(Request $objRequest):SsoInterface
    {
        try {
            
            $objSsoInterface = NULL;
            $grantType = $objRequest->get('grant_type');
            switch ($grantType)
            {
                case self::GRANT_CLIENT_CREDENTIALS:
                    $objSsoInterface = new SsoApi($this->objContainer, $this->objLoggerInterface);
                    break;
                case self::GRANT_PASSWORD:
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

