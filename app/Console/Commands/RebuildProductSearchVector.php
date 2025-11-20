<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildProductSearchVector extends Command
{
    protected $signature = 'search:rebuild';
    protected $description = 'Rebuild search_vector column for all products';

    public function handle()
    {
        $this->info('Rebuilding search_vector for all products...');

        DB::statement("
    UPDATE products SET search_vector =
        setweight(to_tsvector('simple', coalesce(title, '')), 'A') ||
        setweight(to_tsvector('simple', coalesce(description::text, '')), 'B') ||
        setweight(to_tsvector('simple', coalesce(spec::text, '')), 'C')
");

        $this->info('✅ Done! Search vector has been rebuilt.');
    }
}
