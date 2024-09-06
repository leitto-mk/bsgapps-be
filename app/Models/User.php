<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    protected $connection = 'bsg_app';
    protected $table = 'mst_emp';
    protected $primaryKey = 'id_mst_emp';
    public $timestamps = false;

    public function userValidate($username)
    {
        $result = User::where('username', $username)->where('flag_active', 1)->exists();

        return $result;
    }

    public function authentication($username, $password)
    {
        $result = DB::connection('hrms')
            ->table('app_user')
            ->where('username', $username)
            ->where('password', $password)
            ->exists();

        if ($result) {
            $result = User::select(
                [
                    'id_mst_emp',
                    'id_role_it',
                    'is_admin',
                    'emp_phist.pos_name',
                    'emp.name as nama_pegawai',
                    'emp_phist.dept_id',
                    'emp_phist.div_id'
                ]
            )
                ->join('hrms.app_user', 'app_user.idPegawai', '=', 'id_emp')
                ->join('hrms.emp', 'app_user.idPegawai', '=', 'emp.id')
                ->join('hrms.emp_phist', 'emp.id', '=', 'emp_phist.parent_id')
                ->where('app_user.username', $username)
                ->where('emp_phist.status', 1)
                ->first();
        }

        return $result;
    }
}
