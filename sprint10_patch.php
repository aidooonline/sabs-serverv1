<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Starting Sprint 10 Database Patch...\n";

$tables = [
    'nobs_registration',
    'account_types',
    'agent_commissions',
    'capital_accounts',
    'capital_account_transactions',
    'central_loan_accounts',
    'commission_payouts',
    'coupons',
    'documents',
    'document_folders',
    'document_types',
    'fund_transfers',
    'invoices',
    'invoice_items',
    'loan_application_requirements',
    'loan_default_logs',
    'loan_fees',
    'loan_products',
    'loan_repayment_schedules',
    'nobs_savings_accounts',
    'nobs_susu_cycles',
    'nobs_user_account_numbers',
    'user_defualt_views'
];

foreach ($tables as $tableName) {
    echo "Checking table: $tableName... ";
    
    if (Schema::hasTable($tableName)) {
        if (!Schema::hasColumn($tableName, 'comp_id')) {
            echo "Adding comp_id... ";
            try {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_' . $table->getTable() . '_comp_id');
                });
                
                // Backfill
                DB::table($tableName)->update(['comp_id' => 2]);
                echo "Done.\n";
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "comp_id exists. ";
            // Check index
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes($tableName);
            $indexName = 'idx_' . $tableName . '_comp_id';
            // Simple check if index key exists (ignoring precise name match for now, just ensure indexed)
            // But let's try to add if not exists by name
            $hasIndex = false;
            foreach ($indexes as $idx) {
                if (in_array('comp_id', $idx->getColumns())) {
                    $hasIndex = true;
                    break;
                }
            }
            
            if (!$hasIndex) {
                 echo "Adding Index... ";
                 try {
                     Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                        $table->index('comp_id', 'idx_' . $tableName . '_comp_id');
                     });
                     echo "Done.\n";
                 } catch (\Exception $e) {
                     echo "Index Error: " . $e->getMessage() . "\n";
                 }
            } else {
                echo "Index exists.\n";
            }
        }
    } else {
        echo "Table not found.\n";
    }
}

echo "Patch Complete.\n";

