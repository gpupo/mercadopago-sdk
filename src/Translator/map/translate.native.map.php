<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

$expands = [
    'payer' => $native->get('payer'),
    'external_reference' => $native->get('external_reference'),
    'metadata' => $native->get('metadata'),
    'transaction_amount_refunded' => $native->get('transaction_amount_refunded'),
    'transaction_details' => $native->get('transaction_details'),
    'money_release_date' => $native->get('money_release_date'),
    'captured' => $native->get('captured'),
    'card' => $native->get('card'),
    'statement_descriptor' => $native->get('statement_descriptor'),
    'notification_url' => $native->get('notification_url'),
    'refunds' => $native->get('refunds'),
    'order' => $native->get('order'),
];

 $marketplaceFee = 0;
if ($native->get('fee_details')) {
    foreach ($native->get('fee_details') as $fee) {
        if (is_array($fee)) {
            $marketplaceFee += $fee['amount'];
        }
    }
}

 $array = [
     'operation_type' => $native->get('operation_type'),
     'payment_number' => $native->get('id'),
     'date_created' => $native->get('date_created'),
     'date_approved' => $native->get('date_approved'),
     'date_last_modified' => $native->get('date_last_updated'),
     'operation_type' => $native->get('operation_type'),
     'expands' => $expands,
     'collector' => $native->get('collector'),
     'reason' => $native->get('description'),
     'marketplace_fee' => $marketplaceFee,
     'currency_id' => $native->get('currency_id'),
     'transaction_amount' => $native->get('transaction_details')['total_paid_amount'],
     'status' => $native->get('status'),
     'status_detail' => $native->get('status_detail'),
     'authorization_code' => $native->get('call_for_authorize_id'),
     'payment_method_id' => $native->get('payment_method_id'),
     'issuer_id' => $native->get('issuer_id'),
     'payment_type' => $native->get('payment_type_id'),
     'installments' => $native->get('installments'),
 ];

 return $array;
