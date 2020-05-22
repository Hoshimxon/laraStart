<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;

class UploadController extends Controller
{
    /**
     * @param Request $request
     * @return false|string|null
     */
    public function image(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:512'
        ]);

        return success_out($this->uploadFile($request, 'image'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function images(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:512'
        ]);

        return success_out($this->uploadFiles($request, 'images'));
    }

    /**
     * @param UploadedFile $image
     * @return mixed
     */
    private function upload(UploadedFile $image)
    {
        $name = rand() . '-' . time() . '' . $image->getClientOriginalName();
        return Storage::disk('public')->putFileAs('files', $image, $name);
    }

    /**
     * Upload file by key
     * @param Request $request
     * @param $key
     * @return false|string|null
     */
    public function uploadFile(Request $request, $key)
    {
        if ($request->hasFile($key)) {
            $image = $request->file($key);
            $file = $this->upload($image);
            return asset(Storage::url($file));
        }

        return null;
    }

    /**
     * Upload files by key
     * @param Request $request
     * @param $key
     * @return array
     */
    public function uploadFiles(Request $request, $key)
    {
        $images = [];
        if ($request->hasFile($key)) {
            foreach ($request->file($key) as $image) {
                $file = $this->upload($image);
                $images[] = asset(Storage::url($file));
            }
        }

        return $images;
    }
}
