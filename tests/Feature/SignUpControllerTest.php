<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers;

/**
 * @see \App\Http\Controllers\SignUpController
 */
class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test index */
    public function ユーザー登録画面を開ける()
    {
        $this->withoutExceptionHandling();
        // ** データの準備 **
        $this->get('signup')
            ->assertOk();
    }

    /** @test store */
    public function ユーザー登録できる()
    {
//        $this->withoutExceptionHandling();
        // ** データの準備 **

        // ** 実行 **

        // ** 検証 **
        // DBに保存
        // ログインさせてからマイページにリダイレクト

//        $validData = User::factory()->valid()->raw(); // ->raw(); は make()->toArray(); と同義（インスタンスを作成して配列で取得）
        $validData = User::factory()->validData(); // Factoryに配列を返すメソッドを定義しておけばそのまま使い回せる

        $this->post('signup', $validData)
            ->assertOk();

        // パスワードはハッシュ化されるので、unsetしておく
        unset($validData['password']);

        // DBにデータが保存されているか
        $this->assertDatabaseHas('users', $validData);

        // パスワードの検証（きちんとハッシュ化されて保存されているか）
        $user = User::firstWhere($validData);
        $this->assertNotNull($user); // userが登録されているか
        $this->assertTrue(\Hash::check('abcd1234', $user->password)); // パスワードが正しいか
    }
}
