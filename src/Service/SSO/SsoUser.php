<?php
namespace App\Service\SSO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SsoUser extends SsoAbstract
{
    public function login(Request $objRequest):array
    {
        try {
            if($this->isLoggedIn()){
                $this->invalidate();
            }
            $this->validate($objRequest);
            
            $objAutorizacaoUsuarioRepository = $this->objContainer->get('doctrine')->getRepository("App:Autorizacao\Usuario");
            
            if(!($objAutorizacaoUsuarioRepository instanceof \Doctrine\ORM\EntityRepository)){
                throw new \RuntimeException("Repository for 'Autorizacao\Usuario' not found. ");
            }
            
            echo $username = $objRequest->get('username', NULL);
            echo $password = $objRequest->get('password', NULL);
            
            $objAutorizacaoUsuario = $objAutorizacaoUsuarioRepository->findOneBy(array('username'=>$username, 'ativo'=>true));
            if(!($objAutorizacaoUsuario instanceof \App\Entity\Autorizacao\Usuario)){
                throw new \RuntimeException("Usuario not found. ");
            }
            
            $objUserPasswordEncoder = new BCryptPasswordEncoder(12);
            if (!$objUserPasswordEncoder->isPasswordValid($objAutorizacaoUsuario->getPassword(), trim($password), $objAutorizacaoUsuario->getSalt())) {
                throw new BadCredentialsException("Invalid credentials");
            }
            
            $userData = [
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
            $username = $objRequest->get('username', NULL);
            $password = $objRequest->get('password', NULL);
            
            if(!$username){
                throw new \RuntimeException("Parameter 'Username' of value '' violated a constraint 'This value should not be blank.'");
            }
            
            if(!$password){
                throw new \RuntimeException("Parameter 'Password' of value '' violated a constraint 'This value should not be blank.'");
            }
            
        } catch (\RuntimeException $ex){
            throw $ex;
        } catch (\Exception $ex){
            throw $ex;
        }
        return true;
    }
}
