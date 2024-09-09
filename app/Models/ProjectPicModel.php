<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectPicModel extends Model
{
    protected $connection = 'bsg_app';
    protected $table = 'trx_project_pic';
    protected $primaryKey = 'id_trx_mst_project';
    public $timestamps = true;
    // protected $fillable = ['id_mst_project','nama_project'];

    function emp() {
        return $this->hasOne(User::class, 'id_mst_emp', 'id_mst_emp');
    }

}
