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

        // データ検証（入力チェック）
        // DBに保存
        // ログインさせてからマイページにリダイレクト

//        $validData = User::factory()->valid()->raw(); // ->raw(); は make()->toArray(); と同義（インスタンスを作成して配列で取得）
        $validData = User::factory()->validData(); // Factoryに配列を返すメソッドを定義しておけばそのまま使い回せる

        $this->post('signup', $validData)
            ->assertRedirect('mypage/blogs');

        // パスワードはハッシュ化されるので、unsetしておく
        unset($validData['password']);

        // DBにデータが保存されているか
        $this->assertDatabaseHas('users', $validData);

        // パスワードの検証（きちんとハッシュ化されて保存されているか）
        $user = User::firstWhere($validData);
        $this->assertNotNull($user); // userが登録されているか
        $this->assertTrue(\Hash::check('abcd1234', $user->password)); // パスワードが正しいか

        // ユーザーが認証されているかを確認
        $this->assertAuthenticatedAs($user);
    }

    /** @test  store*/
    public function 不正なデータではユーザー登録できない()
    {
//        $this->withoutExceptionHandling();

        // ** データの準備 **
        $url = 'signup';

        // ** 実行 **

        // ** 検証 **
        // メモ：->dumpSession() で実際に出ているバリデーションエラー文を表示できる
        // 空でpostするとリダイレクトされる
        $this->from('signup')->post($url, [])->assertRedirect('signup'); // Laravelの機能で１つ前にリダイレクトされるので、fromを設定しておく
        // 名前を入力しないとエラーになる
        $this->post($url, ['name' => ''])->assertInvalid(['name' => '名前は必ず指定してください。' ]);
        // 21文字以上だとエラーが起こる
        $this->post($url, ['name' => str_repeat('あ', 21)])->assertInvalid(['name' => '名前は、 20文字以下で指定してください。']);
        // 20文字だと通る
        $this->post($url, ['name' => str_repeat('あ', 20)])->assertValid('name');
        // メールアドレスを入力しないとエラーになる
        $this->post($url, ['email' => ''])->assertInvalid(['email' => 'メールアドレスは必ず指定してください。' ]);
        // メールアドレスを正しい形式で入力しないとエラーになる
        $this->post($url, ['email' => 'aaabbbccc'])->assertInvalid(['email' => 'メールアドレスには、有効なメールアドレスを指定してください。' ]);
        $this->post($url, ['email' => 'aaa@あああ.いいい'])->assertInvalid(['email' => 'メールアドレスには、有効なメールアドレスを指定してください。' ]);
        // メールアドレスがユニークかどうか
        User::factory()->create(['email' => 'aaa@bbb.net']);
        $this->post($url, ['email' => 'aaa@bbb.net'])->assertInvalid(['email' => 'メールアドレスの値は既に存在しています。']);
        // パスワードが空だとエラーが起こる
        $this->post($url, ['password' => ''])->assertInvalid(['password' => 'パスワードは必ず指定してください。']);
        // パスワードが7文字だとエラーが起こる
        $this->post($url, ['password' => 'abcd123'])->assertInvalid(['password' => 'パスワードは、8文字以上で指定してください。']);;
        // パスワードが8文字だとエラーが起こらない
        $this->post($url, ['password' => 'abcd1234'])->assertValid('password');

    }
}
