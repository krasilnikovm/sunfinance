<?php

declare(strict_types=1);

namespace App\Loan\Repository;

use App\Loan\Entity\PaymentOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentOrder>
 *
 * @method PaymentOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentOrder[]    findAll()
 * @method PaymentOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentOrder::class);
    }
}
