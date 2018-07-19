<?php
namespace App\Service\SSO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use \Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use App\Entity\Autorizacao\Aplicacao;
use App\Entity\Autorizacao\Usuario;

class SsoUser extends SsoAbstract
{
    public function login(Request $objRequest):array
    {
        try {
            if($this->isLoggedIn()){
                $this->invalidate();
            }
            $this->validate($objRequest);
            
            $objRegistry = $this->objContainer->get('doctrine');
            if(!($objRegistry instanceof Registry)){
                throw new \RuntimeException("No database connection.");
            }
            
            $objEntityManager = $objRegistry->getManager('default');
            if(!($objEntityManager instanceof EntityManager)){
                throw new \RuntimeException("No database.");
            }
            $objAutorizacaoAplicacaoRepository  = $objEntityManager->getRepository("App:Autorizacao\Aplicacao");
            
            if(!($objAutorizacaoAplicacaoRepository instanceof \Doctrine\ORM\EntityRepository)){
                throw new \RuntimeException("Repository for 'Autorizacao\Aplicacao' not found. ");
            }
            
            $apiKey = $objRequest->headers->get('ApiKey', NULL);
            $objAplicacao = $objAutorizacaoAplicacaoRepository->findOneBy(['apiKey' => $apiKey, 'ativo' => true]);
            if(!($objAplicacao instanceof Aplicacao)){
                throw new \RuntimeException("Unauthorized key");
            }
            
            $objAutorizacaoUsuarioRepository    = $objEntityManager->getRepository("App:Autorizacao\Usuario");
            if(!($objAutorizacaoUsuarioRepository instanceof \Doctrine\ORM\EntityRepository)){
                throw new \RuntimeException("Repository for 'Autorizacao\Usuario' not found. ");
            }
            
            $username = trim($objRequest->get('username', NULL));
            $password = trim($objRequest->get('password', NULL));
            
            $objAutorizacaoUsuario = $objAutorizacaoUsuarioRepository->findOneBy(array('username'=>$username, 'ativo'=>true));
            if(!($objAutorizacaoUsuario instanceof Usuario)){
                throw new BadCredentialsException("Usuario not found.");
            }
            
            $objUserPasswordEncoder = new BCryptPasswordEncoder(12);
            if (!$objUserPasswordEncoder->isPasswordValid($objAutorizacaoUsuario->getPassword(), trim($password), $objAutorizacaoUsuario->getSalt())) {
                throw new BadCredentialsException("Invalid credentials");
            }
            
            $userData = [
                'id'            => $objAutorizacaoUsuario->getId(),
                'dataCadastro'  => $objAutorizacaoUsuario->getDataCadastro()->format('Y-m-d H:i:s'),
                'nome'          => $objAutorizacaoUsuario->getNome(),
                'username'      => $objAutorizacaoUsuario->getUsername()
            ];
            
            return $this->setUserData($userData)->getUserData();
        } catch (BadCredentialsException $ex){
            throw $ex;
        } catch (\Exception $ex){
            throw $ex;
        }
    }
    
    public function validate(Request $objRequest):bool
    {
        try {
            $username   = $objRequest->get('username', NULL);
            $password   = $objRequest->get('password', NULL);
            $apiKey     = $objRequest->headers->get('ApiKey', NULL);
            
            if(!$username){
                throw new \RuntimeException("Parameter 'Username' of value '' violated a constraint 'This value should not be blank.'");
            }
            
            if(!$password){
                throw new \RuntimeException("Parameter 'Password' of value '' violated a constraint 'This value should not be blank.'");
            }
            
            if(!$apiKey){
                throw new \RuntimeException("Parameter 'ApiKey' of value '' violated a constraint 'This value should not be blank.'");
            }
        } catch (\RuntimeException $ex){
            throw $ex;
        } catch (\Exception $ex){
            throw $ex;
        }
        return true;
    }
}
