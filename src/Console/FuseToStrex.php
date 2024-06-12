<?php

namespace Ragnarok\Strex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FuseToStrex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ragnarok:fuse2strex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update chunk metadata based on non-seen imported data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endDate = DB::table('ragnarok_chunks')
            ->where('sink_id', 'strex')
            ->whereNot('fetch_status', 'finished')
            ->whereNull('sink_file_id')
            ->where('import_status', 'new')->max('chunk_id');

        $candiDates = DB::table('strex_transactions')
            ->select('transaction_date', DB::raw('count(*) as count'))
            ->whereDate('transaction_date', '<=', $endDate)
            ->groupBy('transaction_date')
            ->orderBy('transaction_date')
            ->get()
            ->keyBy('transaction_date');

        $chunks = DB::table('ragnarok_chunks')
            ->where('sink_id', 'strex')
            ->whereIn('chunk_id', $candiDates->pluck('transaction_date'))->get();
        $now = new Carbon();
        foreach ($chunks as $chunk) {
            $meta = $candiDates[$chunk->chunk_id];
            $this->line(sprintf("Chunk %s (%s): %d records", $chunk->id, $chunk->chunk_id, $meta->count));
            DB::table('ragnarok_chunks')
                ->where('id', $chunk->id)
                ->update([
                    'import_status' => 'finished',
                    'import_size' => $meta->count,
                    'imported_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
        }
        $this->info(sprintf('Update import status on %d chunks', $chunks->count()));
    }
}
