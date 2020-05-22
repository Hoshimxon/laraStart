<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $short_content
 * @property string $content
 * @property integer $post_id
 * @property string $locale
 * @property string $created_at
 * @property string $updated_at
 */

class PostTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'sub_title', 'short_content', 'content'];
}
