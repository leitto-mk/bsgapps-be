<?php

namespace App\Models;

use App\Models\ProjectPicModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model
{
    protected $connection = 'bsg_app';
    protected $table = 'mst_project';
    protected $primaryKey = 'id_mst_project';
    public $timestamps = true;
    protected $fillable = [
        'nama_project',
        'nama_project',
        'deskripsi',
        'flag_proposal',
        'flag_brd',
        'id_priority',
        'status',
        'flag_release',
        'dept_id',
        'div_id',
        'id_inputter',
        'inputter_title',
        'inputter_name',
        'id_approval',
        'approval_title',
        'approval_name',
        'id_approval_it',
        'approval_it_title',
        'approval_it_name',
        'date_created',
        'date_init',
        'date_released',
        'flag_active',
    ];

    public function project($div = NULL)
    {
        $query = ProjectModel::query();

        if ($div !== NULL) {
            $query->where('div_id', $div);
        }

        $result = $query->get();

        return $result;
    }

    public function projectDetail($idMstProject)
    {
        $trxProject = DB::connection('bsg_app')->table('trx_project')->where('id_mst_project', $idMstProject)->get();
        $trxProjectPic = ProjectPicModel::with('emp')->where('id_mst_project', $idMstProject)->get();
        $trxSubTask = DB::connection('bsg_app')->table('trx_sub_task')->where('id_mst_project', $idMstProject)->get();

        $result = [
            "trx_project" => $trxProject,
            "trx_project_pic" => $trxProjectPic,
            "trx_sub_task" => $trxSubTask
        ];

        return empty(json_decode($trxProject, TRUE)) && empty(json_decode($trxProject, TRUE)) && empty(json_decode($trxProject, TRUE)) ? [] : $result;
    }

    public function projectSummary($div = NULL)
    {
        $query = ProjectModel::query();

        if ($div !== NULL) {
            $query->where('div_id', $div);
        }

        $result = $query->select(
            DB::raw("CASE
                        WHEN status IN (0) THEN 'no action'
                        WHEN status IN (1, 3, 5, 6, 7, 9, 11, 12, 13) THEN 'on progress'
                        WHEN status IN (8, 10) THEN 'on hold'
                        WHEN status IN (14) THEN 'released'
                        WHEN status IN (2, 4) THEN 'rejected'
                        ELSE 'unidentified'
                    END AS status_summary"),
            DB::raw("COUNT(*) AS jumlah")
        )
            ->groupBy(DB::raw("CASE
                    WHEN status IN (0) THEN 'no action'
                    WHEN status IN (1, 3, 5, 6, 7, 9, 11, 12, 13) THEN 'on progress'
                    WHEN status IN (8, 10) THEN 'on hold'
                    WHEN status IN (14) THEN 'released'
                    WHEN status IN (2, 4) THEN 'rejected'
                    ELSE 'unidentified'
                END"))
            ->get();

        return $result;
    }

    public function projectProgress($div = NULL)
    {
        $query = ProjectModel::query();

        if ($div !== NULL) {
            $query->where('div_id', $div);
        }

        $result = $query->select(
            'nama_project',
            'status',
            DB::raw('status/18*100 AS hitung')
        )
            ->get();

        return $result;
    }

    public function projectReleased($div = NULL)
    {
        $query = ProjectModel::query();

        if ($div !== NULL) {
            $query->where('div_id', $div);
        }

        $result = $query->select(
            'flag_release',
            DB::raw(' count(*) AS jumlah')
        )
            ->groupBy('flag_release')
            ->get();

        return $result;
    }
}
