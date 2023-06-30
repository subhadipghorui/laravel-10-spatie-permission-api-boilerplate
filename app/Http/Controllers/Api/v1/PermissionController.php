<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index(){
        try{
            $data['permissions'] = Permission::all();
            return $this->sendResponse("Permissions fetch successfully", $data, 200);
        } catch(\Exception $e){
            $this->handleException($e);
        }
    }

    public function changeStatus(Request $request){
        try{
            DB::beginTransaction();
            $data['permission'] = Permission::find($request->id);
            if(empty($data['permission'])){
                return $this->sendError("Permission not found", ["errors" => ["general" => "Permission not found"]], 404);
            }
            $data['permission']->update(['status' => false]);
            DB::commit();

            // Clear cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            return $this->sendResponse("Permission updated successfully", $data, 201);

        } catch(\Exception $e){
            DB::rollBack();
            $this->handleException($e);
        }
    }
}
