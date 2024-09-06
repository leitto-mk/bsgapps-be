<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    protected $connection = 'bsg_app';
    protected $table = 'mst_emp';
    protected $primaryKey = 'id_mst_emp';
    public $timestamps = true;
    protected $fillable = ['id_mst_emp', 'id_emp', 'id_role_it', 'username', 'is_admin', 'flag_active', 'nama_pegawai'];

    public function checkUserHrms($username)
    {
        return DB::connection('hrms')
            ->table('app_user', 'au')
            ->join('emp_phist as ep', 'au.idPegawai', '=', 'ep.parent_id')
            ->where('au.username', $username)
            ->where('ep.status', 1)
            ->first([
                'au.idPegawai as id_emp',
                'au.namaUser as nama_pegawai',
                'au.username'
            ]);
    }
}
