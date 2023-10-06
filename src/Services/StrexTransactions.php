<?php

namespace Ragnarok\Strex\Services;

use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\DB;
use Ragnarok\Sink\Traits\LogPrintf;

class StrexTransactions
{
	use LogPrintf;

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
        $count = 0;
        while ($rows) {
            $values = $this->processCsvRow(rtrim(array_shift($rows)));
            if (count($headers) === count($values)) {
                $this->insertTransaction($dateStr, array_combine($headers, $values));
                $count += 1;
            }
        }
        $this->debug('Imported %d transactions', $count);
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
            'age'                   => is_numeric($row['Age']) ? intval($row['Age']) : null,
            'business_model'        => $row['BusinessModel'],
            'channel_id'            => $row['ChannelId'],
            'correlation_id'        => $row['CorrelationId'],
            'created'               => $row['Created'],
            'handling_company'      => $row['HandlingCompany'],
            'handling_company_info' => $row['HandlingCompanyDescription'],
            'invoice_text'          => $row['InvoiceText'],
            'is_restricted'         => $row['IsRestricted'] ? $row['IsRestricted'] !== 'False' : null,
            'is_stop_message'       => $row['IsStopMessage'] ? $row['IsStopMessage'] !== 'False' : null,
            'keyword'               => $row['Keyword'],
            'keyword_id'            => $row['KeywordId'],
            'merchant_id'           => $row['MerchantId'],
            'message_content'       => $row['Content'],
            'message_parts'         => intval($row['MessageParts']),
            'operator'              => $row['Operator'],
            'price'                 => is_numeric($row['Price']) ? floatval($row['Price']) : null,
            'processed'             => $row['Processed'],
            'properties'            => $row['Properties'],
            'recipient'             => $row['Recipient'],
            'recipient_prefix'      => $row['RecipientPrefix'],
            'result_code'           => is_numeric($row['ResultCode']) ? intval($row['ResultCode']) : null,
            'result_info'           => $row['ResultDescription'],
            'send_time'             => $row['SendTime'],
            'sender'                => $row['Sender'],
            'sender_prefix'         => $row['SenderPrefix'],
            'service_code'          => is_numeric($row['ServiceCode']) ? intval($row['ServiceCode']) : null,
            'session_id'            => $row['SessionId'],
            'smsc_transaction_id'   => $row['SmscTransactionId'],
            'status_code'           => $row['StatusCode'],
            'status_code_info'      => $row['DetailedStatusCode'],
            'tags'                  => $row['Tags'],
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
