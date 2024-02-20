<?php

declare(strict_types=1);

namespace App\Loan\Repository;

use App\Loan\Entity\Enums\LoanState;
use App\Loan\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loan>
 *
 * @method Loan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loan[]    findAll()
 * @method Loan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function fetchLoanByReference(string $reference): ?Loan
    {
        return $this->createQueryBuilder('l')
            ->where('l.reference = :reference')
            ->andWhere('l.state = :state')
            ->setMaxResults(1)
            ->setParameter('reference', $reference)
            ->setParameter('state', LoanState::Active)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
