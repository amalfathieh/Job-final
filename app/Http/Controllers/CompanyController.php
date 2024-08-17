<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\ContactInfoRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\ContactInfo;
use App\Models\User;
use App\services\FileService;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class CompanyController extends Controller
{
    use responseTrait;
    public function createCompany(CompanyRequest $request, FileService $fileService)
    {
        try {
            // $this->authorize('isCompany');
            $user = User::where('id', Auth::user()->id)->first();

            $logo_file = $request->file('logo');
            $logo = $fileService->store($logo_file,'images/Company/Logos');
            $company = Company::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'domain' => $request->domain,
                'location' => $request->location,
                'about' => $request->about,
            ]);
            $company->image()->create([
                'url' => $logo
            ]);

            $contactInfo = ContactInfo::create([
                'user_id' => $user->id,
                'email' => $request['contact_info']['email'] ?? null,
                'phone' => $request['contact_info']['phone'] ?? null,
                'linkedin' => $request['contact_info']['linkedin'] ?? null,
                'gitHub' => $request['contact_info']['gitHub'] ?? null,
                'website' => $request['contact_info']['website'] ?? null,
            ]);
            return $this->apiResponse(new CompanyResource($company),  __('strings.success'),  201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function update(ContactInfoRequest $request, FileService $fileService) {
        try {
            // $this->authorize('isCompany');
            $user = User::where('id', Auth::user()->id)->first();

            $logo_file = $request->file('logo');
            $company = $user->company;
            $old_file = $company->image->url ?? null;
            if ($request->hasFile('logo') && $logo_file != '') {
                $logo = $fileService->update($logo_file, $old_file, 'images/Company/Logos');
                if ($company->image()->where('imageable_id', $company->id)->first())
                    $company->image()->where('imageable_id', $company->id)->update([
                        'url' => $logo
                    ]);
                else
                    $company->image()->create(['url' => $logo]);
            }
            $company->update([
                'company_name' => $request->company_name ?? $company['company_name'],
                'domain' => $request->domain ?? $company['domain'],
                'location' => $request->location ?? $company['location'],
                'about' => $request->about ?? $company['about'],
            ]);

            $contactInfo = $user->contactInfo;
            $contactInfo->update([
                'email' => $request['contact_info']['email'] ?? $contactInfo->email,
                'phone' => $request['contact_info']['phone'] ?? $contactInfo->phone,
                'linkedin' => $request['contact_info']['linkedin'] ?? $contactInfo->linkedin,
                'gitHub' => $request['contact_info']['gitHub'] ?? $contactInfo->facebook,
                'website' => $request['contact_info']['website'] ?? $contactInfo->website,
            ]);
            return $this->apiResponse(new UserResource($user), __('strings.success'),  201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

//الشركات المقترحة
    public function proposed_Companies(){
        $seeker = User::find(Auth::user()->id)->seeker;
        $companies = Company::where('domain' , $seeker->specialization)->get();
        $companies = $companies->reject(function (Company $company){
            return $company->user->isBanned();
        });
        return $this->apiResponse(CompanyResource::collection($companies),  'proposed Companies', 200);
    }
}
