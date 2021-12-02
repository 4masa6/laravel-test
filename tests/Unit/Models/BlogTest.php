<?php

namespace Tests\Unit\Models;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    /** @test user */
    public function userリレーションができているか()
    {
        $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        // blogを１件作成、userメソッドを呼んだ返り値がUserモデルのインスタンスであることを確認
        $blog = Blog::factory()->create();
        $this->assertInstanceOf(User::class, $blog->user);
    }
}
