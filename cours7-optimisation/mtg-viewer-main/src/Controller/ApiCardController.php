<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Repository\CardRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Operations for Magic cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly CardRepository $cardRepository,
        private readonly ArtistRepository $artistRepository
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'List cards with pagination',
        description: 'Returns cards paginated by 100 results and supports set code and artist filters.'
    )]
    #[OA\Parameter(name: 'page', description: 'Page number starting at 1', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1))]
    #[OA\Parameter(name: 'setCode', description: 'Filter cards by set code', in: 'query', schema: new OA\Schema(type: 'string', nullable: true))]
    #[OA\Parameter(name: 'artistId', description: 'Filter cards by artist id', in: 'query', schema: new OA\Schema(type: 'integer', nullable: true))]
    #[OA\Response(response: 200, description: 'Paginated cards list')]
    public function cardList(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $setCode = $this->normalizeNullableString($request->query->get('setCode'));
        $artistId = $request->query->getInt('artistId');

        return $this->json(
            $this->cardRepository->paginateCards(
                $page,
                $setCode,
                $artistId > 0 ? $artistId : null
            )
        );
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    #[OA\Get(
        summary: 'Search cards by name',
        description: 'Returns up to 20 cards. The search starts from 3 characters and supports set code and artist filters.'
    )]
    #[OA\Parameter(name: 'q', description: 'Card name query, minimum 3 characters', in: 'query', required: true, schema: new OA\Schema(type: 'string', minLength: 3))]
    #[OA\Parameter(name: 'setCode', description: 'Filter cards by set code', in: 'query', schema: new OA\Schema(type: 'string', nullable: true))]
    #[OA\Parameter(name: 'artistId', description: 'Filter cards by artist id', in: 'query', schema: new OA\Schema(type: 'integer', nullable: true))]
    #[OA\Response(response: 200, description: 'Card search results')]
    public function search(Request $request): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        if (mb_strlen($query) < 3) {
            return $this->json([
                'items' => [],
                'query' => $query,
            ]);
        }

        $setCode = $this->normalizeNullableString($request->query->get('setCode'));
        $artistId = $request->query->getInt('artistId');

        return $this->json([
            'items' => $this->cardRepository->searchCards(
                $query,
                $setCode,
                $artistId > 0 ? $artistId : null
            ),
            'query' => $query,
        ]);
    }

    #[Route('/set-codes', name: 'set_codes', methods: ['GET'])]
    #[OA\Get(summary: 'List available set codes')]
    #[OA\Response(response: 200, description: 'Distinct set codes available in the catalog')]
    public function setCodes(): Response
    {
        return $this->json([
            'items' => $this->cardRepository->findAvailableSetCodes(),
        ]);
    }

    #[Route('/artists', name: 'artists', methods: ['GET'])]
    #[OA\Get(summary: 'List available artists')]
    #[OA\Response(response: 200, description: 'Artists available for filtering cards')]
    public function artists(): Response
    {
        return $this->json([
            'items' => $this->artistRepository->findAllForFilter(),
        ]);
    }

    #[Route('/{uuid}', name: 'show', methods: ['GET'])]
    #[OA\Get(summary: 'Show one card')]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Card detail')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $card = $this->cardRepository->findOneWithArtistByUuid($uuid);
        if ($card === null) {
            return $this->json(['error' => 'Card not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($card);
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmedValue = trim($value);

        return $trimmedValue === '' ? null : $trimmedValue;
    }
}
