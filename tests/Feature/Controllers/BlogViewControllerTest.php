<?php

namespace Tests\Feature\Controllers;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogViewControllerTest extends TestCase
{

    use RefreshDatabase; // マイグレーションを実行する。お決まりで書く。


    /** @test index */
    public function ブログのTOPページを開ける()
    {
         $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        // DBにブログが登録されている場合、トップページを開くと、ブログのタイトル一覧が表示されていることを表現する
        $blog1 = Blog::factory()->hasComments(1)->create(); // リレーションされている時、->has◯◯()でコメント付きのブログを作成できる
        $blog2 = Blog::factory()->hasComments(2)->create();
        $blog3 = Blog::factory()->hasComments(3)->create();

        $this->get('/')
            ->assertOk() // ステータスコードが200かどうか
            ->assertSee($blog1->title) // レスポンスにブログのタイトルが含まれているか
            ->assertSee($blog2->title) // レスポンスにブログのタイトルが含まれているか
            ->assertSee($blog3->title) // レスポンスにブログのタイトルが含まれているか
            ->assertSee($blog1->user->name) // レスポンスにユーザーの名前が含まれているか
            ->assertSee($blog2->user->name) // レスポンスにユーザーの名前が含まれているか
            ->assertSee($blog3->user->name) // レスポンスにユーザーの名前が含まれているか
            ->assertSee("(1件のコメント)") // レスポンスにユーザーの名前が含まれているか
            ->assertSee("(2件のコメント)") // レスポンスにユーザーの名前が含まれているか
            ->assertSee("(3件のコメント)"); // レスポンスにユーザーの名前が含まれているか
    }

}
