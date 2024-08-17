<?php

namespace App\Http\Controllers;

use App\Http\Resources\RolesAndPermissionsResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Seeker;
use App\Models\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use responseTrait;

    public function addRole(Request $request) {

        $validator = Validator::make($request->all(), [
            'new_name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'file' => 'sometimes',
            'type' => function ($attribute, $value, $fail) use ($request) {
                if ($request->file('file') && !$request->input('type')) {
                    $fail('The type field is required when a file is uploaded.');
                }
            },
        ]);

        if ($validator->fails()){
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $permissions = $request->permissions;
        $role = Role::create(['name' => $request->new_name, 'guard_name' => 'web'])->givePermissionTo($permissions);

        return $this->apiResponse($role, 'Role created successfully', 200);
    }

    public function editRole(Request $request, $id) {
        try {
            $validate = Validator::make($request->all(), [
                'new_name' => 'string',
                'permissions' => 'array'
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 400);
            }
            if ($id <= 5) {
                return $this->apiResponse(null, 'You cannot edit the default roles', 403);
            }
            $role = Role::findById($id, 'web');
            $role->name = $request->new_name;
            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }
            $role->save();
            return $this->apiResponse($role, 'Edited successfully', 200);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 404);
        }
    }

    public function deleteRole($id) {
        try {
            if ($id <= 5) {
                return $this->apiResponse(null, 'You cannot delete the default roles', 403);
            }

            $role = Role::findById($id, 'web');
            $role->delete();
            return $this->apiResponse(null, __('strings.deleted_successfully'), 200);

        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 404);
        }
    }

    public function allRoles() {
        $roles = Role::all();
        $data = [];
        foreach ($roles as $role) {
            $data[$role->name] = $role->permissions->pluck('name');
        }
        return RolesAndPermissionsResource::collection($roles);
    }

    // For employee
    public function getRoles() {
        $roles = Role::all();
        $roles = $roles->reject(function(Role $role) {
            return ($role->name === 'owner' || $role->name === 'employee' || $role->name === 'company' || $role->name === 'job_seeker' || $role->name === 'user');
        });
        $data = [];
        foreach ($roles as $role) {
            $data[$role->name] = $role->permissions->pluck('name');
        }
        return RolesAndPermissionsResource::collection($roles);
    }

    public function rolesForUserUpgrade() {
        $roles = Role::all();
        $roles = $roles->reject(function(Role $role) {
            return ($role->name === 'owner' || $role->name === 'company' || $role->name === 'job_seeker' || $role->name === 'user');
        });
        $data = [];
        foreach ($roles as $role) {
            $data[$role->name] = $role->permissions->pluck('name');
        }
        return RolesAndPermissionsResource::collection($roles);
    }

    public function editUserRoles($id, Request $request){
        $validate = Validator::make($request->all(), [
            'roles_name' => 'required|array'
        ]);
        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }

        $user = User::where('id', $id)->first();
        if ($user) {
            $previousRoles = $user->roles_name;
            if (array_search('company', $previousRoles)) {
                return $this->apiResponse(null, 'You cannot upgrade a company to employee', 403);
            } else if (array_search('job_seeker', $previousRoles)) {
                $seeker = Seeker::where('user_id', $user->id)->first();
                if ($seeker) {
                    $employee = Employee::create([
                        'user_id' => $id,
                        'first_name' => $seeker->first_name,
                        'last_name' => $seeker->last_name,
                        'gender' => $seeker->gender,
                        'birth_day' => $seeker->birth_day,
                        'is_change_password' => 1,
                    ]);
                    $seeker->delete();
                }
            }
            $roles = [];
            foreach ($request->roles_name as $role) {
                if (($role != 'user' && $role != 'company' && $role != 'job_seeker' && $role != null))
                {
                    $roles[] = $role;
                }
            }
            $user->roles()->detach();
            $user->syncRoles($roles);
            $user->update([
                'roles_name' => $roles
            ]);
            return $this->apiResponse(null, 'Roles updated successfully', 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }
}
