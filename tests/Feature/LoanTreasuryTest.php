<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class LoanTreasuryTest extends TestCase
{
    // Use WithoutMiddleware to bypass auth if you just want to test logic, 
    // OR create a user if you have factories.
    // use WithoutMiddleware; 

    /**
     * Test creating a Capital Account.
     *
     * @return void
     */
    public function test_create_capital_account()
    {
        // 1. Authenticate (Mock or Real)
        // Assuming you have a User model. If not, use WithoutMiddleware.
        $user = new User(['id' => 1]); 
        $this->actingAs($user, 'api');

        // 2. Post Data
        $response = $this->postJson('/api/direct-capital-account', [
            'name' => 'Test Bank Account',
            'type' => 'bank_account',
            'balance' => 5000.00,
            'created_by' => 1
        ]);

        // 3. Assert
        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }

    /**
     * Test Fund Transfer to Pool.
     */
    public function test_fund_transfer()
    {
        $user = new User(['id' => 1]); 
        $this->actingAs($user, 'api');

        // First, get an account ID (or assume ID 1 exists from previous test)
        // For robustness, let's create one first
        $accountResp = $this->postJson('/api/loans/capital-accounts', [
            'name' => 'Transfer Source',
            'type' => 'investor',
            'balance' => 1000.00
        ]);
        $accountId = $accountResp->json('data.id');

        // Now Transfer
        $response = $this->postJson('/api/loans/fund-transfer', [
            'from_account_id' => $accountId,
            'amount' => 500.00,
            'description' => 'Unit Test Transfer',
            'created_by' => 1
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    /**
     * Test Creating Loan Product.
     */
    public function test_create_loan_product()
    {
        $user = new User(['id' => 1]); 
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/loans/products', [
            'name' => 'Test SME Product',
            'min_principal' => 100,
            'max_principal' => 1000,
            'duration_options' => '30,60',
            'repayment_frequency_options' => 'Weekly',
            'created_by' => 1
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }
}
