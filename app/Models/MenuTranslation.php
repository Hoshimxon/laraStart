<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $menu_id
 * @property string $locale
 * @property string $created_at
 * @property string $updated_at
 */
class MenuTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'content'];
}
