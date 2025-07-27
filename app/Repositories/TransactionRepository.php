<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function findBookingId(string $bookingId) //: ?Transaction
    {
        return Transaction::where('booking_trx_id', $bookingId)->first();
    }

    // create new Transaction in table database Transactions
    public function create(array $data) //: Transaction
    {
        return Transaction::create($data);
    }

    public function getUserTransactions(int $userId) //: Collection
    {
        return Transaction::with('pricing')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
