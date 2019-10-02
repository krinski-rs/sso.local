<?php

namespace App\Repository\Authorization;

use App\Entity\Authorization\Applications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Applications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Applications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Applications[]    findAll()
 * @method Applications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Applications::class);
    }

    public function findByLogin(string $clientId, string $apiKey)
    {
        $parameters = ['clientId'=>$clientId, 'apiKey'=>$apiKey, 'expirationDate' => date('Y-m-d H:i:s'), 'isDeleted'=>FALSE, 'isActive'=>TRUE];
        $objQueryBuilder = $this->createQueryBuilder('app');
        
        $objEqClientId  = $objQueryBuilder->expr()->eq('app.clientId', ':clientId');
        $objEqApiKey    = $objQueryBuilder->expr()->eq('app.apiKey', ':apiKey');
        $objEqIsDeleted = $objQueryBuilder->expr()->eq('app.isDeleted', ':isDeleted');
        $objEqIsActive  = $objQueryBuilder->expr()->eq('app.isActive', ':isActive');
        
        $objOrx     = $objQueryBuilder->expr()->orX();
        $objIsNull  = $objQueryBuilder->expr()->isNull('app.expirationDate');
        $objGt      = $objQueryBuilder->expr()->gt('app.expirationDate', ':expirationDate');
        $objOrx->add($objIsNull);
        $objOrx->add($objGt);
        
        $objQueryBuilder
            ->andWhere($objEqClientId)
            ->andWhere($objEqApiKey)
            ->andWhere($objOrx)
            ->andWhere($objEqIsDeleted)
            ->andWhere($objEqIsActive)
            ->setParameters($parameters);
        return $objQueryBuilder->getQuery()->getOneOrNullResult();
    }
    
    /*
    public function findOneBySomeField($value): ?Applications
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
