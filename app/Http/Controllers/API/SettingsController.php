<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::query()->paginate();
        return success_out($settings, true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->request->add(['translations' => change_translations($request->translations)]);

        $data = $request->validate([
            'translations.ru.title' => 'required|string|max:255',
            'translations.uz.title' => 'required|string|max:255',
            'translations.en.title' => 'required|string|max:255',
           'link' => 'nullable|string|max:255',
           'is_active' => 'nullable|boolean',
           'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        foreach (config('translatable.locales') as $locale)
        {
            $data[$locale] = $request['translations'][$locale];
        }
        unset($data['translations']);

        if ($request->hasFile('file')) {
            $data['image'] = $request->file('file')->store('settings', 'public');
        }

        $item = new Setting();
        $item->fill($data);

        if ($item->save())
            return success_out($item);

        return error_out([], 422, 'Error saving model!');
    }

    /**
     * Display the specified resource.
     *
     * @param Setting $setting
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        return success_out($setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Setting $setting
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        $request->request->add(['translations' => change_translations($request->translations)]);

        $data = $request->validate([
            'translations.ru.title' => 'required|string|max:255',
            'translations.uz.title' => 'required|string|max:255',
            'translations.en.title' => 'required|string|max:255',
            'link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        foreach (config('translatable.locales') as $locale)
        {
            $data[$locale] = $request['translations'][$locale];
        }
        unset($data['translations']);

        if ($request->hasFile('file')) {
            if ($setting->image && Storage::exists('public/'.$setting->image))
                Storage::delete('public/'.$setting->image);
            $data['image'] = $request->file('file')->store('settings', 'public');
        }

        if ($setting->update($data))
            return success_out($setting);

        return error_out([], 422, 'Error saving model!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Setting $setting
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Setting $setting)
    {
        if ($setting->image && Storage::exists('public/'.$setting->image))
            Storage::delete('public/'.$setting->image);

        $setting->delete();
        return success_out($setting);
    }

    /**
     * @return array
     */
    public function languages()
    {
        $languages = config('translatable.locales');
        return $languages;
    }

    /**
     * @param $id
     * @return array
     */
    public function activeSetting($id)
    {
        $setting = Setting::query()->where(['id' => $id, 'is_active' => true])->get();
        return success_out($setting);
    }

}
