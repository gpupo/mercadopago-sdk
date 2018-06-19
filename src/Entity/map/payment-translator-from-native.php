<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

$foreign = [];

foreach ([
    'id' => 'payment_number',
    'currency_id' => 'currency_id',
    'status' => 'status',
    'external_reference' => 'transaction_order_id',
    'status_detail' => 'status_detail',
    'transaction_amount' => 'transaction_amount',
    'date_created' => 'date_created',
    'date_last_updated' => 'date_last_modified',
    'payment_method_id' => 'payment_method_id',
    'installments' => 'installments',
    'operation_type' => 'operation_type',
    'payment_type_id' => 'payment_type',
    'date_approved' => 'date_approved',
    'coupon_amount' => 'coupon_amount',
    'issuer_id' => 'issuer_id',
    'authorization_code' => 'authorization_code',
] as $origin => $destination) {
    $foreign[$destination] = $native->get($origin);
}

$foreign['collector'] = [
    'id' => $native->get('collector_id'),
];

$transaction = $native->get('transaction_details');
$foreign['total_paid_amount'] = $transaction['total_paid_amount'];

$foreign['transaction_net_amount'] = $transaction['net_received_amount'];

$foreign['marketplace_fee'] = 0.0;
foreach ($native->getFeeDetails() as $fee) {
    $foreign['marketplace_fee'] += $fee['amount'];
}

$foreign['overpaid_amount'] = ($foreign['transaction_amount'] - ($foreign['transaction_net_amount'] + $foreign['marketplace_fee']));
$foreign['expands'] = $native->toArray();

return $foreign;
// unmapped fields:
// 'status_code',
// 'shipping_cost',
// 'card_id',
// 'reason',
// 'activation_uri',
// 'atm_transfer_reference',
// 'coupon_id',
// 'available_actions',
// 'installment_amount',
// 'deferred_period',
