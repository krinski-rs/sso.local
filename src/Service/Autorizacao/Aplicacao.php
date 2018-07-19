<?php
namespace App\Service\Autorizacao;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Autorizacao\Aplicacao\Create;
use Psr\Log\LoggerInterface;

class Aplicacao
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
    
//     public function get(int $idPessoa)
//     {
//         try {
//             $objPessoasPessoaListing = new Listing($this->objEntityManager);
//             return $objPessoasPessoaListing->get($idPessoa);
//         } catch (\RuntimeException $e){
//             throw $e;
//         } catch (\Exception $e){
//             throw $e;
//         }
//     }
    
//     public function list(Request $objRequest)
//     {
//         try {
//             $objPessoasPessoaListing = new Listing($this->objEntityManager);
//             return $objPessoasPessoaListing->list($objRequest);
//         } catch (\RuntimeException $e){
//             throw $e;
//         } catch (\Exception $e){
//             throw $e;
//         }
//     }
}

