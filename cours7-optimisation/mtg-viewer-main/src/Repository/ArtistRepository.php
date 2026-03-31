<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 *
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    /**
     * @return array<string, int>
     */
    public function getIndexedByExternalId(): array
    {
        $result = $this->createQueryBuilder('artist')
            ->select('artist.id, artist.artistExternalId')
            ->getQuery()
            ->getArrayResult()
        ;

        $artists = [];
        foreach ($result as $artist) {
            $artists[$artist['artistExternalId']] = (int) $artist['id'];
        }

        return $artists;
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    public function findAllForFilter(): array
    {
        return $this->createQueryBuilder('artist')
            ->select('artist.id, artist.name')
            ->orderBy('artist.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
