<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookToRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'book_title',
        'book_description',
        'authors',
        'categories',
        'average_rating',
        'book_img_url',
        'book_info_link',
    ];
}
