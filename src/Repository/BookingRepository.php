<?php

namespace App\Repository;

use App\Entity\Booking;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Booking repository
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    /**
     * Array of allowed filters
     */
    const ALLOWED_FILTERS = ['id', 'member', 'owner', 'room'];

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Add
     *
     * @param Booking $entity
     * @param bool $flush
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function add(Booking $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Add unique booking on current datetime range
     *
     * @param Booking $booking
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConnectionException
     * @throws NonUniqueResultException
     * @throws TransactionRequiredException
     */
    public function addBooking(Booking $booking): bool
    {
        $bookingAdded = false;
        $connection = $this->_em->getConnection();
        $connection->beginTransaction();
        try {
            $bookingCheck = $this->_em->createQueryBuilder()
                ->select(['booking.startDatetime', 'booking.endDatetime'])
                ->from(Booking::class, 'booking')
                ->where('(:start < booking.startDatetime AND :end > booking.startDatetime) 
                OR (:start > booking.startDatetime AND :end < booking.endDatetime)
                OR (:start < booking.endDatetime AND :end > booking.endDatetime)');
            if ($booking->getId()) {
                $bookingCheck = $bookingCheck->andWhere('booking.id != :id')
                    ->setParameter('id', $booking->getId());
            }
            $bookingCheck = $bookingCheck
                ->setParameter('start', $booking->getStartDatetime())
                ->setParameter('end', $booking->getEndDatetime())
                ->getQuery()
                ->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)
                ->getOneOrNullResult();

            if (!$bookingCheck) {
                $this->add($booking);
                $bookingAdded = true;
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();

            throw $e;
        }

        return $bookingAdded;
    }

    /**
     * Remove
     *
     * @param Booking $entity
     * @param bool $flush
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Booking $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Find by filters between start and end
     *
     * @param array $filters
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @return Booking[] Returns an array of Booking objects
     */
    public function findByFiltersBetweenStartEnd(array $filters, DateTimeInterface $start, DateTimeInterface $end): array
    {
        $result = $this->createQueryBuilder('booking')
            ->where(
                '(:start < booking.startDatetime AND :end > booking.startDatetime) 
                OR (:start > booking.startDatetime AND :end < booking.endDatetime)
                OR (:start < booking.endDatetime AND :end > booking.endDatetime)'
            )
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'));

        foreach ($filters as $filterName => $filter) {
            if (!in_array($filterName, self::ALLOWED_FILTERS) && !is_int($filter)) {
                continue;
            }
            switch ($filterName) {
                case 'member':
                    $result = $result
                        ->andWhere(':member MEMBER OF booking.bookingMembers')
                        ->setParameter('member', (int) $filter);
                    break;
                default:
                    if ($filter < 0) {
                        $result = $result
                            ->andWhere('booking.' . $filterName . ' != :' . $filterName);
                        $result = $result
                            ->setParameter($filterName, abs($filter));
                    } else {
                        $result = $result
                            ->andWhere('booking.' . $filterName . ' = :' . $filterName);
                        $result = $result
                            ->setParameter($filterName, $filter);
                    }
                    break;
            }
        }

        return $result->getQuery()->getResult();
    }
}
