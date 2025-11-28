<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaydisiniClient
{
    public function createTransaction(array $params): array
    {
        $baseUrl   = rtrim(config('paydisini.base_url'), '/');
        $apiKey    = config('paydisini.api_key');

        $uniqueCode = $params['unique_code'];
        $service    = $params['service'];
        $amount     = $params['amount'];
        $validTime  = $params['valid_time'];

        // signature: md5(key . unique_code . service . amount . valid_time . 'NewTransaction')
        $signature = md5($apiKey . $uniqueCode . $service . $amount . $validTime . 'NewTransaction');

        $body = [
            'key'          => $apiKey,
            'request'      => 'new',
            'unique_code'  => $uniqueCode,
            'service'      => $service,
            'amount'       => $amount,
            'note'         => $params['note'] ?? 'Topup saldo Maitri',
            'valid_time'   => $validTime,
            'type_fee'     => 1, // fee ditanggung user
            'payment_guide'=> false,
            'callback_count' => 3,
        ];

        if (!empty($params['return_url'])) {
            $body['return_url'] = $params['return_url'];
        }

        $body['signature'] = $signature;

        $response = Http::asForm()->post($baseUrl . '/', $body);

        if (! $response->successful()) {
            throw new \RuntimeException('Paydisini HTTP error: '.$response->status());
        }

        $json = $response->json();

        if (!isset($json['success']) || $json['success'] !== true) {
            throw new \RuntimeException('Paydisini API error: '.($json['msg'] ?? 'unknown'));
        }

        return $json;
    }
}
