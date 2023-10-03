<?php

namespace Ragnarok\Strex\Services;

use Illuminate\Support\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
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
     * @return string
     */
    public function getTransactions($dateStr): string
    {
        $downloadUrl = sprintf(config('ragnarok_strex.download_url'), $dateStr);
        $response = $this->httpClient->get($downloadUrl, [
            'headers' => ['x-ApiKey' => config('ragnarok_strex.api_key')]
        ]);
        return $response->getBody()->getContents();
    }
}
