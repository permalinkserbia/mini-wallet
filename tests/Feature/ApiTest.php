<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;

    /**
     * Run seeder before start tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected $amount = 10.0;
    /**
     * Testing get transactions endpoint.
     */
    public function test_get_all_transactions()
    {
        $response = $this->get('/api/transactions');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'data' => []
            ]
        ]);
    }

    /**
     * Testing create transaction endpoint.
     */
    public function test_create_new_transaction()
    {
        $sender = User::where('email', 'user_a@miniwallet.com')->first();
        $receiver = User::where('email', 'user_b@miniwallet.com')->first();

        $response = $this->actingAs($sender)->post('/api/transactions', [
            'receiver_id' => $receiver->id,
            'amount' => $this->amount
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'receiver_id',
            'amount',
            'message'
        ]);
        $this->assertDatabaseHas('transactions', ['receiver_id' => $receiver->id, 'sender_id' => $sender->id, 'amount' => $this->amount]);
    }

    /**
     * Testing insufficient funds case.
     */
    public function test_insufficient_funds_case()
    {
        $sender = User::where('email', 'user_a@miniwallet.com')->first();
        $receiver = User::where('email', 'user_b@miniwallet.com')->first();

        $response = $this->actingAs($sender)->post('/api/transactions', [
            'receiver_id' => $receiver->id,
            'amount' => ($this->amount * 1000)
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    /**
     * Testing case where seller and receiver are the same.
     */
    public function test_same_seller_and_recever_case()
    {
        $sender = User::where('email', 'user_a@miniwallet.com')->first();

        $response = $this->actingAs($sender)->post('/api/transactions', [
            'receiver_id' => $sender->id,
            'amount' => $this->amount
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    /**
     * Testing case amount is negative number.
     */
    public function test_amount_is_negative_number_case()
    {
        $sender = User::where('email', 'user_a@miniwallet.com')->first();
        $receiver = User::where('email', 'user_b@miniwallet.com')->first();

        $response = $this->actingAs($sender)->post('/api/transactions', [
            'receiver_id' => $receiver->id,
            'amount' => (-1 * $this->amount)
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }
}
