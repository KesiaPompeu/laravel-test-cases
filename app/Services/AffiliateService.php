<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     * @throws AffiliateCreateException
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {

        if ($this->isEmailInUse($email)) {
            throw new AffiliateCreateException("Email is already in use as a merchant or affiliate.");
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'type' => User::TYPE_AFFILIATE,
        ]);
        $merchant->user()->associate($user);
        $merchant->save();

        $affiliate = new Affiliate();
        $affiliate->merchant_id = $merchant->id;
        $affiliate->user_id = $user->id;
        $affiliate->commission_rate = $commissionRate;


        $discountCodeData = $this->apiService->createDiscountCode($merchant);
        $affiliate->discount_code = $discountCodeData['code'];
        $affiliate->save();


        Mail::to($user->email)->send(new AffiliateCreated($affiliate));

        return $affiliate;
    }

    /**
     * Check if the given email is already in use by a merchant or affiliate.
     *
     * @param  string $email
     * @return bool
     */
    protected function isEmailInUse(string $email): bool
    {
        return Merchant::whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->orWhereHas('affiliate.user', function ($query) use ($email) {
            $query->where('email', $email);
        })->exists();
    }
}

