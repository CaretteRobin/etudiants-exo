<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public const LIST_PAGE_SIZE = 100;

    public const SEARCH_LIMIT = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function getAllUuids(): array
    {
        $result =  $this->createQueryBuilder('c')
            ->select('c.uuid')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
        return array_column($result, 'uuid');
    }

    public function findOneWithArtistByUuid(string $uuid): ?Card
    {
        return $this->createFilteredQueryBuilder()
            ->andWhere('card.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array{
     *     items: list<Card>,
     *     pagination: array{
     *         page: int,
     *         pageSize: int,
     *         totalItems: int,
     *         totalPages: int
     *     }
     * }
     */
    public function paginateCards(int $page, ?string $setCode = null, ?int $artistId = null): array
    {
        $page = max(1, $page);
        $queryBuilder = $this->createFilteredQueryBuilder(setCode: $setCode, artistId: $artistId);
        $totalItems = (int) (clone $queryBuilder)
            ->select('COUNT(card.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $items = $queryBuilder
            ->orderBy('card.name', 'ASC')
            ->setFirstResult(($page - 1) * self::LIST_PAGE_SIZE)
            ->setMaxResults(self::LIST_PAGE_SIZE)
            ->getQuery()
            ->getResult()
        ;

        return [
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'pageSize' => self::LIST_PAGE_SIZE,
                'totalItems' => $totalItems,
                'totalPages' => max(1, (int) ceil($totalItems / self::LIST_PAGE_SIZE)),
            ],
        ];
    }

    /**
     * @return list<Card>
     */
    public function searchCards(string $query, ?string $setCode = null, ?int $artistId = null): array
    {
        return $this->createFilteredQueryBuilder($query, $setCode, $artistId)
            ->orderBy('card.name', 'ASC')
            ->setMaxResults(self::SEARCH_LIMIT)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return list<string>
     */
    public function findAvailableSetCodes(): array
    {
        $result = $this->createQueryBuilder('card')
            ->select('DISTINCT card.setCode AS setCode')
            ->andWhere('card.setCode IS NOT NULL')
            ->andWhere('card.setCode != :empty')
            ->setParameter('empty', '')
            ->orderBy('card.setCode', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        return array_column($result, 'setCode');
    }

    private function createFilteredQueryBuilder(
        ?string $query = null,
        ?string $setCode = null,
        ?int $artistId = null
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('card')
            ->leftJoin('card.artist', 'artist')
            ->addSelect('artist')
        ;

        if ($query !== null && $query !== '') {
            $queryBuilder
                ->andWhere('LOWER(card.name) LIKE :query')
                ->setParameter('query', '%' . mb_strtolower($query) . '%')
            ;
        }

        if ($setCode !== null && $setCode !== '') {
            $queryBuilder
                ->andWhere('card.setCode = :setCode')
                ->setParameter('setCode', $setCode)
            ;
        }

        if ($artistId !== null) {
            $queryBuilder
                ->andWhere('artist.id = :artistId')
                ->setParameter('artistId', $artistId)
            ;
        }

        return $queryBuilder;
    }
}
