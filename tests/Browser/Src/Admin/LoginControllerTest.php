<?php

namespace Tests\Browser\Src\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_if_admin_login_page_contains_fields_for_authentication(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit(route('admin_login'))
                ->assertPresent('form[method="post"]')
                ->assertPresent('form[action="'. route('admin_authenticate') .'"]')
                ->assertInputPresent('login')
                ->assertInputPresent('password')
                ->assertPresent('button[type="submit"]')
            ;
        });
    }

    public function test_if_possible_navigate_to_create_account(){
        $this->browse(function (Browser $browser) {
            $browser->visit(route('admin_login'))
                ->assertPresent('a[href="'. route('admin_create_account')  .'"]')
                ->click('a[href="'. route('admin_create_account')  .'"]')
                ->assertRouteIs('admin_create_account')
            ;
        });
    }

    public function test_if_admin_is_not_possible_authenticate_invalid_login(){
        $this->browse(function (Browser $browser) {
            $browser->visit(route('admin_login'))
                ->type('login', 'invalid name')
                ->type('password', 'invalid password')
                ->click('button[type="submit"]')
                ->assertRouteIs('admin_login')
                ->assertPresent('div.alert-danger')
            ;
        });
    }

    public function test_if_admin_is_possible_authenticate_valid_login(){
        $this->browse(function (Browser $browser) {
            $browser->visit(route('admin_login'))
                ->type('login', 'admin')
                ->type('password', 'admin')
                ->click('button[type="submit"]')
                ->assertRouteIs('admin_index')
            ;
        });
    }

    public function test_if_admin_is_possible_execute_logout(){

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1), 'admin')
                ->visit(route('admin_index'))
                ->assertAuthenticated('admin')
                ->click('.header-nav a.nav-profile')
                ->click('.header-nav a[href="'. route('admin_logout') .'"]')
                ->assertRouteIs('admin_login')
            ;

        });
    }
}
