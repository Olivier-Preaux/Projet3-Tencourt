<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\SearchService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     *
     */
    public function search(SearchService $search): array
    {
        $query = $this
            ->createQueryBuilder('u');

        if (!empty($search->level)) {
            $query = $query
            ->andWhere('u.level = :level')
            ->setParameter('level', $search->level);
        }

        if (!empty($search->sex)) {
                $query = $query
                ->andWhere('u.sex = :sex')
                ->setParameter('sex', $search->sex);
        }

        if (!empty($search->min)) {
            $query = $query
            ->andWhere('DATE_DIFF(CURRENT_DATE(), u.birthdate)  >= :min*365.25')
            ->setParameter('min', $search->min);
        }
        if (!empty($search->max)) {
            $query = $query
            ->andWhere('DATE_DIFF(CURRENT_DATE(), u.birthdate)  <= :max*365.25')
            ->setParameter('max', $search->max);
        }
        if (!empty($search->city)) {
            $query = $query
            ->andWhere('u.city = :city')
            ->setParameter('city', $search->city);
        }

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
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
