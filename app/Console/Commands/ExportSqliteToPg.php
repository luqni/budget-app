<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportSqliteToPg extends Command
{
    protected $signature = 'db:export-pg {--file=dump.sql}';
    protected $description = 'Export SQLite data to PostgreSQL INSERT statements';

    public function handle()
    {
        $file = $this->option('file');
        $tables = [
            'users', 
            'categories', 
            'monthly_incomes', 
            'incomes', 
            'expenses', 
            'expense_details'
        ];

        $handle = fopen($file, 'w');
        fwrite($handle, "-- PostgreSQL Data Dump from SQLite\n");
        fwrite($handle, "BEGIN;\n\n");

        foreach ($tables as $table) {
            $this->info("Exporting $table...");
            
            $rows = DB::table($table)->get();
            
            if ($rows->isEmpty()) {
                continue;
            }
            
            // Truncate first? Maybe not needed if importing to fresh DB, but useful.
            // fwrite($handle, "TRUNCATE TABLE \"$table\" CASCADE;\n");

            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $cols = array_keys($rowArray);
                $values = array_values($rowArray);
                
                $colFiles = implode(', ', array_map(fn($c) => "\"$c\"", $cols));
                
                $valFields = implode(', ', array_map(function($v) {
                    if (is_null($v)) return 'NULL';
                    if (is_bool($v)) return $v ? 'TRUE' : 'FALSE';
                    if (is_int($v) || is_float($v)) return $v;
                    // Escape single quotes for SQL
                    $v = str_replace("'", "''", $v);
                    return "'$v'";
                }, $values));

                fwrite($handle, "INSERT INTO \"$table\" ($colFiles) VALUES ($valFields);\n");
            }
            fwrite($handle, "\n");
        }

        // Fix sequence for Postgres (optional but good)
        // SELECT setval('tablename_id_seq', (SELECT MAX(id) FROM tablename));
        foreach ($tables as $table) {
             $rows = DB::table($table)->get();
             if($rows->count() > 0) {
                 fwrite($handle, "SELECT setval('{$table}_id_seq', (SELECT MAX(id) FROM \"$table\"));\n");
             }
        }

        fwrite($handle, "\nCOMMIT;\n");
        fclose($handle);

        $this->info("Export completed to $file");
    }
}
