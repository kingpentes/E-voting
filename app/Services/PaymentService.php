<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    protected function configureMidtrans()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        Log::info('Midtrans Config:', [
            'is_production' => Config::$isProduction,
            'server_key_prefix' => substr(Config::$serverKey, 0, 5),
            'server_key_length' => strlen(Config::$serverKey)
        ]);
    }

    public function createTransaction($orderId, $amount, $customerDetails = [], $itemDetails = [], $redirectUrl = null)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => $customerDetails,
        ];

        if ($redirectUrl) {
            $params['callbacks'] = [
                'finish' => $redirectUrl
            ];
        }

        if (!empty($itemDetails)) {
            $params['item_details'] = $itemDetails;
        }

        try {
            // Get Snap Payment Page URL
            return Snap::createTransaction($params)->redirect_url;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function verifyTransaction($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            Log::error('Midtrans Verification Error: ' . $e->getMessage());
            return null;
        }
    }
}

