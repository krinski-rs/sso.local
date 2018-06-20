<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Strategy\AuthStrategy;

class SSOController extends Controller
{
    public function login(Request $objRequest)
    {
        try {
            
            $authVersion = $objRequest->headers->get('AuthVersion', 'V1');
            
            $objAuthStrategy = $this->get('auth_strategy');
            if(!($objAuthStrategy instanceof AuthStrategy)){
                throw new \RuntimeException('Class "App\Service\Strategy\AuthStrategy" not found.');
            }
            
            $objSsoInterface = $objAuthStrategy->getSSO($objRequest);
            $objSsoInterface->login($objRequest);
            
            $objSsoInterface->getUserData();
            
//             $helper = $this->get('security.authentication_utils');
            return new JsonResponse(['mensagem'=>'login', $objRequest->get('password')], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function logout(Request $objRequest)
    {
        try {
//             $helper = $this->get('security.authentication_utils');
            return new JsonResponse(['mensagem'=>'logout'], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function me(Request $objRequest)
    {
        try {
//             $helper = $this->get('security.authentication_utils');
            return new JsonResponse(['mensagem'=>'me'], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function form(Request $objRequest)
    {
        try {
//             $helper = $this->get('security.authentication_utils');
            return new JsonResponse(['mensagem'=>'form'], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

