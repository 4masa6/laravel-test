<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogViewControllerTest extends TestCase
{
    /** @test index */
    public function ブログのTOPページを開ける()
    {
        // $this->withoutExceptionHandling(); // エラー内容を表示できる（）

        $response = $this->get('/');
        $response->assertOk(); // ステータスコードが200かどうか
    }

}
