<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\User;
use App\services\FileService;
use App\Traits\responseTrait;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\type;

class NewsController extends Controller
{
    use responseTrait;
    public function create(NewsRequest $request, FileService $fileService) {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $news = News::create([
                'title' => $request->title,
                'body' => $request->body,
                'created_by' => $user->id
            ]);

            $img_paths = [];
            if ($request->file('images') && $request->images != null) {
                $images = $request->file('images');
                if (is_array($images)) {
                    foreach ($images as $image) {
                        $image_path = $fileService->store($image, 'images/Dashboard/News');
                        $img_paths[] = $image_path;
                    }
                } else {
                    $image_path = $fileService->store($images, 'images/Dashboard/News');
                    $img_paths[] = $image_path;
                }
                foreach ($img_paths as $value) {
                    $news->images()->create(['url' => $value]);
                }
            }

            $file_paths = [];
            if ($request->file('files') && $request->files != null) {
                $files = $request->file('files');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $file_path = $fileService->store($file, 'files/Dashboard/News');
                        $file_paths[] = $file_path;
                    }
                } else {
                    $file_path = $fileService->store($files, 'files/Dashboard/News');
                    $file_paths[] = $file_path;
                }
                foreach ($file_paths as $value) {
                    $news->files()->create(['url' => $value]);
                }
            }
            return $this->apiResponse(new NewsResource($news), __('strings.added_successfully'), 201);
        } catch (\Exception $th) {
            return $this->apiResponse(null, $th->getMessage(), 500);
        }
    }

    public function update($id, Request $request, FileService $fileService) {
        $news = News::withTrashed()->where('id', $id)->first();
        $img_paths = [];
        $file_paths = [];
            if ($request->file('images') && $request->images != null) {
                $images = $request->file('images');
                if (is_array($images)) {
                    foreach ($images as $image) {
                        $image_path = $fileService->store($image, 'images/Dashboard/News');
                        $img_paths[] = $image_path;
                    }
                } else {
                    $image_path = $fileService->store($images, 'images/Dashboard/News');
                    $img_paths[] = $image_path;
                }
                foreach ($img_paths as $value) {
                    $news->images()->create(['url' => $value]);
                }
            }

            if ($request->file('files') && $request->files != null) {
                $files = $request->file('files');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $file_path = $fileService->store($file, 'files/Dashboard/News');
                        $file_paths[] = $file_path;
                    }
                } else {
                    $file_path = $fileService->store($files, 'files/Dashboard/News');
                    $file_paths[] = $file_path;
                }
                foreach ($file_paths as $value) {
                    $news->files()->create(['url' => $value]);
                }
            }

        $news->update([
            'title' => $request->input('title', $news->title),
            'body' => $request->input('body', $news->body),
        ]);

        if ($news) {
            return $this->apiResponse(new NewsResource($news), __('strings.updated_successfully'), 201);
        }
        return $this->apiResponse(null, __('strings.error_occurred_talk_developer'), 500);
    }

    public function deleteImage($news_id, $img_id, FileService $fileService) {

        $news = News::withTrashed()->where('id', $news_id)->first();
        $img = $news->images()->where('id', $img_id)->first();
        if ($img) {
            $fileService->delete($img->url);
            $img->delete();
            return $this->apiResponse(new NewsResource($news), __('strings.deleted_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function deleteFile($news_id, $file_id, FileService $fileService) {
        $news = News::withTrashed()->where('id', $news_id)->first();
        $file = $news->files()->where('id', $file_id)->first();
        if ($file) {
            $fileService->delete($file->url);
            $file->delete();
            return $this->apiResponse(new NewsResource($news), __('strings.deleted_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function delete($id, FileService $fileService) {
        $news = News::withTrashed()->where('id', $id)->first();
        if ($news) {
            $images = $news->images;
            $files = $news->files;
            if (!is_null($images)) {
                foreach ($images as $value) {
                    $fileService->delete($value->url);
                }
                $news->images()->delete();
            }
            if (!is_null($files)) {
                foreach ($files as $value) {
                    $fileService->delete($value->url);
                }
                $news->files()->delete();
            }
            if ($news->forceDelete()){
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }
            return $this->apiResponse(null, __('strings.error_occurred'),  500);
        }
        return $this->apiResponse(null, __('strings.not_found'),  404);
    }

    public function hiding($id) {
        $news = News::where('id', $id)->first();
        if ($news) {
            if ($news->delete()){
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }
            return $this->apiResponse(null, __('strings.error_occurred'),  500);
        }
        return $this->apiResponse(null, __('strings.not_found'),  404);
    }

    public function show($id) {
        $news = News::withTrashed()->where('id', $id)->first();
        if ($news) {
            if ($news->restore()){
                return $this->apiResponse($news, 'restored successfully', 200);
            }
            return $this->apiResponse(null, __('strings.error_occurred'),  500);
        }
        return $this->apiResponse(null, __('strings.not_found'),  404);
    }

    public function getAllNews() {
        $news = NewsResource::collection(News::latest()->withTrashed()->get());
        if($news) {
            return $this->apiResponse($news, __('strings.all_news'), 200);
        }
        return $this->apiResponse(null, __('strings.error_occurred'), 500);
    }

    public function getNews() {
        $news = NewsResource::collection(News::latest()->get());
        if($news) {
            return $this->apiResponse($news, __('strings.all_news'), 200);
        }
        return $this->apiResponse(null, __('strings.error_occurred'), 500);
    }
}
