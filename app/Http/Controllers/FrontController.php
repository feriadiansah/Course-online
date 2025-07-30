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
}
