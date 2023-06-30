<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(){
        try{
            $data['roles'] = Role::all();
            return $this->sendResponse("Roles fetch successfully", $data, 200);
        } catch(\Exception $e){
            $this->handleException($e);
        }
    }
    public function changeStatus(Request $request){
        try{
            DB::beginTransaction();
            $data['role'] = Role::find($request->id);
            if(empty($data['role'])){
                return $this->sendError("Role not found", ["errors" => ["general" => "Role not found"]], 404);
            }
            $data['role']->update(['status' => false]);
            DB::commit();
            
            // Clear cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            return $this->sendResponse("Role updated successfully", $data, 201);
        } catch(\Exception $e){
            DB::rollBack();
            $this->handleException($e);
        }
    }
}
