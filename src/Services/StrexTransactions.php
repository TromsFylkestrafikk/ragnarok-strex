<?php

namespace Ragnarok\Strex\Services;

use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Ragnarok\Sink\Traits\LogPrintf;

class StrexTransactions
{
    use LogPrintf;

    protected $transactionCount = 0;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient = null;

    public function __construct()
    {
        $this->logPrintfInit('[Strex Transactions]: ');
        $this->httpClient = new GuzzleHttpClient();
    }

    /**
     * Get all transactions for a single day.
     *
     * @param string $dateStr
     *
     * @return string The transaction data in CSV format.
     */
    public function getTransactions($dateStr): string
    {
        $downloadUrl = sprintf(config('ragnarok_strex.download_url'), $dateStr);
        $response = $this->httpClient->get($downloadUrl, [
            'headers' => ['x-ApiKey' => config('ragnarok_strex.api_key')]
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * Import transactions to DB.
     *
     * @param string $dateStr
     * @param string $csvData
     *
     * @return $this
     */
    public function import($dateStr, $csvData)
    {
        $rows = explode(PHP_EOL, $csvData);
        $headers = explode(',', rtrim(array_shift($rows)));
        $this->transactionCount = 0;
        while ($rows) {
            $values = $this->processCsvRow(rtrim(array_shift($rows)));
            if (count($headers) === count($values)) {
                $this->insertTransaction($dateStr, array_combine($headers, $values));
                $this->transactionCount++;
            }
        }
        $this->debug('Imported %d transactions', $this->transactionCount);
        return $this;
    }

    /**
     * Delete transactions from DB.
     *
     * @param string $dateStr
     *
     * @return $this
     */
    public function delete($dateStr)
    {
        $this->debug("Purging imported data for %s", $dateStr);
        DB::table('strex_transactions')->where('transaction_date', $dateStr)->delete();
        return $this;
    }

    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }

    /**
     * Insert a single transaction to DB.
     *
     * @param string $dateStr The transaction date.
     * @param array $row The transaction data.
     *
     * @return bool True on success.
     */
    protected function insertTransaction($dateStr, $row)
    {
        DB::table('strex_transactions')->insert([
            'created'               => new Carbon($row['Created']),
            'invoice_text'          => $row['InvoiceText'],
            'keyword'               => $row['Keyword'],
            'message_content'       => $row['Content'],
            'message_parts'         => intval($row['MessageParts']),
            'operator'              => $row['Operator'],
            'price'                 => is_numeric($row['Price']) ? floatval($row['Price']) : null,
            'recipient'             => $row['Recipient'],
            'recipient_prefix'      => $row['RecipientPrefix'],
            'result_code'           => is_numeric($row['ResultCode']) ? intval($row['ResultCode']) : null,
            'result_info'           => $row['ResultDescription'],
            'send_time'             => new Carbon($row['SendTime']),
            'sender'                => $row['Sender'],
            'sender_prefix'         => $row['SenderPrefix'],
            'smsc_transaction_id'   => $row['SmscTransactionId'],
            'status_code'           => $row['StatusCode'],
            'status_code_info'      => $row['DetailedStatusCode'],
            'transaction_date'      => $dateStr,
            'transaction_id'        => $row['TransactionId'],
        ]);
        return true;
    }

    /**
     * Retrieve values from a CSV data row.
     *
     * @param string $csvRow The transaction data.
     *
     * @return array string The processed values.
     */
    protected function processCsvRow($csvRow)
    {
        $row = str_replace('""', '', $csvRow);
        $isSeparator = true;
        foreach (str_split($row) as $k => $ch) {
            if ($ch === '"') {
                $isSeparator = !$isSeparator;
                continue;
            }
            if ($isSeparator && ($ch === ',')) $row[$k] = '|';
        }
        return explode('|', $row);
    }
}
