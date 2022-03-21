<?php

/** @noinspection PhpParameterNameChangedDuringInheritanceInspection */

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\Users\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function get_class;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $id
     * @param $lockMode
     * @param $lockVersion
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     * @throws NoResultException
     *
     * @todo
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?User
    {
        return $this->getQueryForLoadUsersWithLikes()
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return QueryBuilder
     * @TODO
     */
    public function getQueryForLoadUsersWithLikes(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select('u, r, rl')
            ->leftJoin('u.reviews', 'r')
            ->leftJoin('r.likes', 'rl')
            ;
    }

    /**
     * @param string $email
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     * @TODO
     */
    public function loadUserByEmail(string $email): ?User
    {
        return $this->getQueryForLoadUsersWithLikes()
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param string $email
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $email): ?User
    {
        return $this->loadUserByEmail($email);
    }

    /**
     * @param string $email
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $email): ?User
    {
        return $this->loadUserByEmail($email);
    }
}
