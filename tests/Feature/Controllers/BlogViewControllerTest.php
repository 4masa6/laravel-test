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
//         $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        // DBにブログが登録されている場合、トップページを開くと、ブログのタイトル一覧が表示されていることを表現する
        $blog1 = Blog::factory()->hasComments(1)->create(); // リレーションされている時、->has◯◯()でコメント付きのブログを作成できる
        $blog2 = Blog::factory()->hasComments(3)->create();
        $blog3 = Blog::factory()->hasComments(2)->create();

        $this->get('/')
            // ステータスコードが200かどうか
            ->assertOk()
            // レスポンスにブログのタイトルが含まれているか
            ->assertSee($blog1->title)
            ->assertSee($blog2->title)
            ->assertSee($blog3->title)
            // レスポンスにユーザーの名前が含まれているか
            ->assertSee($blog1->user->name)
            ->assertSee($blog2->user->name)
            ->assertSee($blog3->user->name)
            // レスポンスにユーザーの名前が含まれているか
            ->assertSee("(1件のコメント)")
            ->assertSee("(2件のコメント)")
            ->assertSee("(3件のコメント)")
            // コメントの多い順に並んでいるか
            ->assertSeeInOrder([$blog2->title, $blog3->title, $blog1->title])
        ;
    }

}
