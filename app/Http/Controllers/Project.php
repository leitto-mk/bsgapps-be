<?php

namespace App\Http\Controllers;

use App\Models\ProjectModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Project extends Controller
{
    protected $projectModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel;
    }

    public function getProject(Request $request)
    {
        $tokenPayload = $request['token_payload'];

        try {
            $data = $tokenPayload->is_admin == 1 || $tokenPayload->id_role_it != 0 ? $this->projectModel->project() : $this->projectModel->project($tokenPayload->div_id);

            if ($data) {
                $response = [
                    'status' => TRUE,
                    'data' => $data,
                    'message' => 'Data ditemukan.'
                ];

                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => FALSE,
                    'data' => [],
                    'message' => 'Data tidak ditemukan.'
                ];

                return response()->json($response,  404);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => 'Error.'
            ];

            Log::error($e->getMessage(), ['func' => 'getProjectSummary', 'div_id' => $tokenPayload->div_id]);

            return response()->json($response, 500);
        }
    }

    public function getProjectDetail($idProject)
    {
        try {
            $data = $this->projectModel->projectDetail($idProject);

            if (empty($data)) {
                throw new Exception("Data Tidak ditemukan.", 404);
            }

            $response = [
                'status' => TRUE,
                'data' => $data,
                'message' => 'Data ditemukan.'
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => $e->getMessage()
            ];

            Log::error($e->getMessage(), ['func' => 'getProjectDetail', 'id_mst_project' => $idProject]);

            return response()->json($response, $e->getCode());
        }
    }

    public function getProjectSummary(Request $request)
    {
        $tokenPayload = $request['token_payload'];

        try {
            $divId = $tokenPayload->is_admin == 1 || $tokenPayload->id_role_it != 0 ? NULL : $tokenPayload->div_id;

            $data = json_decode($this->projectModel->projectSummary($divId), TRUE);

            if (empty($data)) {
                throw new Exception("Data Tidak ditemukan.", 404);
            }

            $response = [
                'status' => TRUE,
                'data' => $data,
                'message' => 'Data ditemukan.'
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => $e->getMessage()
            ];

            Log::error($e->getMessage(), ['func' => 'getProjectSummary', 'div_id' => $tokenPayload->div_id]);

            return response()->json($response, $e->getCode());
        }
    }

    public function getProjectProgress(Request $request)
    {
        $tokenPayload = $request['token_payload'];

        try {
            $divId = $tokenPayload->is_admin == 1 || $tokenPayload->id_role_it != 0 ? NULL : $tokenPayload->div_id;

            $data = json_decode($this->projectModel->projectProgress($divId), TRUE);

            if (empty($data)) {
                throw new Exception("Data Tidak ditemukan.", 404);
            }

            $response = [
                'status' => TRUE,
                'data' => $data,
                'message' => 'Data ditemukan.'
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => $e->getMessage()
            ];

            Log::error($e->getMessage(), ['func' => 'getProjectProgress', 'div_id' => $tokenPayload->div_id]);

            return response()->json($response, $e->getCode());
        }
    }

    public function getProjectReleased(Request $request)
    {
        $tokenPayload = $request['token_payload'];

        try {
            $divId = $tokenPayload->is_admin == 1 || $tokenPayload->id_role_it != 0 ? NULL : $tokenPayload->div_id;

            $data = json_decode($this->projectModel->projectReleased($divId), TRUE);

            if (empty($data)) {
                throw new Exception("Data Tidak ditemukan.", 404);
            }

            $response = [
                'status' => TRUE,
                'data' => $data,
                'message' => 'Data ditemukan.'
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => $e->getMessage()
            ];

            Log::error($e->getMessage(), ['func' => 'getProjectReleased', 'div_id' => $tokenPayload->div_id]);

            return response()->json($response, $e->getCode());
        }
    }

    public function postProject(Request $request)
    {
        $tokenPayload = $request['token_payload'];

        unset($request['token_payload']);

        $body = $request->all();
        return response()->json($tokenPayload, 200);
        /** Validate Request */
        $validator = [
            'role' => 'required'
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

        Log::info($body);

        ProjectModel::create($body);

        return response()->json($tokenPayload, 200);
    }
}
