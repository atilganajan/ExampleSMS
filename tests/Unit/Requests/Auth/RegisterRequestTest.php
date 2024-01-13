<?php

namespace Requests\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;
    public function test_register_request_validation_passes()
    {
        $request = new RegisterRequest([
            'username' => 'validusername',
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_register_request_validation_fails_on_missing_username()
    {
        $request = new RegisterRequest([
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    public function test_register_request_validation_fails_on_missing_password()
    {
        $request = new RegisterRequest([
            'username' => 'validusername',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_validation_fails_on_short_password()
    {
        $request = new RegisterRequest([
            'username' => 'validusername',
            'password' => 'short',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_validation_fails_on_short_username()
    {
        $request = new RegisterRequest([
            'username' => 'short',
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }


    public function test_register_request_validation_fails_on_long_password()
    {
        $request = new RegisterRequest([
            'username' => 'validusername',
            'password' => Str::random(21),
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_validation_fails_on_long_username()
    {
        $request = new RegisterRequest([
            'username' => Str::random(21),
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }


    public function test_register_request_validation_fails_on_duplicate_username()
    {
        User::create([
            'username' => 'existinguser',
            'password' => 'validpassword',
        ]);

        $request = new RegisterRequest([
            'username' => 'existinguser',
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }


}
