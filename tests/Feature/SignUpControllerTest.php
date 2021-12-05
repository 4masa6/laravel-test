<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers;

/**
 * @see \App\Http\Controllers\SignUpController
 */
class SignUpControllerTest extends TestCase
{
    /** @test index */
    public function ユーザー登録画面を開ける()
    {
        $this->withoutExceptionHandling();
        // ** データの準備 **
        $this->get('signup')
            ->assertOk();

        // ** 実行 **

        // ** 検証 **

    }
}
