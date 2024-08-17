<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeekerRequest;
use App\Http\Resources\SeekerResource;
use App\Http\Resources\UserResource;
use App\Models\Opportunity;
use App\Models\Seeker;
use App\Models\User;
use App\services\SeekerService;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use PDF;
class SeekerController extends Controller
{
    use responseTrait;
    // CREATE PROFILE FOR J0B_SEEKER
    public function create(SeekerRequest $request, SeekerService $service)
    {
        try {
            //$this->authorize('isJobSeeker');
            $image = $request->file('image');
            $seeker = $service->createSeeker(
                $request->first_name,
                $request->last_name,
                $request->birth_day,
                $request->gender,
                $request->location,
                $image,
                $request->skills,
                $request->certificates,
                $request->specialization,
                $request->about,
                $request->contact_info
            );
            return $this->apiResponse(new SeekerResource($seeker), __('strings.created_successfully'), 201);
        } catch (AuthorizationException $authExp) {
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function update(Request $request, SeekerService $seekerService){
        $seeker = $seekerService->update($request);
        return $this->apiResponse($seeker, __('strings.updated_successfully'), 201);
    }


    // public function createCV(Request $request) {
    //     $user = Auth::user();
    //     $data = $request->all();
    //     $data['image'] = $user->seeker->image;
    //     $file_name = $user->user_name . '_CV.pdf';
    //     $pdf = PDF::loadView('pdf.pdf', compact('data'));
    //     return $pdf->download();
    //     // return response()->download(public_path('files/Job_seeker/CVs/' . $file_name));
    // }

        public function createCV(Request $request) {
        $user = Auth::user();
        $data = $request->all();
        $data['image'] = $user->seeker->image;
        $file_name = $user->user_name . '_CV.pdf';
        $html = view()->make('pdf.pdf', compact('data'))->render();
        PDF::SetTitle($request->full_name . '_CV.pdf');
        PDF::AddPage();
        PDF::WriteHTML($html, true, false, true, false, "");
        PDF::output(public_path('files/Job_seeker/CVs/' . $file_name), 'F');
        return response()->download(public_path('files/Job_seeker/CVs/' . $file_name));
    }
}
