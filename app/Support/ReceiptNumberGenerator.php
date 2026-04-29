<?php

namespace App\Support;

use App\Models\Sale;

class ReceiptNumberGenerator
{
    /**
     * Generate the next receipt number for today. Must be called inside DB::transaction().
     */
    public static function next(): string
    {
        $prefix = 'RCP-'.now()->format('Ymd').'-';

        $last = Sale::query()
            ->where('receipt_number', 'like', $prefix.'%')
            ->orderByDesc('receipt_number')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($last !== null) {
            $suffix = (int) substr($last->receipt_number, -3);
            $next = $suffix + 1;
        }

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
