<?php
namespace App\Service\Autorizacao\Aplicacao;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Doctrine\ORM\EntityManager;
use App\Entity\Autorizacao\Aplicacao;

class Create
{
    private $objEntityManager   = NULL;
    private $objAplicacao       = NULL;
    
    public function __construct(EntityManager $objEntityManager)
    {
        $this->objEntityManager = $objEntityManager;
    }
    
    public function create(Request $objRequest)
    {
        try {
            $this->validate($objRequest);
            $objDateTime = new \DateTime();
            
            $nome = trim($objRequest->get('nome', NULL));
            
            $timestamp = $objDateTime->getTimestamp();
            $apiKey = sha1($timestamp.$nome);
            
            $this->objAplicacao = new Aplicacao();
            $this->objAplicacao->setApelido(trim($objRequest->get('apelido', NULL)));
            $this->objAplicacao->setApiKey($apiKey);
            $this->objAplicacao->setAtivo(TRUE);
            $this->objAplicacao->setDataCadastro($objDateTime);
            $this->objAplicacao->setNome($nome);
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
        
        $objLengthNome = new Assert\Length(
            [
                'min' => 2,
                'max' => 255,
                'minMessage' => 'O campo deve ter pelo menos {{ limit }} caracteres.',
                'maxMessage' => 'O campo não pode ser maior do que {{ limit }} caracteres.'
            ]
        );
        
        $objLengthApelido = new Assert\Length(
            [
                'min' => 2,
                'max' => 5,
                'minMessage' => 'O campo deve ter pelo menos {{ limit }} caracteres.',
                'maxMessage' => 'O campo não pode ser maior do que {{ limit }} caracteres.'
            ]
        );
        
        $objType = new Assert\Type(
            [
                'type' => 'bool',
                'message' => 'O valor \'{{ value }}\' não é válido \'{{ type }}\'.'
            ]
        );
        
        $objRecursiveValidator = Validation::createValidatorBuilder()->getValidator();
        
        $objCollection = new Assert\Collection(
            [
                'fields' => [
                    'apelido' => new Assert\Required( [
                            $objNotNull,
                            $objNotBlank,
                            $objLengthApelido
                        ]
                    ),
                    'nome' => new Assert\Required( [
                            $objNotNull,
                            $objNotBlank,
                            $objLengthNome
                        ]
                    )
                ]
            ]
        );
        $data = [
            'apelido'   => trim($objRequest->get('apelido', NULL)),
            'nome'      => trim($objRequest->get('nome', NULL))
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
        $this->objEntityManager->persist($this->objAplicacao);
        $this->objEntityManager->flush();
        return $this->objAplicacao;
    }
}

