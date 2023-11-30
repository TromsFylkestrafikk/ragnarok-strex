<?php

namespace Ragnarok\Strex\Sinks;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
    public function fetch(string $id): int
    {
        $content = gzencode(StrexTransactions::getTransactions($id));
        $file = $this->strexFiles->toFile($this->chunkFilename($id), $content);
        return $file ? $file->size : 0;
    }

    /**
     * @inheritdoc
     */
    public function getChunkVersion(string $id): string
    {
        return $this->strexFiles->getFile($this->chunkFilename($id))->checksum;
    }

    /**
     * @inheritdoc
     */
    public function getChunkFiles(string $id): Collection
    {
        return $this->strexFiles->getFilesLike($this->chunkFilename($id));
    }

    /**
     * @inheritdoc
     */
    public function removeChunk(string $id): bool
    {
        $this->strexFiles->rmFile($this->chunkFilename($id));
        return true;
    }

    /**
     * @inheritdoc
     */
    public function import(string $id): int
    {
        $content = gzdecode($this->strexFiles->getContents($this->chunkFilename($id)));
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
