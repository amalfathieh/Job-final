<?php


namespace App\services;


use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Models\ContactInfo;
use App\Models\Seeker;
use App\Models\User;
use App\Traits\responseTrait;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\search;

class SeekerService
{
    protected $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    use responseTrait;
    public function createSeeker(
        $first_name,
        $last_name,
        $birth_day,
        $gender,
        $location,
        $image,
        $skills,
        $certificates,
        $specialization,
        $about,
        $contact_info) {
            $seeker_image = null;
            if ($image != '') {
            $seeker_image = $this->fileService->store($image,'images/job_seeker/profilePhoto');
            }

            $skills = json_decode($skills);
            $certificates = json_decode($certificates);
            $seeker = Seeker::create([
                'user_id' => Auth::user()->id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'birth_day' => $birth_day,
                'location' => $location,
                'skills' => $skills,
                'certificates' => $certificates,
                'specialization' => $specialization,
                'about' => $about
            ]);

            $seeker->image()->create([
            'url' => $seeker_image
            ]);
            $contactInfo = ContactInfo::create([
                'user_id' => Auth::user()->id,
                'email' => $contact_info['email'] ?? null,
                'phone' => $contact_info['phone'] ?? null,
                'linkedin' => $contact_info['linkedin'] ?? null,
                'gitHub' => $contact_info['gitHub'] ?? null,
                'website' => $contact_info['website'] ?? null,
            ]);
            return $seeker;
        }

    public function update($request){
        $seeker_image = null;
        $user = Auth::user();
        $seeker = Seeker::where('user_id', $user->id)->first();
        $old_file = $seeker->image->url ?? null;

        if ($request->hasFile('image') && $request->image != '') {
            $seeker_image = $this->fileService->update($request->image, $old_file ,'images/job_seeker/profilePhoto');
            if ($seeker->image()->where('imageable_id', $seeker->id)->first()) {
                $seeker->image()->where('imageable_id', $seeker->id)->update(['url' => $seeker_image]);
            } else {
                $seeker->image()->create(['url' => $seeker_image]);
            }
        }
        $skills = json_decode($request['skills']);
        $certificates = json_decode($request['certificates']);
        $seeker->update([
            'first_name' =>$request['first_name'] ?? $seeker['first_name'],
            'last_name' =>$request['last_name'] ?? $seeker['last_name'],
            'birth_day' =>$request['birth_day'] ?? $seeker['birth_day'],
            'location' =>$request['location'] ?? $seeker['location'],
            'skills' =>$skills ?? $seeker['skills'],
            'specialization'=>$request['specialization'] ?? $seeker['specialization'],
            'certificates'=>$certificates ?? $seeker['certificates'],
            'about' =>$request['about'] ?? $seeker['about'],
            'gender' =>$request['gender'] ?? $seeker['gender']
        ]);

        $contactInfo = $user->contactInfo;
        $contactInfo->update([
            'email' => $request['contact_info']['email'] ?? $contactInfo->email,
            'phone' => $request['contact_info']['phone'] ?? $contactInfo->phone,
            'linkedin' => $request['contact_info']['linkedin'] ?? $contactInfo->linkedin,
            'gitHub' => $request['contact_info']['gitHub'] ?? $contactInfo->twitter,
            'website' => $request['contact_info']['website'] ?? $contactInfo->website,
        ]);
        return new UserResource($user);
    }
}
