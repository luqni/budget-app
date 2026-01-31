<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\Income;
use Illuminate\Support\Facades\Schema;

class ImportOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import-sqlite {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from old SQLite database file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return;
        }

        $this->info("Starting import from: $path");

        // 1. Setup Connection to Old DB
        config(['database.connections.old_sqlite' => [
            'driver' => 'sqlite',
            'database' => $path,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);

        try {
            $oldDb = DB::connection('old_sqlite');
            $oldDb->getPdo();
        } catch (\Exception $e) {
            $this->error("Could not connect to old database: " . $e->getMessage());
            return;
        }

        $this->info("Connected to old database.");

        DB::beginTransaction();
        
        // Disable mass assignment protection
        User::unguard();
        Expense::unguard();
        ExpenseDetail::unguard();
        Income::unguard();

        try {
            // Mapping Arrays: Old ID => New ID
            $userMap = [];
            $categoryMap = [];

            // --- 1. IMPORT USERS ---
            $this->info("Importing Users...");
            $oldUsers = $oldDb->table('users')->get();
            
            foreach ($oldUsers as $oldUser) {
                // Check if user exists by email
                $existingUser = User::where('email', $oldUser->email)->first();

                if ($existingUser) {
                    $this->line("  - User exists: {$oldUser->email} (ID: {$existingUser->id})");
                    $userMap[$oldUser->id] = $existingUser->id;
                } else {
                    $newUser = User::create([
                        'name' => $oldUser->name,
                        'email' => $oldUser->email,
                        'password' => $oldUser->password, // Password hash should work directly
                        'created_at' => $oldUser->created_at,
                        'updated_at' => $oldUser->updated_at,
                    ]);
                    $this->info("  + Created User: {$newUser->email} (ID: {$newUser->id})");
                    $userMap[$oldUser->id] = $newUser->id;
                }
            }

            // --- 2. IMPORT CATEGORIES ---
            $this->info("Importing Categories...");
            // Assuming categories table exists in old DB. If not, we might skip.
            if (Schema::connection('old_sqlite')->hasTable('categories')) {
                $oldCategories = $oldDb->table('categories')->get();
                foreach($oldCategories as $oldCat) {
                    // Try to match by name
                    $existingCat = Category::where('name', $oldCat->name)->first(); // Assuming unique names generally
                    
                    if ($existingCat) {
                         $categoryMap[$oldCat->id] = $existingCat->id;
                    } else {
                        // Create new
                         $newCat = Category::create([
                            'name' => $oldCat->name,
                            'icon' => $oldCat->icon ?? '📝',
                            'color' => $oldCat->color ?? '#6c757d',
                         ]);
                         $categoryMap[$oldCat->id] = $newCat->id;
                    }
                }
            } else {
                $this->warn("Table 'categories' not found in old DB. Skipping category mapping.");
            }


            // --- 3. IMPORT EXPENSES ---
            $this->info("Importing Expenses...");
            $oldExpenses = $oldDb->table('expenses')->orderBy('created_at')->get();
            $expenseCount = 0;

            foreach ($oldExpenses as $oldExp) {
                if (!isset($userMap[$oldExp->user_id])) {
                    $this->warn("  ! Skipping expense ID {$oldExp->id}: User ID {$oldExp->user_id} not mapped.");
                    continue;
                }

                $newUserId = $userMap[$oldExp->user_id];
                $newCatId = null;
                if (isset($oldExp->category_id) && isset($categoryMap[$oldExp->category_id])) {
                    $newCatId = $categoryMap[$oldExp->category_id];
                }

                $newExp = Expense::create([
                    'user_id' => $newUserId,
                    'category_id' => $newCatId,
                    'amount' => $oldExp->amount,
                    'note' => $oldExp->note,
                    'date' => $oldExp->date ?? $oldExp->created_at, // Handle potential date column
                    'month' => $oldExp->month,
                    'created_at' => $oldExp->created_at,
                    'updated_at' => $oldExp->updated_at,
                ]);

                // Import Details for this expense
                // Assuming 'expense_details' table exists and has foreign key 'expense_id'
                if (Schema::connection('old_sqlite')->hasTable('expense_details')) {
                    $oldDetails = $oldDb->table('expense_details')->where('expense_id', $oldExp->id)->get();
                    foreach ($oldDetails as $oldDetail) {
                        ExpenseDetail::create([
                            'expense_id' => $newExp->id,
                            'name' => $oldDetail->name,
                            'qty' => $oldDetail->qty,
                            'price' => $oldDetail->price,
                            'is_checked' => $oldDetail->is_checked ?? false,
                            'created_at' => $oldDetail->created_at,
                            'updated_at' => $oldDetail->updated_at,
                        ]);
                    }
                }

                $expenseCount++;
            }
            $this->info("Imported $expenseCount expenses.");


             // --- 4. IMPORT INCOMES ---
             $this->info("Importing Incomes...");
             if (Schema::connection('old_sqlite')->hasTable('incomes')) {
                 $oldIncomes = $oldDb->table('incomes')->get();
                 foreach($oldIncomes as $oldInc) {
                     if (!isset($userMap[$oldInc->user_id])) continue;
 
                     // Check existing for this month to avoid dupe?
                     // Or just insert all. Let's just insert/update logic.
                     // Simple implementation: Create new.

                     Income::create([
                         'user_id' => $userMap[$oldInc->user_id],
                         'amount' => $oldInc->amount,
                         'month' => $oldInc->month,
                         'created_at' => $oldInc->created_at,
                         'updated_at' => $oldInc->updated_at,
                     ]);
                 }
                 $this->info("Imported incomes.");
             }

            DB::commit();
            $this->info("Migration completed successfully! 🎉");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during migration: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
    }
}
