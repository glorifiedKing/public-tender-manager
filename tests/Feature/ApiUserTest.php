<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * feature tests for user endpoints api.
     *
     * @return void
     */
    public function test_api_user_registration()
    {
        
        $response = $this->postJson(route('user.register'),[
            'name' => 'Color Elepant',
            'email' => 'hello@colorelephant.com',
            'password' => '12345678',         
            
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [            
            'name' => 'Color Elepant',
            'email' => 'hello@colorelephant.com',
        ]);        

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);
    }

    public function test_api_user_get_token()
    {
        $user = User::factory()->create();
        $response = $this->postJson(route('user.get.token'),[            
            'email' => $user->email,
            'password' => 'password',         
            
        ]);
        $response->assertSessionHasNoErrors();                   

        $response->assertStatus(200);
                    
    }

    public function test_api_user_unauthorized()
    {
        $response = $this->getJson(route('contracts.get.contract',1));
        $response->assertStatus(401);

    }
}
