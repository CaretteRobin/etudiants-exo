<?php

namespace model;

class ApiKey extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'apikey';
    protected $primaryKey = 'id_apikey';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

}
?>
