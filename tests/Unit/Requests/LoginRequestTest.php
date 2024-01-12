<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Auth\LoginRequest;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    public function test_login_request_validation_passes()
    {
        $request = new LoginRequest([
            'username' => 'validusername',
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_login_request_validation_fails_on_missing_username()
    {
        $request = new LoginRequest([
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    public function test_login_request_validation_fails_on_missing_password()
    {
        $request = new LoginRequest([
            'username' => 'validusername',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_login_request_validation_fails_on_short_username()
    {
        $request = new LoginRequest([
            'username' => 'short',
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    public function test_login_request_validation_fails_on_short_password()
    {
        $request = new LoginRequest([
            'username' => 'validusername',
            'password' => 'short',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }


    public function test_login_request_validation_fails_on_long_username()
    {
        $request = new LoginRequest([
            'username' => 'long_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'password' => 'validpassword',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    public function test_login_request_validation_fails_on_long_password()
    {
        $request = new LoginRequest([
            'username' => 'validusername',
            'password' => 'long_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

}
