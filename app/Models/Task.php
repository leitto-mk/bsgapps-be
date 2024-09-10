<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'bsg_app';
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trx_sub_task';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_trx_sub_task';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function project() {
        return $this->hasOne(ProjectModel::class, 'id_mst_project', 'id_mst_project');
    }

    public function emp() {
        return $this->hasOne(User::class, 'id_mst_emp', 'id_mst_emp');
    }

    public function taskLog() {
        return $this->hasMany(TaskLog::class, 'id_trx_sub_task', 'id_trx_sub_task');
    }
}
