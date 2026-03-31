<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Advert extends Model
{
    protected $table = 'annonce';
    protected $primaryKey = 'id_annonce';
    public $timestamps = false;
    public $links = null;

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(Advertiser::class, 'id_annonceur');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'id_annonce');
    }
}
