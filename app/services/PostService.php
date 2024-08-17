<?php


namespace App\services;


use App\Models\Post;
use App\Models\Seeker;
use Illuminate\Support\Facades\Auth;

class PostService
{

    protected $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store($seeker_id, $body, $images, $files){
        $images_paths = [];
        $files_paths = [];
        $post = Post::create([
            'seeker_id' => $seeker_id,
            'body' => $body,
        ]);
        if (!is_null($images)) {
            if (is_array($images)) {
                foreach ($images as $img) {
                    $path = $this->fileService->store($img,'images/job_seeker/posts');
                    $images_paths[] = $path;
                }
            } else {
                $file =  $this->fileService->store($images,'images/job_seeker/posts');
                $images_paths[] = $file;
            }
            foreach ($images_paths as $value) {
                $post->images()->create([
                    'url' => $value
                ]);
            }
        }
        if (!is_null($files)) {
            if (is_array($files)) {
                foreach ($files as $file) {
                    $file = $this->fileService->store($file,'files/Job_seeker/posts');
                    $files_paths[] = $file;
                }
            } else {
                $file = $this->fileService->store($files,'files/Job_seeker/posts');
                $files_paths[] = $file;
            }
            foreach ($files_paths as $value) {
                $post->files()->create([
                    'url' => $value
                ]);
            }
        }
        return $post;
    }

    public function edit($request, $post)
    {
        $img_paths = [];
        $file_paths = [];
        if ($request->file('images') && $request->images != null) {
            $images = $request->file('images');
            if (is_array($images)) {
                foreach ($images as $image) {
                    $image_path = $this->fileService->store($image, 'images/job_seeker/posts');
                    $img_paths[] = $image_path;
                }
            } else {
                $image_path = $this->fileService->store($images, 'images/job_seeker/posts');
                $img_paths[] = $image_path;
            }
            foreach ($img_paths as $value) {
                $post->images()->create(['url' => $value]);
            }
        }

        if ($request->file('files') && $request->files != null) {
            $files = $request->file('files');
            if (is_array($files)) {
                foreach ($files as $file) {
                    $file_path = $this->fileService->store($file, 'files/Job_seeker/posts');
                    $file_paths[] = $file_path;
                }
            } else {
                $file_path = $this->fileService->store($files, 'files/Job_seeker/posts');
                $file_paths[] = $file_path;
            }
            foreach ($file_paths as $value) {
                $post->files()->create(['url' => $value]);
            }
        }

        $post->update([
            'body' => $request['body'] ?? $post['body'],
            'type' => $request['type'] ?? $post['type']
        ]);

        return $post;
    }
}
