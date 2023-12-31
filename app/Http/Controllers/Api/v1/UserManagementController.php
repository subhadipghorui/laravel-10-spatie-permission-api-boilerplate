<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try{
            $search_query = $request->search ?? null;

            $user_query = DB::table('users');

            if($search_query){
                $user_query = $user_query->where(function($query) use($search_query){
                    $query->orWhere('users.email', 'like', "%".$search_query."%");
                    $query->orWhere('users.username', 'like', "%".$search_query."%");
                    $query->orWhere('users.first_name', 'like', "%".$search_query."%");
                    $query->orWhere('users.last_name', 'like', "%".$search_query."%");
                    $query->orWhere('users.status', 'like', "%".$search_query."%");
                    $query->orWhere('users.created_at', 'like', "%".$search_query."%");
                });
            }
            $data['users'] =$user_query->select('users.id', 'users.username','users.email', 'users.first_name', 'users.last_name', 'users.status', 'users.created_at', 'users.updated_at')->get();
            return $this->sendResponse("All users fetch successfully.", $data, 200);
        } catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users,email',
                'password' => 'required|string|min:4|max:255',
                'about' => 'sometimes|string|max:1000',
                'status' => 'sometimes|boolean',
                'permissions' => 'sometimes|array',
            ], [
                'email.unique' => "The email is already exists."
            ]);
     
            if ($validator->fails()) {
                return $this->sendError("Please enter valid input data", $validator->errors(), 400);
            }
            DB::beginTransaction();
            $data['user']=User::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'about' => $request->about,
                'status' => $request->status ?? true,
            ]);
            $permissions = Permission::whereIn('id',$request->permissions)->get();
            $data['user']->syncPermissions($permissions);
            
            DB::commit();
            return $this->sendResponse("User created successfully.", $data, 201);
        } catch(\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }
  

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try{
            $data['user'] =User::with('permissions:id,name,status')->where('users.id', '=', $id)
            ->select('users.id', 'users.username', 'users.email', 'users.first_name', 'users.last_name', 'users.status', 'users.created_at', 'users.updated_at')->first();

            if(empty($data['user'])){
                return $this->sendError("User not found", ["errors" => ["general" => "User not found"]], 404);
            }
            return $this->sendResponse("User fetch successfully.", $data, 200);
        } catch(\Exception $e){
            $this->handleException($e);
        }
    }

      /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try{
            $data['user'] = User::find($id);
            if(empty($data['user'])){
                return $this->sendError("User not found", ["errors" => ["general" => "User not found"]], 404);
            }
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users,email,'.$id,
                'password' => 'sometimes|string|min:4|max:255',
                'about' => 'sometimes|string|max:1000',
                'status' => 'sometimes|boolean',
                'permissions' => 'sometimes|array',
            ], [
                'email.unique' => "The email is already exists."
            ]);
     
            if ($validator->fails()) {
                return $this->sendError("Please enter valid input data", $validator->errors(), 400);
            }
            DB::beginTransaction();
            $updateUserData = $validator->validated();
            $data['user']->update($updateUserData);

            $permissions = Permission::whereIn('id',$request->permissions)->get();
            $data['user']->syncPermissions($permissions);

            DB::commit();
            return $this->sendResponse("User updated successfully.", $data, 201);
        } catch(\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try{
            $data['user'] = User::find($id);
            if(empty($data['user'])){
                return $this->sendError("User not found", ["errors" => ["general" => "User not found"]], 404);
            }else{
                DB::beginTransaction();
                $data['user']->permissions()->delete();
                $data['user']->delete();
                DB::commit();
                return $this->sendResponse("User deleted successfully.", $data, 200);
            }
        } catch(\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    /**
     * Assign Permissions to the Users
     */
    public function assignPermissions(Request $request, $userid): JsonResponse
    {
        try{
            $data['user'] = User::find($userid);
            $data['permissions'] = Permission::whereIn('id',$request->permissions)->get();
            if(empty($data['user'])){
                return $this->sendError("User not found", ["errors" => ["general" => "User not found"]], 404);
            }else{
                DB::beginTransaction();
                $data['user']->syncPermissions($data['permissions']);
                DB::commit();
                return $this->sendResponse("User's permissions updated successfully.", $data, 200);
            }
        } catch(\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }


}
