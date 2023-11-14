<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['review','rating'];
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    // if you make changes the review in the loaded model then it will triggered
    protected static function booted()
    {
        static::updated(fn (Review $review) => cache()->forget('book:'. $review->book->id));
        static::deleted(fn (Review $review) => cache()->forget('book:'. $review->book->id));
    } 
}
