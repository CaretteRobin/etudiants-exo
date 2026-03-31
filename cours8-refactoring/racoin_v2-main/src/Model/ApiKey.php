<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

final class ApiKey extends Model
{
    protected $table = 'apikey';
    protected $primaryKey = 'id_apikey';
    public $timestamps = false;
    public $incrementing = false;
}
