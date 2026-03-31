<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

final class Department extends Model
{
    protected $table = 'departement';
    protected $primaryKey = 'id_departement';
    public $timestamps = false;
}
