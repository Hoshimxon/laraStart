<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

/**
 * @property integer $id
 * @property string $link
 * @property string $image
 * @property string $image_url
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 */

class Setting extends Model implements TranslatableContract
{
    use Translatable;
    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['link', 'image', 'is_active'];

    /**
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * return string
     */

    public function getImageUrlAttribute()
    {
        return $this->image ? asset(\Storage::url($this->image)) : null;
    }
}
