<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Astrotomic\Translatable\Validation\RuleFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return $menus = Menu::query()->orderBy('position', 'asc')->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        $request->request->add(['translations' => change_translations($request->translations)]);

        $request->request->add(['slug' => make_slug($request['translations'][app()->getLocale()]['title'])]);

        $data = $request->validate(
            [
                'translations.ru.title' => 'required|string|max:255',
                'translations.uz.title' => 'required|string|max:255',
                'translations.en.title' => 'required|string|max:255',
                'parent_id' => 'nullable|int|exists:menus,id',
                'position' => 'nullable|int|min:0',
                'internal_link' => 'nullable|max:255',
                'external_link' => 'nullable|max:255',
                'slug' => 'required|string|max:255|unique:menus',
                'is_active' => 'nullable|boolean',
                'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]
        );


        foreach (config('translatable.locales') as $locale)
        {
            $data[$locale] = $request['translations'][$locale];
        }
        unset($data['translations']);

        if ($request->hasFile('file')) {
            $data['image'] = $request->file('file')->store('menus');
        }

        $item = new Menu();

        if (!isset($data['position']))
            $data['position'] = $item->max('position') + 1;

        $item->fill($data);

        if ($item->save()){
            return success_out($item);
        } else {
            return error_out([], 422, 'Error saving model!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  Menu  $menu
     * @return Response
     */
    public function show(Menu $menu)
    {
        return success_out($menu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Menu $menu
     * @return Response
     */

    public function update(Request $request, Menu $menu)
    {
        $request->request->add(['translations' => change_translations($request->translations)]);

        $request->request->add(['slug' => make_slug($request['translations'][app()->getLocale()]['title'])]);

        $data = $request->validate(
            [
                'translations.ru.title' => 'required|string|max:255',
                'translations.uz.title' => 'required|string|max:255',
                'translations.en.title' => 'required|string|max:255',
                'parent_id' => 'nullable|int|exists:menus,id|not_in:' . $menu->id,
                'position' => 'nullable|int|min:0',
                'internal_link' => 'nullable|max:255',
                'external_link' => 'nullable|max:255',
                'slug' => 'required|string|max:255|unique:menus,slug,' . $menu->id,
                'is_active' => 'nullable|boolean',
                'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]
        );

        foreach (config('translatable.locales') as $locale)
        {
            $data[$locale] = $request['translations'][$locale];
        }
        unset($data['translations']);

        if ($request->hasFile('file')) {
            if ($menu->image && Storage::exists($menu->image))
                Storage::delete($menu->image);

            $data['image'] = $request->file('file')->store('menus');
        }

        if ($menu->update($data)){
            return success_out($menu);
        } else {
            return error_out([], 422, 'Error saving model!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Menu $menu
     * @return Response
     * @throws \Exception
     */
    public function destroy(Menu $menu)
    {
        if ($menu->image && Storage::exists($menu->image)) {
            Storage::delete($menu->image);
        }
        $menu->delete();
        return success_out($menu);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function activeMenus()
    {
        $menus = Menu::query()->where(['is_active' => true])->orderBy('position', 'asc')->paginate();
        return success_out($menus, true);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function activeMenu($slug)
    {
        $menu = Menu::query()->where(['is_active' => true, 'slug' => $slug])->get();
        return success_out($menu);
    }
}
