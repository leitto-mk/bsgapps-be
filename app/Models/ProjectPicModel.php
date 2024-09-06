<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectPicModel extends Model
{
    protected $connection = 'bsg_app';
    protected $table = 'trx_project';
    protected $primaryKey = 'id_trx_mst_project';
    public $timestamps = true;
    // protected $fillable = ['id_mst_project','nama_project'];

}
