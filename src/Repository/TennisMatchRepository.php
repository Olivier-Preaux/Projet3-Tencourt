<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\TennisMatch;
use App\Service\SearchMatchService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method TennisMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method TennisMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method TennisMatch[]    findAll()
 * @method TennisMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TennisMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TennisMatch::class);
    }

    /**
     * @return TennisMatch[] Returns an array of User objects
     *
     */
    public function searchMatch(SearchMatchService $search): array
    {
        $query = $this
            ->createQueryBuilder('t');

        if (!empty($search->level)) {
            $query = $query
            ->andWhere('t.level = :level')
            ->setParameter('level', $search->level);
        }

        if (!empty($search->min)) {
            $query = $query
            ->andWhere('t.eventDate >= :min')
            ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query
            ->andWhere('t.eventDate <= :max')
            ->setParameter('max', $search->max);
        }


        if (!empty($search->startHour)) {
            $query = $query
            ->andWhere('t.startHour >= :startHour')
            ->setParameter('startHour', $search->startHour);
        }

        if (!empty($search->endHour)) {
            $query = $query
            ->andWhere('t.endHour <= :endHour')
            ->setParameter('endHour', $search->endHour);
        }

        if (!empty($search->adress)) {
            $query = $query
            ->andWhere('t.adress = :adress')
            ->setParameter('adress', $search->adress);
        }

        return $query->getQuery()->getResult();
    }

    public function findParticipationMatch(User $user)
    {
        $query = $this->createQueryBuilder('t')
            ->innerJoin('t.participent', 'u')
            ->where('u = :user')
            ->setParameter('user', $user);

        return $query->getQuery()->getResult();  
    }
}
