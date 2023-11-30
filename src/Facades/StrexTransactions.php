<?php

namespace Ragnarok\Strex\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Strex\Services\StrexTransactions as StrexTransactionsService;

/**
 * @method static string getTransactions(string $dateStr)
 * @method static StrexTransactionsService import(string $dateStr, string $csvData)
 * @method static StrexTransactionsService delete(string $dateStr)
 * @method int getTransactionCount()
 */
class StrexTransactions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return StrexTransactionsService::class;
    }
}
