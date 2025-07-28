<?php

namespace App\Http\Controllers;

use App\Repositories\PricingRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    protected $midtransService;
    protected $pricingRepository;
    protected $transactionRepository;

    public function __construct(MidtransService $midtransService, PricingRepositoryInterface $pricingRepository, TransactionRepositoryInterface $transactionRepository)
    {
        $this->pricingRepository = $pricingRepository;
        $this->transactionRepository = $transactionRepository;
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        return view('front.index');
    }

    public function pricing()
    {
        $pricing_packages = $this->pricingRepository->getAll();
        $user = Auth::user();
        return view('front.pricing', compact('pricing_packages','user'));
    }
}
