<?php

namespace App\Http\Controllers;

use App\Models\ProjectPicModel;
use App\Models\ProjectModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProjectPic extends Controller
{
    protected $projectModel;

    public function __construct()
    {
        $this->projectModel = new ProjectPicModel;
    }


    public function postProjectPic(Request $request)
    {
        $tokenPayload = $request['token_payload'];

        unset($request['token_payload']);

        $body = $request->all();
        /** Validate Request */
        $validator = [
            'id_mst_project' => 'required'
        ];
        $validator = Validator::make($body, $validator);
        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), TRUE);
            $errorsMessage = [];

            foreach ($errors as $k => $v) {
                $errorsMessage[] = $v[0];
            }

            return response()->json([
                'status' => FALSE,
                'message' => implode(" & ", $errorsMessage)
            ], 422);
        }

        //store new data in table trx_project_pic
        try {
            $x = new ProjectPicModel;
            $x->id_mst_project=$request->id_mst_project;
            $x->id_mst_emp=$request->id_mst_emp;
            $x->pic_type=$request->pic_type;
            $x->flag_active=0;
            $x->save();
            Log::write([
                "msg" => 'Berhasil Simpan trx_project_pic',
                "id_trx_project_pic" => $x->id_trx_project_pic
            ]);
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => 'Data ProjectPic Gagal disimpan.'
            ];
            Log::error($e->getMessage(), ['func' => 'postProjectPic', 'resp' => $response]);
            return response()->json($response, 500);
        }

        //store new data in table trx_project
        try {
            $x = new ProjectModel;
            $x->id_mst_project=$request->id_mst_project;
            $x->process_title=$request->process_title;
            $x->id_mst_emp=$request->id_mst_emp;
            $x->emp_name=$request->emp_name;
            $x->emp_title=$request->emp_title;
            $x->status_from=$request->status_from;
            $x->status_to=$request->status_to;
            $x->save();
            Log::write([
                "status" => 'Berhasil Simpan trx_project',
                "id_trx_project_pic" => $x->id_trx_project_pic
            ]);
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => 'Data Project Gagal disimpan.'
            ];
            Log::error($e->getMessage(), ['func' => 'postProjectPic', 'resp' => $response]);
            return response()->json($response, 500);
        }
        $response = [
            'status' => TRUE,
            'message' => 'Data berhasil disimpan.'
        ];
        return response()->json($response, 400);
    }

    public function deleteProjectPic(Request $request, $id_trx_project_pic)
    {
        unset($request['token_payload']);

        $body = $request->all();

        /** Validate Request */
        $validator = [
            'id_role_it' => 'required'
        ];
        $validator = Validator::make($body, $validator);
        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), TRUE);
            $errorsMessage = [];

            foreach ($errors as $k => $v) {
                $errorsMessage[] = $v[0];
            }

            return response()->json([ 
                'status' => FALSE,
                'message' => implode(" & ", $errorsMessage)
            ], 422);
        }

        $projectPic = ProjectPicModel::find($id_trx_project_pic);
        // Jika data ditemukan, update kolom 'flag' menjadi 0
        if ($projectPic) {
            $projectPic->flag_active = 0; // Set nilai flag menjadi 0
            $projectPic->save();   // Simpan perubahan

            // Mengembalikan respons sukses
            return response()->json([
                'message' => 'ProjectPic flag 0 berhasil',
                'projectPic' => $projectPic // Mengembalikan data yang diupdate
            ], 200);
        } else {
            // Jika data tidak ditemukan, kembalikan respons error
            return response()->json([
                'message' => 'ProjectPic not found.'
            ], 404);
        }
    }

}
