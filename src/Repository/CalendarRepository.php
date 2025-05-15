<?php

namespace App\Repository;

use App\Entity\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendar>
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

        public function findWithDaysInRange(int $calendarId, \DateTime $start, \DateTime $end): ?Calendar
    {
        return $this->createQueryBuilder('c')
            ->join('c.weeks', 'w')
            ->join('w.days', 'd')
            ->addSelect('w', 'd')
            ->where('c.id = :id')
            ->andWhere('d.name BETWEEN :start AND :end')
            ->setParameter('id', $calendarId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
