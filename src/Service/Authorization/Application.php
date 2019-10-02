<?php
namespace App\Service\Authorization;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use App\Service\Authorization\Application\Create;

class Application
{
    private $objEntityManager   = NULL;
    private $objLoggerInterface = NULL;

    public function __construct(Registry $objRegistry, LoggerInterface $objLoggerInterface)
    {
        $this->objEntityManager = $objRegistry->getManager('default');
        $this->objLoggerInterface = $objLoggerInterface;
    }

    public function create(Request $objRequest)
    {
        try {
            $objAutorizacaoAplicacaoCreate = new Create($this->objEntityManager);
            $objAutorizacaoAplicacaoCreate->create($objRequest);
            return $objAutorizacaoAplicacaoCreate->save();
        } catch (\RuntimeException $e){
            throw $e;
        } catch (\Exception $e){
            throw $e;
        }
    }
}
