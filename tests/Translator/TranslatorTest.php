<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Tests\Translator;

use Gpupo\CommonSchema\TranslatorDataCollection;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;
use Gpupo\MercadopagoSdk\Translator\Translator;

/**
 * @coversDefaultClass \App\Entity\Catalog\Product
 */
class TranslatorTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProvider
     *
     * @param mixed $data
     */
    public function testTranslateFrom($data)
    {
        $translator = new Translator();
        $translator->setNative($data);
        $payment = $translator->export();
        //dump($payment);

        $this->assertSame($payment->getOperationType(), $data->get('operation_type'));
    }

    public function dataProvider()
    {
        $data = json_decode('{
    			"id": 123,
    			"date_created": "2011-09-20T00:00:00.000-04:00",
    			"date_approved": null,
    			"date_last_updated": "2011-10-19T16:44:34.000-04:00",
    			"money_release_date": "2011-10-04T17:32:49.000-04:00",
    			"operation_type": "regular_payment",
    			"collector_id": 456,
          "external_reference": "Seller reference",
    			"description": "Payment description",
    			"transaction_amount": 2,
          "currency_id": "BRL",
    			"status": "cancelled",
    			"status_detail": "expired",
    			"payment_type_id": "ticket",
          "call_for_authorize_id": "",
          "payment_method_id": "credit",
          "issuer_id": "",
          "installments": "2",
          "transaction_amount_refunded": 0,
          "captured": false,
          "statement_descriptor": "",
          "notification_url": "",
          "refunds": "",
    			"payer": {
    				"id": "789",
    				"first_name": "Payer First name",
    				"last_name": "Payer Last name",
    				"phone": {
    					"area_code": "0123",
    					"number": "4567890",
    					"extension": null
    				},
    				"email": "payer@email.com"
    			},
    			"transaction_details": {
    				"total_paid_amount": 2
    			},
          "collector": {
              "id": "1"
          },
          "fee_details": {
            "amount" : 10
          },
          "card": {},
          "order": {}
    		}', true);

        $collection = new TranslatorDataCollection($data);

        return [[$collection]];
    }
}
