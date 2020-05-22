<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* @property integer $id
* @property integer $post_id
* @property string $file
* @property string $file_url
 */
class PostFile extends Model
{
    protected $fillable = ['post_id', 'file'];

    /**
     * @var array
     */
    protected $appends = ['file_url'];

    /**
     * return string
     */

    public function getFileUrlAttribute()
    {
        return $this->file ? asset(\Storage::url($this->file)) : null;
    }
}
