<?php

namespace Tests\Feature\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Carbon\Carbon;
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

    /** @test index */
    public function 非公開の記事は表示されない()
    {
//         $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        // 準備
        Blog::factory()->closed()->create([
            'title' => 'ブログA'
        ]);
        Blog::factory()->create(['title' => 'ブログB']);
        Blog::factory()->create(['title' => 'ブログC']);

        $this->get('/')
            // ステータスコードが200かどうか
            ->assertOk()
            // レスポンスにブログのタイトルが含まれていないことを確認
            ->assertDontSee('ブログA')
            // レスポンスにブログのタイトルが含まれているか
            ->assertSee('ブログB')
            ->assertSee('ブログC');
    }

    /** @test show */
    public function ブログの詳細画面が表示でき、コメントが古い順に表示される()
    {
        // 記事を１件準備
        // コメントを同時に作成する関数をfactoryに用意する
        $blog = Blog::factory()->withCommentsData([
                ['created_at' => now()->sub('2 days'), 'name' => '太郎'],
                ['created_at' => now()->sub('3 days'), 'name' => '次郎'],
                ['created_at' => now()->sub('1 days'), 'name' => '三郎']
            ])->create();

        // ↑↑ コールバック関数を使ってリファクタできる
        // $blog = Blog::factory()->create();
        // Comment::factory()->create([
        //     'created_at' => now()->sub('2 days'),
        //     'name' => '太郎',
        //     'blog_id' => $blog->id,
        // ]);
        // Comment::factory()->create([
        //     'created_at' => now()->sub('3 days'),
        //     'name' => '次郎',
        //     'blog_id' => $blog->id,
        // ]);
        // Comment::factory()->create([
        //     'created_at' => now()->sub('1 days'),
        //     'name' => '三郎',
        //     'blog_id' => $blog->id,
        // ]);

        // 記事の詳細ページにアクセス
        $this->get('blogs/'.$blog->id)
            // ページが開かれる
            ->assertOk()
            // ページにタイトルが含まれている
            ->assertSee($blog->title)
            // ページに投稿者の名前が含まれている
            ->assertSee($blog->user->name)
            // コメントが古い順かどうか確認
            ->assertSeeInOrder(['次郎', '太郎', '三郎'])
        ;

    }

    /** @test show */
    public function ブログで非公開のものは詳細画面を表示できない()
    {
//         $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        // 非公開の記事を１件作成
        $blog = Blog::factory()->closed()->create();

        // 記事の詳細ページにアクセスすると、403forbidden になることを確認
        $this->get('blogs/'.$blog->id)
            ->assertForbidden();
    }

    /** @test show */
    public function クリスマスの日は「メリークリスマス」と表示される()
    {
        $blog = Blog::factory()->create();
        Carbon::setTestNow('2020-12-24');
        $this->get('blogs/'.$blog->id)
            ->assertOk()
            ->assertDontSee('メリークリスマス');

        Carbon::setTestNow('2020-12-25');
        $this->get('blogs/'. $blog->id)
            ->assertOk()
            ->assertSee('メリークリスマス');
    }

}
