<?php

namespace App\Services;

use App\Helpers\TransactionHelper;
use App\Models\Pricing;
use App\Repositories\PricingRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentService
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

    public function createPayment(int $pricingId) //: string  return type berupa string
    {
        $user = Auth::user();

        // jika tidak ingin menggunakan repository pengambilan data dari model,bisa langsung seperti ini
        // $pricing = Pricing::findorFail($pricingId);

        $pricing = $this->pricingRepository->findById($pricingId);

        $tax = 0.11;
        $totalTax = $pricing->price * $tax;
        $grandTotal = $pricing->price + $totalTax;

        $params = [
            'transaction_details' => [
                'order_id' => TransactionHelper::generateUniqueTrxId(),
                'gross_amount' => (int) $grandTotal,
            ],
            'item_details' => [
                [
                    'id' => $pricing->id,
                    'price' => (int) $pricing->price,
                    'quantity' => 1,
                    'name' => $pricing->name,
                ],
                [
                    'id' => 'tax',
                    'price' => (int) $totalTax,
                    'quantity' => 1,
                    'name' => 'PPN 11%',
                ],
            ],
            'custom_field1' => $user->id,
            'custom_field2' => $pricingId,
        ];

        return $this->midtransService->getSnapToken($params);
    }

    public function handlePaymentNotification(): array
    {
        $notification = $this->midtransService->handleNotification();

        if (in_array($notification['transaction_status'], ['capture', 'settlement'])) {
            $pricing = Pricing::findorFail($notification['custom_field2']);
            // $pricing = $this->pricingRepository->findById($notification['custom_field2']);
            $this->createTransaction($notification, $pricing);
        }

        return $notification['transaction_status'];
    }

    public function createTransaction(array $notification, Pricing $pricing)
    {
        $startedAt = now();
        $endedAt = $startedAt->copy()->addMonth($pricing->duration);

        $transactionData = [
            'user_id' => $notification['custom_field1'],
            'pricing_id' => $notification['custom_field2'],
            'sub_total_amount' => $pricing->price,
            'total_tax_amount' => $pricing->price * 0.11,
            'grand_total_amount' => $notification['grass_amount'],
            'payment_type' => 'midtrans',
            'is_paid' => true,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
        $this->transactionRepository->create($transactionData);

        Log::info('Transaction successfully created :'. $notification['order_id']);
    }
}
