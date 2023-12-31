<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function scopeTitle(Builder $query, string $title): Builder  
    {
        return $query->where('title','LIKE','%' . $title .'%' );
    }
    public function scopePopular(Builder $query, $from = null , $to = null): Builder{
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q,$from,$to)
            
        ])
        ->orderBy('reviews_count','desc') ;
    }
    public function scopeHighestRated(Builder $query, $from = null , $to = null): Builder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) =>$this->dateRangeFilter($q, $from, $to)
        ],'rating')
            ->orderBy('reviews_avg_rating','desc');
    }
    public function scopeMinReviews(Builder $query,int $minReviews ):Builder {
        return $query->having('reviews_count', '>=' , $minReviews);
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if($from && !$to){
            $query->where('created_at', '>=' , $from );
        }else if(!$from && $to){
            $query->where('created_at', '<=', $to);
        }else if($from && $to){
            $query->whereBetween('created_at', [$from,$to]);
        }
    }
    public function scopePopularLastMonth(Builder $query ):Builder 
    {
        return $query->popular(now()->subMonth(),now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }
    public function scopePopular6LastMonth(Builder $query ):Builder 
    {
        return $query->popular(now()->subMonth(6),now())
            ->highestRated(now()->subMonth(6), now())
            ->minReviews(5);
    }
    public function scopeHighestRatedLastMonth(Builder $query ):Builder 
    {
        return $query->popular(now()->subMonth(),now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }
    public function scopeHighestRated6LastMonth(Builder $query ):Builder 
    {
        return $query->popular(now()->subMonth(6),now())
            ->highestRated(now()->subMonth(6), now())
            ->minReviews(5);
    }
}
