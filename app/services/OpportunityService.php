<?php


namespace App\services;


use App\Models\Opportunity;
use App\Traits\responseTrait;
use Illuminate\Support\Facades\Auth;

class OpportunityService
{
    use responseTrait;
    protected $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function createOpportunity(
        $company_id, $title, $body, $files, $images,
        $location, $job_type, $work_place_type, $job_hours,
        $qualifications, $skills_req, $salary, $vacant
    ){

        $opportunity = Opportunity::create([
            'company_id' => $company_id,
            'title' => $title,
            'body' => $body,
            'location' => $location,
            'job_type' => $job_type,
            'work_place_type' => $work_place_type,
            'job_hours' => $job_hours,
            'qualifications' => $qualifications,
            'skills_req' => $skills_req,
            'salary' => $salary,
            'vacant' => $vacant
        ]);

        $img_paths = [];
        if (!is_null($images)) {
            if (is_array($images)) {
                foreach ($images as $image) {
                    $image_path = $this->fileService->store($image, 'images/Company/Opportunities');
                    $img_paths[] = $image_path;
                }
            } else {
                $image_path = $this->fileService->store($images, 'images/Company/Opportunities');
                $img_paths[] = $image_path;
            }
            foreach ($img_paths as $value) {
                $opportunity->images()->create(['url' => $value]);
            }
        }

        $file_paths = [];
        if (!is_null($files)) {
            if (is_array($files)) {
                foreach ($files as $file) {
                    $file_path = $this->fileService->store($file, 'files/Company/Opportunities');
                    $file_paths[] = $file_path;
                }
            } else {
                $file_path = $this->fileService->store($files, 'files/Company/Opportunities');
                $file_paths[] = $file_path;
            }
            foreach ($file_paths as $value) {
                $opportunity->files()->create(['url' => $value]);
            }
        }

        return $opportunity;
    }

    public function update($request, $opportunity_id){
        $id = Auth::user()->company->id;
        $opportunity = Opportunity::where('company_id', $id)->where('id', $opportunity_id)->first();

        if($opportunity){
            $qualifications = json_decode($request->qualifications);
            $skills_req = json_decode($request->skills_req);
            $opportunity->update([
                'title' => $request->title ?? $opportunity['title'],
                'body' => $request['body'] ?? $opportunity['body'],
                'location' => $request['location'] ?? $opportunity['location'],
                'job_type' => $request['job_type'] ?? $opportunity['job_type'],
                'work_place_type' => $request['work_place_type'] ?? $opportunity['work_place_type'],
                'job_hours' => $request['job_hours'] ?? $opportunity['job_hours'],
                'qualifications' => $qualifications ?? $opportunity['qualifications'],
                'skills_req' => $skills_req ?? $opportunity['skills_req'],
                'salary' => $request['salary'] ?? $opportunity['salary'],
                'vacant' =>$request['vacant'] ?? $opportunity['vacant']
            ]);

            $img_paths = [];
            $file_paths = [];
            if ($request->file('images') && $request->images != null) {
                $images = $request->file('images');
                if (is_array($images)) {
                    foreach ($images as $image) {
                        $image_path = $this->fileService->store($image, 'images/Company/Opportunities');
                        $img_paths[] = $image_path;
                    }
                } else {
                    $image_path = $this->fileService->store($images, 'images/Company/Opportunities');
                    $img_paths[] = $image_path;
                }
                foreach ($img_paths as $value) {
                    $opportunity->images()->create(['url' => $value]);
                }
            }

            if ($request->file('files') && $request->files != null) {
                $files = $request->file('files');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $file_path = $this->fileService->store($file, 'files/Company/Opportunities');
                        $file_paths[] = $file_path;
                    }
                } else {
                    $file_path = $this->fileService->store($files, 'files/Company/Opportunities');
                    $file_paths[] = $file_path;
                }
                foreach ($file_paths as $value) {
                    $opportunity->files()->create(['url' => $value]);
                }
            }
                return $opportunity;
            }
        return $this->apiResponse(null , __('strings.not_found') ,404);
    }

}
