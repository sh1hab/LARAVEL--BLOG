<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'posts';

    protected $fillable = ['title', 'content', 'slug', 'upload_id'];

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function scopeCreatedBy($query, $id)
    {
        return $query->where('created_by', $id);
    }
}
