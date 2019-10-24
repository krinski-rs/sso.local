<?php
namespace App\EventListener;
// use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
class CorsListener// implements EventSubscriberInterface
{
    private $corsParameters = NULL;
    private $objLogger = NULL;
    
    public function __construct($cors, $objLogger)
    {
        $this->corsParameters = $cors;
        $this->objLogger = $objLogger;
    }
    
    public function onKernelRequest(GetResponseEvent $objGetResponseEvent)
    {
        /*
         * Não faça nada se não for o MASTER_REQUEST
         */
        if (HttpKernelInterface::MASTER_REQUEST !== $objGetResponseEvent->getRequestType()) {
            return;
        }
        $objRequest = $objGetResponseEvent->getRequest();
        $this->objLogger->error('CORS', [$objRequest->headers->all(), $objRequest->server->get('HTTP_REFERER'), array_search($objRequest->headers->get('origin'), $this->corsParameters['allowed_origin'])]);
        $method  = $objRequest->getRealMethod();
        
        $allowed_origin = array_search($objRequest->headers->get('origin'), $this->corsParameters['allowed_origin']);
        $allowed_origin = (($allowed_origin === FALSE) ? array_search($objRequest->getClientIp(), $this->corsParameters['allowed_origin']) : $allowed_origin);
        
        if($allowed_origin === FALSE){
            $objResponse = new Response();
            $objResponse->headers->set('status', 403);
            $objGetResponseEvent->setResponse($objResponse);
            return $objGetResponseEvent;
        }
        
        if ('OPTIONS' === strtoupper($method)) {
            $objResponse = new Response();
            $objResponse->headers->set('Access-Control-Allow-Origin', trim($this->corsParameters['allowed_origin'][$allowed_origin]));
            $objResponse->headers->set('Access-Control-Allow-Credentials', 'true');
            $objResponse->headers->set('Access-Control-Allow-Methods', 'POST,GET,PUT,DELETE,PATCH,OPTIONS');
            $objResponse->headers->set('Access-Control-Allow-Headers', implode(",", $this->corsParameters['allowed_headers']));
            $objResponse->headers->set('Access-Control-Max-Age', 3600);
            $objGetResponseEvent->setResponse($objResponse);
            return ;
        }
        
        $this->objLogger->error('CORS', ['content-type'=>$objRequest->headers->get('content-type')]);
        if ($objRequest->headers->get('content-type') == 'application/json') {
            $this->objLogger->error('CORS', ['content'=>$objGetResponseEvent->getRequest()->getContent()]);
            $content = $objGetResponseEvent->getRequest()->getContent();
            if($content){
                $data = json_decode($content, true);
                if(count($data)){
                    reset($data);
                    while($dado = current($data)){
                        $objRequest->attributes->set(key($data), $dado);
                        next($data);
                    }
                }
            }
        }
    }
    
    public function onKernelResponse(FilterResponseEvent $objFilterResponseEvent)
    {
        $objRequest = $objFilterResponseEvent->getRequest();
        /*
         * Execute o CORS aqui para garantir que o domínio esteja no sistema
         */
        if (HttpKernelInterface::MASTER_REQUEST !== $objFilterResponseEvent->getRequestType()) {
            return;
        }
        $allowed_origin = array_search($objRequest->headers->get('origin'), $this->corsParameters['allowed_origin']);
        $allowed_origin = (($allowed_origin === FALSE) ? array_search($objRequest->getClientIp(), $this->corsParameters['allowed_origin']) : $allowed_origin);
        if($allowed_origin === FALSE){
            $objResponse = $objFilterResponseEvent->getResponse();
            return $objResponse->headers->set('status', 403);
        }
        $objResponse = $objFilterResponseEvent->getResponse();
        $objResponse->headers->set('Access-Control-Allow-Origin', trim($this->corsParameters['allowed_origin'][$allowed_origin]));
        $objResponse->headers->set('Access-Control-Allow-Credentials', 'true');
        $objResponse->headers->set('Access-Control-Allow-Methods', 'POST,GET,PUT,DELETE,PATCH,OPTIONS');
        $objResponse->headers->set('Access-Control-Allow-Headers', implode(",", $this->corsParameters['allowed_headers']));
    }
}