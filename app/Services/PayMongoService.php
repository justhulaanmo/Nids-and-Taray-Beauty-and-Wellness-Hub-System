<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = env('PAYMONGO_SECRET_KEY', 'sk_test_placeholder');
        $this->publicKey = env('PAYMONGO_PUBLIC_KEY', 'pk_test_placeholder');
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && $this->secretKey !== 'sk_test_placeholder';
    }

    /**
     * Create PayMongo Checkout Session
     */
    public function createCheckoutSession(array $data)
    {
        if (!$this->isConfigured()) {
            // Return simulation URL if API key not yet set in .env
            return [
                'success' => true,
                'is_simulated' => true,
                'checkout_url' => route('paymongo.simulate', ['transaction_id' => $data['transaction_id']]),
                'checkout_id' => 'sim_' . uniqid(),
            ];
        }

        try {
            $lineItems = [];
            foreach ($data['items'] as $item) {
                $lineItems[] = [
                    'currency' => 'PHP',
                    'amount' => (int) round($item['price'] * 100), // in centavos
                    'name' => $item['name'],
                    'quantity' => 1,
                ];
            }

            if (!empty($data['home_service_fee']) && $data['home_service_fee'] > 0) {
                $lineItems[] = [
                    'currency' => 'PHP',
                    'amount' => (int) round($data['home_service_fee'] * 100),
                    'name' => 'Home Service Transportation Fee',
                    'quantity' => 1,
                ];
            }

            $payload = [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => false,
                        'show_description' => true,
                        'show_line_items' => true,
                        'line_items' => $lineItems,
                        'payment_method_types' => ['gcash', 'paymaya', 'qrph', 'grab_pay', 'card'],
                        'description' => "Nids & Taray - Order " . $data['tracking_number'],
                        'success_url' => route('transactions.show', ['transaction' => $data['transaction_id']]) . '?payment=success',
                        'cancel_url' => route('transactions.show', ['transaction' => $data['transaction_id']]) . '?payment=cancelled',
                    ],
                ],
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/checkout_sessions", $payload);

            if ($response->successful()) {
                $checkoutData = $response->json('data');
                return [
                    'success' => true,
                    'is_simulated' => false,
                    'checkout_url' => $checkoutData['attributes']['checkout_url'],
                    'checkout_id' => $checkoutData['id'],
                ];
            }

            Log::error('PayMongo Checkout Error: ' . $response->body());
            return [
                'success' => false,
                'message' => $response->json('errors.0.detail') ?? 'Hindi ma-konekta sa PayMongo.',
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
