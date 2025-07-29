<?php

namespace App\Http\Controllers;

use App\Models\Pricing;
use App\Services\PaymentService;
use App\Services\PricingService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FrontController extends Controller
{
    protected $transactionService;
    protected $paymentService;
    protected $pricingService;

    public function __construct(PaymentService $paymentService, TransactionService $transactionService, PricingService $pricingService)
    {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
        $this->pricingService = $pricingService;
    }

    public function index()
    {
        return view('front.index');
    }

    public function pricing()
    {
        $pricing_packages = $this->pricingService->getAllPackages();
        $user = Auth::user();
        return view('front.pricing', compact('pricing_packages', 'user'));
    }

    public function checkout(Pricing $pricing)
    {
        $checkoutData = $this->transactionService->prepareCheckout($pricing);

        if ($checkoutData['alreadySubscribed']) {
            return redirect()->route('front.pricing')->with('error', 'You are already subscribed to this plan.');
        }

        return view('front.checkout', $checkoutData);
    }

    public function paymentStoreMidtrans()
    {
        try {
            //code...
            $pricingId = session()->get('pricing_id');

            if (!$pricingId) {
                return response()->json(['error' => 'No Pricing data found in the session.'], 400);
            }

            $snapToken = $this->paymentService->createPayment($pricingId);

            if (!$snapToken) {
                return response()->json(['error' => 'Failed to generate Snap token.'], 500);
            }
            // mengirimkan snap token ke view
            return response()->json(['snap_token' => $snapToken], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment Failed : ' . $e->getMessage()], 500);
        }
    }

    public function paymentMidtransNotification(Request $request)
    {
        try{
            $transactionStatus = $this->paymentService->handlePaymentNotification();

            if(!$transactionStatus) {
                return response()->json(['error' => 'Invalid notification data.'], 500);
            }

            //create record in table databse Transaction
            return response()->json(['status' => $transactionStatus], 200);
        }
        catch (\Exception $e){
            Log::error('Failed to handle payment notification: ', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to handle payment notification: '], 500);
        }
    }

    public function checkout_success()
    {
        $pricing = $this->transactionService->getRecentPricing();

        if(!$pricing){
            return redirect()->route('front.pricing')->with('error', 'No recent pricing found.');
        }

        return view('front.checkout_success', compact('pricing'));
    }
}
