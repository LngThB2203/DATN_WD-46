<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{


    public function up(): void
    {
        $duplicates = DB::table('warehouse_products')
            ->select('warehouse_id', 'variant_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('warehouse_id', 'variant_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $row) {
            $records = DB::table('warehouse_products')
                ->where('warehouse_id', $row->warehouse_id)
                ->where('variant_id', $row->variant_id)
                ->get();

            $keep = $records->first();

            $totalQty = $records->sum('quantity');

            DB::table('warehouse_products')
                ->where('id', $keep->id)
                ->update([
                    'quantity' => $totalQty,
                ]);

            DB::table('warehouse_products')
                ->where('warehouse_id', $row->warehouse_id)
                ->where('variant_id', $row->variant_id)
                ->where('id', '!=', $keep->id)
                ->delete();
        }
    }

    public function down(): void
    {
        // Không thể rollback vì đã gộp dữ liệu
    }

};
