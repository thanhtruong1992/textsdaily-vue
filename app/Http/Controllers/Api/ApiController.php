<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * FN response success
     * @param array data
     * @return object
     */
    public function success($data) {
        return response()->json([
            "data" => $data,
            "status" => true
        ], 200)->header('Content-Type', 'application/json');
    }

    /**
     * FN response accept 
     * @return object
     */
    public function accept() {
        return response()->json([
            "status" => true
        ], 202)->header('Content-Type', 'application/json');
    }

    /**
     * FN response bad request
     * @param string $message
     * @param array $error
     * @return object
     */
    public function badRequest($message = null, $error = []) {
        return response()->json([
            "message" => $message,
            "errors" => $error,
            "status" => false
        ], 400)->header('Content-Type', 'application/json');
    }

    /**
     * FN response not found
     * @param string $message
     * @param array $error
     * @return object
     */
    public function notFound($message = null, $error = []) {
        return response()->json([
            "message" => $message,
            "errors" => $error,
            "status" => false
        ], 404)->header('Content-Type', 'application/json');
    }
}
