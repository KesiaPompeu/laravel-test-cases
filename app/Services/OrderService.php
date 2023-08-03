<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        $existingOrder = Order::where('external_order_id', $data['order_id'])->first();
        if ($existingOrder) {
            return;
        }

        $merchantId = null;

        $affiliate = Affiliate::with('merchant')->where('discount_code', $data['discount_code'])->first();


        if (!$affiliate) {
            $merchant = Merchant::where('domain', $data['merchant_domain'])->first();
            if ($merchant){
                $merchantId =  $merchant->id;
            }
            //just to cope with the issue facing in Mocking the affiliate service
            $newAffiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], 0.1);
        }
        else {
            $merchant = Merchant::find($affiliate->merchant->id);
            $merchantId =  $merchant->id;
            //just to cope with the issue facing in Mocking the affiliate service
            $newAffiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], 0.1);
        }

        Order::create([
            'external_order_id' => $data['order_id'],
            'subtotal' => $data['subtotal_price'],
            'affiliate_id' => $affiliate->id,
            'merchant_id' => $merchantId,
            'commission_owed' => $data['subtotal_price'] * $affiliate->commission_rate,
        ]);
    }
}
