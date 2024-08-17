<?php

namespace App\Http\Resources;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Seeker;
use App\Models\User;
//use http\Client\Curl\User;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\String\u;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::where('id' , $this->id)->first();
            $data=[
                'id' => $this->id,
                'user_name' => $this->user_name,
                'email' => $this->email,
                'is_verified' => $this->is_verified === 1,
                'created_at'=>$this->created_at->toDateTimeString(),
                'roles_name' => $this->roles_name,
                'type' => null,
                'is_banned' => $this->banned_at ? true : false
            ];

            if($user->hasRole('employee')){
                $employee = $user->employee;
                $data['type'] = 'employee';
                $data['more_info'] = new EmployeeResource($employee);
            }

            else if ($user->hasRole('job_seeker')) {
                $seeker = $user->seeker;
                $data['type'] = 'job_seeker';
                $data['more_info'] = new SeekerResource($seeker);
            }

            else if($user->hasRole('company')) {
                $company = $user->company;
                $data['type']= 'company';
                $data['more_info'] = new CompanyResource($company);
            }

            else if($user->hasRole('owner')) {
                $data['type'] = 'owner';
            }

        return $data;
    }
}
