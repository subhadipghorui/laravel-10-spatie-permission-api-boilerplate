<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function sendResponse($message = "Action Successfull", $data = null, $responseCode = 200){
        return response()->json(["error" => false, "message" => $message, "data"=> $data], $responseCode); 
    }

    public function sendError($message = "Action Failed", $data = null, $responseCode = 400){
        return response()->json(["error" => true, "message" => $message, "data"=> $data], $responseCode); 
    }

    public function handleException($e, $sendResponse = true){
        $data['exception_message'] = $e->getMessage();
        $data['exception_code'] = $e->getCode();
        \Log::error("Exception code: ".$data['exception_code']." - ".$data['exception_message']);
        if($sendResponse){
            return $this->sendError("Something went wrong.", $data, 400); 
        }else{
            return false;
        }
    }
}
