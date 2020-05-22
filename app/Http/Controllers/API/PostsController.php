<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Post;
use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param $group
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index($group = 'posts')
    {
        return Post::query()->with('files')->where('group', $group)->orderBy('position', 'asc')->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request  $request
     * @param $group
     * @return Response
     */
    public function create(Request $request, $group = 'posts')
    {
        $request->request->add(['translations' => change_translations($request->translations)]);
        $request->request->add(['slug' => make_slug($request['translations'][app()->getLocale()]['title'])]);
        $request->request->add(['group' => $group]);

        $data = $request->validate(
            [
                'translations.ru.title' => 'required|string|max:255',
                'translations.uz.title' => 'required|string|max:255',
                'translations.en.title' => 'required|string|max:255',
                'parent_id' => 'nullable|int|exists:posts,id',
                'position' => 'nullable|int|min:0',
                'group' => 'required|string|max:255',
                'link' => 'nullable|max:255',
                'slug' => 'required|string|max:255|unique:posts',
                'is_active' => 'nullable|boolean',
//                'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048'
                'file' => 'nullable',
                'file.*' => 'nullable|mimes:doc,docx,pdf,xls,xlsx,jpeg,png,jpg,gif,svg|max:2048'
            ]
        );

        foreach (config('translatable.locales') as $locale)
        {
            $data[$locale] = $request['translations'][$locale];
        }
        unset($data['translations']);

        $item = new Post();

        if (!isset($data['position']))
            $data['position'] = $item->max('position') + 1;

        $item->fill($data);

        if ($item->save()){
            if ($request->hasFile('file')) {
                $data_files = [];
                foreach ($request->file('file') as $k => $file) {
                    $data_files['file'] = $file->store('posts', 'public');
                    $data_files['post_id'] = $item->id;
                    $item['files'][$k] = PostFile::create($data_files);
                }
            }
            return success_out($item);
        } else {
            return error_out([], 422, 'Error saving model!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Response
     */
    public function show(Post $post)
    {
        $post['files'] = $post->files;
        return success_out($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function update(Request $request, Post $post)
    {
        $request->request->add(['translations' => change_translations($request->translations)]);

        $request->request->add(['slug' => make_slug($request['translations'][app()->getLocale()]['title'])]);

        $data = $request->validate(
            [
                'translations.ru.title' => 'required|string|max:255',
                'translations.uz.title' => 'required|string|max:255',
                'translations.en.title' => 'required|string|max:255',
                'parent_id' => 'nullable|int|exists:posts,id|not_in:' . $post->id,
                'position' => 'nullable|int|min:0',
                'link' => 'nullable|max:255',
                'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id,
                'is_active' => 'nullable|boolean',
                'file' => 'nullable',
                'file.*' => 'nullable|mimes:doc,docx,pdf,xls,xlsx,jpeg,png,jpg,gif,svg|max:2048',
                'post_id' => 'nullable',
                'post_id.*' => 'nullable|int'
            ]
        );

        foreach (config('translatable.locales') as $locale)
        {
            $data[$locale] = $request['translations'][$locale];
        }
        unset($data['translations']);

        if (!isset($data['position']))
            $data['position'] = $post->max('position') + 1;

        if ($post->update($data)){
            if ($request->hasFile('file')) {

                $data_files = [];
                foreach ($request->file('file') as $k => $file) {
                    $data_files['file'] = $file->store('posts', 'public');
                    $data_files['post_id'] = $post->id;
                    $post['files'][$k] = PostFile::create($data_files);
                }
            }

            if ($request->has('post_id')) {
                $deleted_files = PostFile::query()->whereIn('id', json_decode($request->post_id))->get();
                foreach ($deleted_files as $deleted_file) {
                    Storage::delete('public/'.$deleted_file->file);
                    $deleted_file->delete();
                }
            }
            return success_out($post);
        } else {
            return error_out([], 422, 'Error saving model!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post $post
     * @return Response
     * @throws \Exception
     */
    public function destroy(Post $post)
    {
        $files = PostFile::query()->where('post_id', $post->id)->get();
        foreach ($files as $file) {
            Storage::delete('public/'.$file->file);
            $file->delete();
        }
        $post->delete();
        return success_out($post);
    }

    /**
     * Display a listing of the resource.
     * @param $group
     * @return Response
     */
    public function activePosts($group = 'posts')
    {
        $posts = Post::query()->with('files')->where(['is_active' => true, 'group' => $group])->orderBy('position', 'asc')->paginate();

        return success_out($posts, true);
    }

    /**
     * Display a listing of the resource.
     * @param $group
     * @return Response
     */
    public function activePost($slug)
    {
        $post = Post::query()->with('files')->where(['is_active' => true, 'slug' => $slug])->get();

        return success_out($post);
    }
}
