<?php

namespace Ragnarok\Strex\Sinks;

use Exception;
use Illuminate\Support\Carbon;
use Ragnarok\Sink\Services\LocalFiles;
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
    public function fetch($id): bool
    {
        $file = null;
        try {
            $content = gzencode(StrexTransactions::getTransactions($id));
            $file = $this->strexFiles->toFile($this->chunkFilename($id), $content);
        } catch (Exception $e) {
            $this->error("%s[%d]: %s\n, %s", $e->getFile(), $e->getLine(), $e->getMessage(), $e->getTraceAsString());
        }
        return $file ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function removeChunk($id): bool
    {
        $this->strexFiles->rmFile($this->chunkFilename($id));
        return true;
    }

    /**
     * @inheritdoc
     */
    public function import($id): bool
    {
        try {
            $content = gzdecode($this->strexFiles->getContents($this->chunkFilename($id)));
            StrexTransactions::import($id, $content);
            return true;
        } catch (Exception $e) {
            $this->error("%s[%d]: %s\n, %s", $e->getFile(), $e->getLine(), $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteImport($id): bool
    {
        try {
            StrexTransactions::delete($id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function chunkFilename($id)
    {
        return $id . '.csv.gz';
    }
}
