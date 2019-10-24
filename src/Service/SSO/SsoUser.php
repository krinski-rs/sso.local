<?php
namespace App\Service\SSO;

use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use \Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use App\Repository\Authorization\ApplicationsRepository;
use App\Repository\Authorization\UsersRepository;
use App\Entity\Authorization\Applications;
use App\Entity\Authorization\Users;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;

class SsoUser extends SsoAbstract
{
    
    private $userCredentials    = [];
    private $apiCredentials     = [];
    
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
            $objUsersRepository  = $objEntityManager->getRepository("AppEntity:Authorization\Users");
            if(!($objApplicationsRepository instanceof ApplicationsRepository) || !($objUsersRepository instanceof UsersRepository)){
                throw new \RuntimeException("Repository not found.");
            }
            
            $objApplications = $objApplicationsRepository->findByLogin($this->apiCredentials[0], $this->apiCredentials[1]);
            if(!($objApplications instanceof Applications)){
                throw new BadCredentialsException("Unauthorized key");
            }
            
            $objUsers = $objUsersRepository->findByLogin($this->userCredentials[0]);
            if(!($objUsers instanceof Users)){
                throw new BadCredentialsException("Unauthorized user");
            }
            
            
            $objNativePasswordEncoder = new NativePasswordEncoder(NULL, NULL, 12);
            if(!$objNativePasswordEncoder->isPasswordValid($objUsers->getPassword(), $this->userCredentials[1], $objUsers->getSalt())){
                throw new BadCredentialsException("Unauthorized user");
            }
            
            $userData = [
                'id'            => $objUsers->getId(),
                'recordingDate' => $objUsers->getRecordingDate()->format('Y-m-d H:i:s'),
                'name'          => $objUsers->getName(),
                'username'      => $objUsers->getUsername(),
                'avatar'        => ''
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
            $this->userCredentials = [trim($objRequest->get('username', NULL)), trim($objRequest->get('password', NULL))];
            $this->objLogger->error('SsoUser',$this->userCredentials);
            if(!$authorization){
                throw new \RuntimeException("Parameter 'Authorization Header' of value '' violated a constraint 'This value should not be blank.'");
            }
            
            $this->apiCredentials = base64_decode(trim(substr($authorization, 5)));
            $this->apiCredentials = explode(':', $this->apiCredentials);
            if((count($this->apiCredentials) !== 2) || (!trim($this->apiCredentials[0]) || !trim($this->apiCredentials[1]))){
                throw new BadCredentialsException("Unauthorized key");
            }
            
            if(!$this->userCredentials[0] || !$this->userCredentials[1]){
                throw new BadCredentialsException("Username and Password are required.");
            }
            return TRUE;
        } catch (\RuntimeException $ex){
            throw $ex;
        } catch (\Exception $ex){
            throw $ex;
        }
    }
}
