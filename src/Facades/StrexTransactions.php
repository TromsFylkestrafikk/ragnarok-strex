<?php

namespace Ragnarok\Strex\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Strex\Services\StrexTransactions as StrexTransactionsService;

/**
 * @method static string getTransactions(string $dateStr)
 * @method static \Ragnarok\Strex\Services\StrexTransactions import(string $dateStr, string $csvData)
 * @method static \Ragnarok\Strex\Services\StrexTransactions delete(string $dateStr)
 * @method static int getTransactionCount()
 * @method static void logPrintfInit(void $prefix = '', void ...$prefixArgs)
 *
 * @see \Ragnarok\Strex\Services\StrexTransactions
 */
class StrexTransactions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return StrexTransactionsService::class;
    }
}
