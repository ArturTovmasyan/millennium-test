<?php

namespace App\Repository;

use App\Entity\UserOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserOrder>
 */
class UserOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOrder::class);
    }
    public function findOrdersByUser($id): array
    {
        return $this->createQueryBuilder('uo')
            ->select('
                uo.id AS orderId,
                p.title AS title,
                p.price AS price
            ')
            ->join('uo.user', 'u')
            ->join('uo.product', 'p')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->orderBy('p.title', 'ASC')
            ->addOrderBy('p.price', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(100)
            ->getQuery()
            ->getArrayResult();
    }
}
