<?php

declare(strict_types=1);

namespace App\Loan\Repository;

use App\Loan\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function fetchPaymentByRefId(string $refId): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->where('p.refId = :refId')
            ->setMaxResults(1)
            ->setParameter('refId', $refId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function fetchCountPaymentsByPaymentDate(\DateTimeImmutable $paymentDate): int
    {
        $from = $paymentDate->setTime(0, 0, 0);
        $to = $paymentDate->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.paymentDate >= :from')
            ->andWhere('p.paymentDate <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array<int, Payment>
     */
    public function fetchPaymentsByPaymentDate(\DateTimeImmutable $paymentDate, int $page, int $limit): array
    {
        $from = $paymentDate->setTime(0, 0, 0);
        $to = $paymentDate->setTime(23, 59, 59);

        return $this->createQueryBuilder('p')
            ->where('p.paymentDate >= :from')
            ->andWhere('p.paymentDate <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit)
            ->getQuery()
            ->execute()
        ;
    }
}
