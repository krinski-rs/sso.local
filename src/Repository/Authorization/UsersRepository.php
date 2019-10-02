<?php

namespace App\Repository\Authorization;

use App\Entity\Authorization\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    
    public function findByLogin(string $username)
    {
        $parameters = ['username'=>$username, 'expirationDate' => date('Y-m-d H:i:s'), 'isDeleted'=>FALSE, 'isActive'=>TRUE];
        $objQueryBuilder = $this->createQueryBuilder('usr');
        
        $objEqUsername  = $objQueryBuilder->expr()->eq('usr.username', ':username');
        $objEqIsDeleted = $objQueryBuilder->expr()->eq('usr.isDeleted', ':isDeleted');
        $objEqIsActive  = $objQueryBuilder->expr()->eq('usr.isActive', ':isActive');
        
        $objOrx     = $objQueryBuilder->expr()->orX();
        $objIsNull  = $objQueryBuilder->expr()->isNull('usr.expirationDate');
        $objGt      = $objQueryBuilder->expr()->gt('usr.expirationDate', ':expirationDate');
        $objOrx->add($objIsNull);
        $objOrx->add($objGt);
        
        $objQueryBuilder
            ->andWhere($objEqUsername)
            ->andWhere($objOrx)
            ->andWhere($objEqIsDeleted)
            ->andWhere($objEqIsActive)
            ->setParameters($parameters);
        return $objQueryBuilder->getQuery()->getOneOrNullResult();
    }
    
    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
