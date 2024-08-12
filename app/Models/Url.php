<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_url',
        'short_url',
        'custom_url',
        'user_id',
        'title',
        'qr',
    ];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Url $url) {
            $url->short_url = Str::random(6);
            $url->user_id = auth()->id();
        });
    }
}
