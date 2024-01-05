<?php

namespace Ragnarok\Strex\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Sink\Services\LocalFile;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;
use Ragnarok\Strex\Facades\StrexTransactions;

class SinkStrex extends SinkBase
{
    public static $id = "strex";
    public static $title = "Strex";

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
        return LocalFile::createFromFilename(self::$id, $this->chunkFilename($id))
            ->put(gzencode(StrexTransactions::getTransactions($id)))
            ->getFile();
    }

    /**
     * @inheritdoc
     */
    public function import(string $id, SinkFile $file): int
    {
        $local = new LocalFile(self::$id, $file);
        $content = gzdecode($local->get());
        return StrexTransactions::import($id, $content)->getTransactionCount();
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        StrexTransactions::delete($id);
        return true;
    }



    protected function chunkFilename(string $id): string
    {
        return $id . '.csv.gz';
    }
}
