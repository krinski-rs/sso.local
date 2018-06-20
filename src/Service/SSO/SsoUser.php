<?php
namespace App\Service\SSO;

use Symfony\Component\HttpFoundation\Request;

class SsoUser extends SsoAbstract
{
    public function login(Request $objRequest):array
    {
        try {
            if($this->isLoggedIn()){
                $this->invalidate();
            }
            $this->validate($objRequest);
            return $this->setUserData(['a'=>1,'b'=>2])
            ->getUserData();
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
