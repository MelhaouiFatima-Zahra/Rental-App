<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 *
 * @method Car|null find($id, $lockMode = null, $lockVersion = null)
 * @method Car|null findOneBy(array $criteria, array $orderBy = null)
 * @method Car[]    findAll()
 * @method Car[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function add(Car $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Car $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isCarAvailable(Car $car, $startDate, $endDate, ?Reservation $existingReservation = null): bool
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $qb->select('r')
            ->from('App\Entity\Reservation', 'r')
            ->where('r.car = :car')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->between(':start_date', 'r.startDate', 'r.endDate'),
                    $qb->expr()->between(':end_date', 'r.startDate', 'r.endDate')
                )
            );
        if ($existingReservation) {
            $qb->andWhere($qb->expr()->neq('r.id', ':existingReservationId'))
                ->setParameter('existingReservationId', $existingReservation->getId());
        }
        $qb->setParameter('car', $car)
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate);

        $result = $qb->getQuery()->getResult();

        return empty($result);
    }


//    /**
//     * @return Car[] Returns an array of Car objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Car
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
