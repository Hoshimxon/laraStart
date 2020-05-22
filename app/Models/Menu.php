<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

/**
 * @property integer $id
 * @property string $external_link
 * @property string $internal_link
 * @property string $slug
 * @property integer $parent_id
 * @property integer $position
 * @property string $image
 * @property string $image_url
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 */

class Menu extends Model implements TranslatableContract
{
    use Translatable;
    public $translatedAttributes = ['title', 'content'];
    protected $fillable = ['parent_id', 'position', 'slug', 'internal_link', 'external_link', 'image', 'is_active'];

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
