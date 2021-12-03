<?php

namespace Tests\Unit\Models;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    /** @test user */
    public function userリレーション()
    {
        $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        // blogを１件作成、userメソッドを呼んだ返り値がUserモデルのインスタンスであることを確認
        $blog = Blog::factory()->create();
        $this->assertInstanceOf(User::class, $blog->user);
    }

    /** @test comments */
    public function commentsリレーション()
    {
        $blog = Blog::factory()->create();
        $this->assertInstanceOf(Collection::class, $blog->comments); // Illuminate/Database/Colectionを返すか
    }

    /** @test scopeOnlyOpen */
    public function ブログの公開・非公開のスコープ()
    {
        // 準備
        $blog1 = Blog::factory()->create(
            [
                'status' => Blog::CLOSE,
                'title'  => 'ブログA'
            ]
        );
        $blog2 = Blog::factory()->create(['title' => 'ブログB']);
        $blog3 = Blog::factory()->create(['title' => 'ブログC']);

        // 今回のテストの目的
        // モデルスコープが正しく動作しているか
        $blogs = Blog::OnlyOpen()->get();

        $this->assertFalse($blogs->contains($blog1)); // 非公開が含まれていないことを確認
        $this->assertTrue($blogs->contains($blog2)); // 公開が含まれていることを確認
        $this->assertTrue($blogs->contains($blog3)); // 公開が含まれていることを確認
        // コレクションモデルのcontainsメソッドで含まれているかをチェックできる
    }
}
