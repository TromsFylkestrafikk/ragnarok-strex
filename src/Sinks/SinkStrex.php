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
    public function destinationTables(): array
    {
        return [
            'strex_transactions' => 'All transaction info',
        ];
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

    /**
     * @inheritdoc
     */
    public function filenameToChunkId(string $filename): string|null
    {
        $matches = [];
        $hits = preg_match('|(?P<date>\d{4}-\d{2}-\d{2})\.csv\.gz$|', $filename, $matches);
        return $hits ? $matches['date'] : null;
    }

    protected function chunkFilename(string $id): string
    {
        return $id . '.csv.gz';
    }
}
