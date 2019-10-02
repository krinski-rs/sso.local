<?php
namespace App\Service\SSO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use \Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use App\Entity\Authorization\Applications;
use App\Repository\Authorization\ApplicationsRepository;

class SsoApi extends SsoAbstract
{
    private $usernamePassword = [];
    
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
            
            $objApplicationsRepository  = $objEntityManager->getRepository("AppEntity:Authorization\Applications");
            if(!($objApplicationsRepository instanceof ApplicationsRepository)){
                throw new \RuntimeException("Repository for 'Authorization\Application' not found.");
            }
            
            $objApplications = $objApplicationsRepository->findByLogin($this->usernamePassword[0], $this->usernamePassword[1]);
            if(!($objApplications instanceof Applications)){
                throw new BadCredentialsException("Unauthorized key");
            }

            $userData = [
                'id'            => $objApplications->getId(),
                'recordingDate' => $objApplications->getRecordingDate()->format('Y-m-d H:i:s'),
                'name'          => $objApplications->getName(),
                'acronym'       => $objApplications->getAcronym(),
                'login'         => date("Y-m-d H:i:s")
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
            $authorization = $objRequest->headers->get('Authorization', NULL);
            if(!$authorization){
                throw new \RuntimeException("Parameter 'Authorization Header' of value '' violated a constraint 'This value should not be blank.'");
            }
            $this->usernamePassword = base64_decode(trim(substr($authorization, 5)));
            $this->usernamePassword = explode(':', $this->usernamePassword);
            if(count($this->usernamePassword) !== 2){
                $this->usernamePassword = [];
                throw new \RuntimeException("Username and Password are required.");
            }
            if(!trim($this->usernamePassword[0]) || !trim($this->usernamePassword[1])){
                $this->usernamePassword = [];
                throw new \RuntimeException("Username and Password are required.");
            }
            return TRUE;
        } catch (\RuntimeException $ex){
            throw $ex;
        } catch (\Exception $ex){
            throw $ex;
        }
    }
}
