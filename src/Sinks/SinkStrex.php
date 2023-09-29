<?php

namespace Ragnarok\Strex\Sinks;

//use Exception;
use Illuminate\Support\Carbon;
use Ragnarok\Sink\Sinks\SinkBase;
use Ragnarok\Sink\Traits\LogPrintf;

class SinkStrex extends SinkBase
{
    use LogPrintf;

    public static $id = "strex";
    public static $title = "Strex";

    public function __construct()
    {
        $this->logPrintfInit('[SinkStrex]: ');
    }

    /**
     * @inheritdoc
     */
    public function getFromDate(): Carbon
    {
        return new Carbon('2023-09-01');
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
        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeChunk($id): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function import($id): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteImport($id): bool
    {
        return true;
    }
}
