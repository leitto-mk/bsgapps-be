<?php

namespace App\Http\Controllers;

use App\Models\UserModel;

use App\Services\JWTService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class User extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel;
    }

    public function getUserByUsername($username)
    {
        try {
            $data = UserModel::where('username', $username)->first();

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
            Log::error($e->getMessage(), ['func' => 'getUserByUsername', 'username' => $username]);

            return response()->json($response, 500);
        }
    }

    public function getUsersActive()
    {
        $data = UserModel::where('flag_active', 1)->get();

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
    }

    public function patchIsAdmin(Request $request, $idMstEmp)
    {
        unset($request['token_payload']);

        $body = $request->all();

        /** Validate Request */
        $validator = [
            'status_admin' => 'required'
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

        try {
            $body = [
                'is_admin' => $body['status_admin']
            ];

            $update = UserModel::where('id_mst_emp', $idMstEmp)->update($body);

            if ($update) {
                $response = [
                    'status' => TRUE,
                    'message' => 'Data berhasil diubah.'
                ];

                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => FALSE,
                    'message' => 'Data gagal diubah.'
                ];

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $update = $e->getCode();

            $response = [
                'status' => FALSE,
                'message' => 'Error.'
            ];
            Log::error($e->getMessage(), ['func' => 'patchIsAdmin', 'id_mst_emp' => $idMstEmp]);

            return response()->json($response, 500);
        }
    }

    public function patchActivate(Request $request, $idMstEmp)
    {
        unset($request['token_payload']);

        $body = $request->all();

        /** Validate Request */
        $validator = [
            'status_aktif' => 'required'
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

        try {
            $body = [
                'flag_active' => $body['status_aktif']
            ];

            $update = UserModel::where('id_mst_emp', $idMstEmp)->update($body);

            if ($update) {
                $response = [
                    'status' => TRUE,
                    'message' => 'Data berhasil diubah.'
                ];

                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => FALSE,
                    'message' => 'Data gagal diubah.'
                ];

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $update = $e->getCode();

            $response = [
                'status' => FALSE,
                'message' => 'Error.'
            ];
            Log::error($e->getMessage(), ['func' => 'patchIsAdmin', 'id_mst_emp' => $idMstEmp]);

            return response()->json($response, 500);
        }
    }

    public function patchRoleIt(Request $request, $idMstEmp)
    {
        unset($request['token_payload']);

        $body = $request->all();

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

        try {
            $body = [
                'id_role_it' => $body['role']
            ];

            $update = UserModel::where('id_mst_emp', $idMstEmp)->update($body);

            if ($update) {
                $response = [
                    'status' => TRUE,
                    'message' => 'Data berhasil diubah.'
                ];

                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => FALSE,
                    'message' => 'Data gagal diubah.'
                ];

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $update = $e->getCode();

            $response = [
                'status' => FALSE,
                'message' => 'Error.'
            ];
            Log::error($e->getMessage(), ['func' => 'patchRoleIt', 'id_mst_emp' => $idMstEmp]);

            return response()->json($response, 500);
        }
    }

    public function putSync($username)
    {
        try {
            $exist = UserModel::where('username', $username)->exists();

            if ($exist) {
                $response = [
                    'status' => FALSE,
                    'message' => "Username $username sudah sinkron."
                ];

                return response()->json($response, 200);
            } else {
                $checkUserHrms = $this->userModel->checkUserHrms($username);

                if (!$checkUserHrms) {
                    $response = [
                        'status' => FALSE,
                        'message' => "Username $username tidak terdaftar di HCMS."
                    ];

                    return response()->json($response, 404);
                }

                $data = [
                    'id_emp' => $checkUserHrms->id_emp,
                    'id_role_it' => 0,
                    'username' => $username,
                    'is_admin' => 0,
                    'flag_active' => 1,
                    'nama_pegawai' => $checkUserHrms->nama_pegawai
                ];

                $insert = UserModel::create($data);

                if ($insert) {
                    $response = [
                        'status' => TRUE,
                        'data' =>  $data,
                        'message' => 'Data berhasil ditambahkan.'
                    ];

                    return response()->json($response, 201);
                } else {
                    $response = [
                        'status' => FALSE,
                        'message' => 'Data gagal ditambahkan.'
                    ];

                    return response()->json($response, 400);
                }
            }
        } catch (\Exception $e) {
            $response = [
                'status' => FALSE,
                'message' => 'Error.'
            ];

            Log::error($e->getMessage(), ['func' => 'putSync', 'username' => $username]);

            return response()->json($response, 500);
        }
    }
}
