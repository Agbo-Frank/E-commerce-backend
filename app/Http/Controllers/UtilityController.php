<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class UtilityController extends Controller
{ 
    public function responseHandler($message, $data, $error, $status)
    {
        $response = [
            'status' => $status,
            'error' => $error,
            'message' => $message,
            'data' => $data
        ];
        
        return response()->json($response);
    }
}
