<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccess($data = [], $message)
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessages, $error = [], $code = 401)
    {
        $response = [
            'success' => false,
            'message' => $errorMessages,
        ];
        if (!empty($error)) {
            $response['data'] = $error;
        }
        return response()->json($response, $code);
    }
}
