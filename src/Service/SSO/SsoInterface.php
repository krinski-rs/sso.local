<?php
namespace App\Service\SSO;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

interface SsoInterface {
    
    public function isLoggedIn():bool;
    
    public function login(Request $objRequest):array;
    
    public function invalidate():bool;
    
    public function getUserSession():Session;
    
    public function getUserData():array;
    
    public function setUserData(array $userData):SsoInterface;
    
    public function validate(Request $objRequest):bool;
    
    public function getRequest():Request;
    
    public function setRequestStack(RequestStack $objRequestStack):SsoInterface;
        
    public function getCredentials():array;
}