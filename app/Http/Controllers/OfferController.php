<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\OfferRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    // GET /offers 
    public function index()
    {
        $offers = Offer::active()->orderBy('valid_until')->get();
        return view('offers.index', compact('offers'));
    }

    // POST /offers/redeem 
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code  = strtoupper(trim($request->code));
        $offer = Offer::where('code', $code)->first();

        // Code not found
        if (!$offer) {
            return back()->with('offer_error', 'Invalid promo code. Please check and try again.');
        }

        // Offer expired or inactive
        if (!$offer->isValid()) {
            return back()->with('offer_error', 'This offer has expired or is no longer available.');
        }

        // Already redeemed by this user
        if ($offer->isRedeemedBy(Auth::id())) {
            return back()->with('offer_error', 'You have already redeemed this offer.');
        }

        // Redeem it
        OfferRedemption::create([
            'offer_id'    => $offer->id,
            'user_id'     => Auth::id(),
            'redeemed_at' => now(),
        ]);

        // Increment used count
        $offer->increment('used_count');

        return back()->with('offer_success', [
            'title'    => $offer->title,
            'discount' => $offer->discount_percent,
            'code'     => $offer->code,
        ]);
    }
}
