<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;
    /**
     * guarded
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * tags
     *
     * @return void
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * getImageAttribute
     *
     * @param mixed $image
     * @return void
     */
    public function getImageAttribute($image)
    {
    return asset('storage/prestasis/' . $image);
    }
}
