<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Advertiser extends Model
{
    protected $table = 'annonceur';
    protected $primaryKey = 'id_annonceur';
    public $timestamps = false;

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advert::class, 'id_annonceur');
    }
}
