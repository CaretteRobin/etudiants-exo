<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Advert;
use App\Model\Category;
use Illuminate\Support\Collection;

final class SearchService
{
    public function search(array $criteria): Collection
    {
        $keyword = str_replace(' ', '', (string) ($criteria['motclef'] ?? ''));
        $city = str_replace(' ', '', (string) ($criteria['codepostal'] ?? ''));

        if ($this->hasNoCriteria($criteria, $keyword, $city)) {
            return Advert::all();
        }

        $query = Advert::query();

        if ($keyword !== '') {
            $query->where('description', 'like', '%' . $criteria['motclef'] . '%');
        }

        if ($city !== '') {
            $query->where('ville', '=', $criteria['codepostal']);
        }

        $this->applyCategoryFilter($query, (string) ($criteria['categorie'] ?? ''));
        $this->applyPriceFilters($query, (string) ($criteria['prix-min'] ?? 'Min'), (string) ($criteria['prix-max'] ?? 'Max'));

        return $query->get();
    }

    private function hasNoCriteria(array $criteria, string $keyword, string $city): bool
    {
        return $keyword === ''
            && $city === ''
            && (((string) ($criteria['categorie'] ?? '')) === 'Toutes catégories' || ((string) ($criteria['categorie'] ?? '')) === '-----')
            && ((string) ($criteria['prix-min'] ?? 'Min')) === 'Min'
            && in_array((string) ($criteria['prix-max'] ?? 'Max'), ['Max', 'nolimit'], true);
    }

    private function applyCategoryFilter($query, string $category): void
    {
        if ($category === 'Toutes catégories' || $category === '-----') {
            return;
        }

        $categoryId = Category::select('id_categorie')->where('id_categorie', '=', $category)->first()?->id_categorie;
        if ($categoryId !== null) {
            $query->where('id_categorie', '=', $categoryId);
        }
    }

    private function applyPriceFilters($query, string $minimumPrice, string $maximumPrice): void
    {
        if ($minimumPrice !== 'Min' && $maximumPrice !== 'Max') {
            if ($maximumPrice !== 'nolimit') {
                $query->whereBetween('prix', [$minimumPrice, $maximumPrice]);
                return;
            }

            $query->where('prix', '>=', $minimumPrice);
            return;
        }

        if ($maximumPrice !== 'Max' && $maximumPrice !== 'nolimit') {
            $query->where('prix', '<=', $maximumPrice);
            return;
        }

        if ($minimumPrice !== 'Min') {
            $query->where('prix', '>=', $minimumPrice);
        }
    }
}
