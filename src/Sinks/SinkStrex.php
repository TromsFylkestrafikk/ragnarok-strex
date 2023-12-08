<?php

namespace Ragnarok\Strex\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Sink\Services\LocalFiles;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;
use Ragnarok\Sink\Traits\LogPrintf;
use Ragnarok\Strex\Facades\StrexTransactions;

class SinkStrex extends SinkBase
{
    use LogPrintf;

    public static $id = "strex";
    public static $title = "Strex";

    /**
     * @var LocalFiles
     */
    protected $strexFiles = null;

    public function __construct()
    {
        $this->strexFiles = new LocalFiles(static::$id);
        $this->logPrintfInit('[SinkStrex]: ');
    }

    /**
     * @inheritdoc
     */
    public function getFromDate(): Carbon
    {
        return new Carbon('2021-10-07');
    }

    /**
     * @inheritdoc
     */
    public function getToDate(): Carbon
    {
        return today()->subDay();
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id): SinkFile|null
    {
        $content = gzencode(StrexTransactions::getTransactions($id));
        return $this->strexFiles->toFile($this->chunkFilename($id), $content);
    }

    /**
     * @inheritdoc
     */
    public function import(string $id, SinkFile $file): int
    {
        $content = gzdecode($this->strexFiles->getContents($file));
        return StrexTransactions::import($id, $content)->getTransactionCount();
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id): bool
    {
        StrexTransactions::delete($id);
        return true;
    }



    protected function chunkFilename(string $id): string
    {
        return $id . '.csv.gz';
    }
}
