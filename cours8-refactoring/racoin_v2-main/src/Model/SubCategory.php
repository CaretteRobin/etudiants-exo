<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

final class SubCategory extends Model
{
    protected $table = 'sous_categorie';
    protected $primaryKey = 'id_sous_categorie';
    public $timestamps = false;
}
