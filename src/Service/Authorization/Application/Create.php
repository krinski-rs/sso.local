<?php
namespace App\Service\Authorization\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Doctrine\ORM\EntityManager;
use App\Entity\Authorization\Applications;

class Create
{
    private $objEntityManager   = NULL;
    private $objApplication     = NULL;

    public function __construct(EntityManager $objEntityManager)
    {
        $this->objEntityManager = $objEntityManager;
    }

    public function create(Request $objRequest)
    {
        try {
            $this->validate($objRequest);
            $objDateTime = new \DateTime();

            $name = trim($objRequest->get('name', NULL));
            $acronym = trim($objRequest->get('acronym', NULL));
            $host = trim($objRequest->get('host', NULL));
            $expirationDate = trim($objRequest->get('expirationDate', NULL));

            $timestamp = $objDateTime->getTimestamp();
            $apiKey = hash("sha256", $timestamp.$name);
            $clientId = hash("sha256", $timestamp.uniqid(mt_rand()));

            $this->objApplication = new Applications();
            $this->objApplication->setAcronym($acronym);
            $this->objApplication->setApiKey($apiKey);
            $this->objApplication->setClientId($clientId);
            $this->objApplication->setExpirationDate(($expirationDate ? \DateTime::createFromFormat('Y-m-d H:i', $expirationDate) : NULL));
            $this->objApplication->setHost($host);
            $this->objApplication->setIsActive(TRUE);
            $this->objApplication->setIsDeleted(FALSE);
            $this->objApplication->setName($name);
            $this->objApplication->setRecordingDate($objDateTime);
            $this->objApplication->setRemovalDate(NULL);
        } catch (\RuntimeException $e){
            throw $e;
        } catch (\Exception $e){
            throw $e;
        }
    }

    private function validate(Request $objRequest)
    {
        $objNotNull = new Assert\NotNull();
        $objNotNull->message = 'Esse valor não deve ser nulo.';
        $objNotBlank = new Assert\NotBlank();
        $objNotBlank->message = 'Esse valor não deve estar em branco.';

        $objLengthName = new Assert\Length(
            [
                'min' => 2,
                'max' => 100,
                'minMessage' => 'O campo deve ter pelo menos {{ limit }} caracteres.',
                'maxMessage' => 'O campo não pode ser maior do que {{ limit }} caracteres.'
            ]
        );

        $objUrl = new Assert\Url();
        $objDateTime = new Assert\DateTime(["format" => 'Y-m-d H:i']);
        
        $objLengthAcronym = new Assert\Length(
            [
                'min' => 2,
                'max' => 10,
                'minMessage' => 'O campo deve ter pelo menos {{ limit }} caracteres.',
                'maxMessage' => 'O campo não pode ser maior do que {{ limit }} caracteres.'
            ]
        );

        $objRecursiveValidator = Validation::createValidatorBuilder()->getValidator();

        $objCollection = new Assert\Collection(
            [
                'fields' => [
                    'acronym' => new Assert\Required( [
                            $objNotNull,
                            $objNotBlank,
                            $objLengthAcronym
                        ]
                    ),
                    'name' => new Assert\Required( [
                            $objNotNull,
                            $objNotBlank,
                            $objLengthName
                        ]
                    ),
                    'host' => new Assert\Required( [
                            $objNotNull,
                            $objNotBlank,
                            $objUrl
                        ]
                    ),
                    'expirationDate' => new Assert\Optional( [
                            $objDateTime
                        ]
                    )
                ]
            ]
        );
        $data = [
            'acronym'           => trim($objRequest->get('acronym', NULL)),
            'name'              => trim($objRequest->get('name', NULL)),
            'host'              => trim($objRequest->get('host', NULL)),
            'expirationDate'    => trim($objRequest->get('expirationDate', NULL))
        ];

        $objConstraintViolationList = $objRecursiveValidator->validate($data, $objCollection);

        if($objConstraintViolationList->count()){
            $objArrayIterator = $objConstraintViolationList->getIterator();
            $objArrayIterator->rewind();
            $mensagem = '';
            while($objArrayIterator->valid()){
                if($objArrayIterator->key()){
                    $mensagem.= "\n";
                }
                $mensagem.= $objArrayIterator->current()->getPropertyPath().': '.$objArrayIterator->current()->getMessage();
                $objArrayIterator->next();
            }
            throw new \RuntimeException($mensagem);
        }
    }

    public function save()
    {
        $this->objEntityManager->persist($this->objApplication);
        $this->objEntityManager->flush();
        return $this->objApplication;
    }
}

