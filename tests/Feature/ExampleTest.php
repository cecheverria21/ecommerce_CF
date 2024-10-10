<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\mock;

// beforeEach(function(){

//     //logger("Hola");
//     $this->user = User::factory()->create();

// });


//para agrupar los test
//describe("Test auth", function() {

    it('returns a successful response', function () {
        $response = $this->get('/');
    
        $response->assertStatus(something());
    });
    
    it('is one', function () {
        $this->expect(1)->toBeOne();
    });
    
    it('is Two', function () {
        $this->expect(2)->toBeTwo();
    });
    

//});

it('skil', function (){
})->skip('Not implemented yet');


it('is customer', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    // $response = $this->actingAs($this->user)->get('/');

    $response->assertStatus(200);
    $response->assertSee("products");


});

it('is create an user', function () {
    $user = User::factory()->create();

    // $this->assertEquals(13, User::count());
    // $this->assertDatabaseCount(User::class, 23);
    $this->assertDatabaseHas('users', [
        'email' => $user->email
    ]);

});


it('Fakes product', function() {

    $mock = mock(Product::class, function($mock){
        $mock->shouldReceive('luis')->andReturn('hola');
    });

    $this->assertEquals('hola', $mock->luis());
});




// it('Fakes email', function(){

//     Mail::fake();

//     $user = User::factory()->create();

//     $this->get('send-mail/' . $user->id);

//     Mail::assertQueued(App\Livewire\Auth\ForgotPasswordPage::class);

// });



// namespace Tests\Feature;

// // use Illuminate\Foundation\Testing\RefreshDatabase;
// use Tests\TestCase;

// class ExampleTest extends TestCase
// {
//     /**
//      * A basic test example.
//      */
//     public function test_the_application_returns_a_successful_response(): void
//     {
//         $response = $this->get('/');

//         $response->assertStatus(200);
//         //$response->assertEquals(200,200);
//     }

// }
