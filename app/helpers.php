<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Collection;
//require 'Providers/Swagger/Source/helpers.php';

if (!function_exists('success_out')) {
    /**
     * @param Collection|LengthAwarePaginator|array|Authenticatable|null|string|boolean $data
     * @param null|bool|array $links
     * @param null|array $errors
     * @return ResponseFactory|\Illuminate\Http\Response
     */
    function success_out($data, $links = null, $errors = null)
    {
        if ($links) {
            $links = [
                'count' => $data->count(),
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ];
            $data = $data->getCollection();
        }
        return response([
            'success' => true,
            'data' => $data,
            'links' => $links,
            'errors' => $errors
        ]);
    }
}

if (!function_exists('error_out')) {
    /**
     * @param $errors
     * @param int $code
     * @param null $message
     * @return ResponseFactory|\Illuminate\Http\Response
     */
    function error_out($errors, $code = 422, $message = null)
    {
        return response([
            'success' => false,
            'data' => [],
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}

if (!function_exists('cyrl_to_latin')) {
    /**
     * @param $text
     * @return string
     */
    function cyrl_to_latin($text)
    {
        $cyrl = ['Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ъ', 'Ф', 'Ы', 'В', 'А', 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч', 'С', 'М', 'И', 'Т', 'Ь', 'Б', 'Ю', 'Ё',
            'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю', 'ё',
            'Ғ', 'Қ', 'Ў', 'Ҳ', 'ғ', 'қ', 'ў', 'ҳ', '‘'
        ];

        $lat = ['Y', 'S', 'U', 'K', 'E', 'N', 'G', 'SH', 'SH', 'Z', 'X', "'", 'F', 'I', 'V', 'A', 'P', 'R', 'O', 'L', 'D', 'J', 'E', 'YA', 'CH', 'S', 'M', 'I', 'T', "'", 'B', 'YU', 'YO',
            'y', 's', 'u', 'k', 'e', 'n', 'g', 'sh', 'sh', 'z', 'x', "'", 'f', 'i', 'v', 'a', 'p', 'r', 'o', 'l', 'd', 'j', 'e', 'ya', 'ch', 's', 'm', 'i', 't', "'", 'b', 'yu', 'yo',
            "G`", 'Q', "O`", 'H', 'g`', 'q', 'o`', 'h', "'"
        ];

        return str_replace($cyrl, $lat, $text);
    }
}

//if (!function_exists('make_slug')) {
//    /**
//     * @param $text
//     * @return string
//     */
//    function make_slug($text)
//    {
//        return str_replace(' ', '-', cyrl_to_latin($text));
//    }
//}

function make_slug($text)
{
    return \Illuminate\Support\Str::slug(cyrl_to_latin($text), '-');
}

function clearPhone($phone)
{
    $phone = str_replace(" ", "", $phone);
    $phone = str_replace("-", "", $phone);
    $phone = str_replace("+", "", $phone);

    return $phone;
}

function clearChars($str)
{
    return preg_replace('/[^A-Za-z0-9]/', '', $str);
}

function formatExpiryDate($date)
{
    $items = explode("/", $date);

    if (count($items) == 2)
        return $items[1] . $items[0];
    return $date;
}

if (!function_exists('get_current_title')) {
    /**
     * @param array
     * @return string
     */
    function get_current_title($translations)
    {
        $translations = collect($translations);
        $translations->filter(function ($item) {
            if ($item['locale'] == app()->getLocale())
                return $item['title'];
        });
    }
}

if (!function_exists('change_translations')) {
    /**
     * @param array
     * @return array
     */
    function change_translations($translations)
    {
        $translations = json_decode($translations);
        $new_translations = [];
        foreach ($translations as $translation) {
            $translation = (array) $translation;
            $new_translations[$translation['locale']] = $translation;
            unset($new_translations[$translation['locale']]['locale']);
        }
        return $new_translations;
    }
}