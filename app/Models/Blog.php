<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    const OPEN = 1;
    const CLOSE = 0;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    // Laravelクエリスコープ
    // 公開非公開を判定する
    public function scopeOnlyOpen($query) {
        return $query->where('status', self::OPEN);
    }
}
