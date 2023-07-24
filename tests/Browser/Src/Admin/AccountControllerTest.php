<?php

namespace Tests\Browser\Src\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AccountControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_if_is_possible_create_account(){

        //Arrange
        $user = User::factory()->make();
        $data = $user->toArray();
        $data['password'] = '12345678';
        $data['password_confirmation'] = '12345678';
        $data['terms'] = 'yes';

        //Act
        $response  = $this->post(route('admin_save_create_account'), $data );

        //Asserts
        $this->assertDatabaseHas('users', [
            'name'  =>  $data['name'],
            'email' =>  $data['email']
        ]);
    }

    public function test_if_validations_work(){
        //Arrange
        $dataRequired = [
            'name'  =>  '',
            'email' =>  '',
            'password'  =>  '',
            'password_confirmation' =>  '',
            'terms' =>  ''
        ];

        $dataEmail = [
            'name'  =>  'Name SecondName',
            'email' =>  'invalid_email.com',
            'password'  =>  '12345678',
            'password_confirmation' =>  '12345678',
            'terms' =>  'yes'
        ];

        $dataSamePassword = [
            'name'  =>  'Name SecondName',
            'email' =>  'valid_email@teste.com',
            'password'  =>  '12345678',
            'password_confirmation' =>  '123456789',
            'terms' =>  'yes'
        ];
        // Act
        $responseDataRequired  = $this->post(route('admin_save_create_account'), $dataRequired ) ;
        //Asserts
        $responseDataRequired->assertSessionHasErrors([
            'name', 'email','password','terms'
        ]);

        //Act
        $responseDataEmail  = $this->post(route('admin_save_create_account'), $dataEmail );
        //Asserts
        $responseDataEmail->assertSessionHasErrors(['email']);
        $responseDataEmail->assertSessionDoesntHaveErrors(['name', 'password_confirmation','terms']);

        //Act
        $responseDataSamePassword  = $this->post(route('admin_save_create_account'), $dataSamePassword );
        //Asserts
        $responseDataSamePassword->assertSessionHasErrors(['password_confirmation']);
        $responseDataSamePassword->assertSessionDoesntHaveErrors(['name', 'email', 'password','terms']);

    }


    public function test_if_form_fileds_exists(){

        $this->browse(function (Browser $browser) {
            $browser->visit(route('admin_create_account'))
                ->assertInputPresent('name')
                ->assertInputPresent('email')
                ->assertInputPresent('password')
                ->assertInputPresent('password_confirmation')
                ->assertInputPresent('terms')
                ->assertPresent('form[action="'. route('admin_save_create_account') .'"')
                ->assertPresent('form button[type="submit"]')
            ;
        });
    }



}
