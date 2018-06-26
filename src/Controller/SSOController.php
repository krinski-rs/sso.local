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
            $objAuthStrategy = $this->get('auth_strategy');
            if(!($objAuthStrategy instanceof AuthStrategy)){
                throw new \RuntimeException('Class "App\Service\Strategy\AuthStrategy" not found.');
            }
            
            $objSsoInterface = $objAuthStrategy->getSSO($objRequest);
            $objSsoInterface->login($objRequest);
            
            return new JsonResponse($objSsoInterface->getUserData(), Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function logout(Request $objRequest)
    {
        try {
            $objAuthStrategy = $this->get('auth_strategy');
            if(!($objAuthStrategy instanceof AuthStrategy)){
                throw new \RuntimeException('Class "App\Service\Strategy\AuthStrategy" not found.');
            }
            
            $objSsoInterface = $objAuthStrategy->getSSO($objRequest);
            
            return new JsonResponse(['logout' => $objSsoInterface->invalidate()], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function me(Request $objRequest)
    {
        try {
            $objAuthStrategy = $this->get('auth_strategy');
            if(!($objAuthStrategy instanceof AuthStrategy)){
                throw new \RuntimeException('Class "App\Service\Strategy\AuthStrategy" not found.');
            }
            
            $objSsoInterface = $objAuthStrategy->getSSO($objRequest);
            $arrayUser = $objSsoInterface->getCredentials($objRequest);
            
            if(empty($arrayUser)){
                throw new \RuntimeException('user is not logged in.');
            }
            return new JsonResponse($arrayUser, Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function form(Request $objRequest)
    {
        try {
            return $this->render('auth\login.html.twig', ['error' => NULL, 'last_username' => NULL]);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_PRECONDITION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['mensagem'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

