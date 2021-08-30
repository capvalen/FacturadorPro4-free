<?php

use App\Models\Tenant\Item;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBarcodeColumnToItemsTable extends Migration
{
    protected $updateAt = '2021-01-08 10:00:00';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $items = Item::whereNull('barcode')
            ->get();

        foreach ($items as $item) {
            if ($item->internal_id) {
                $item->barcode = $item->internal_id;
            } else {
                $item->barcode = str_pad($item->id, 12, '0', STR_PAD_LEFT);
            }
            $item->updated_at = $this->updateAt;
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Item::where('updated_at', $this->updateAt)
            ->update([
                'barcode' => null
            ]);
    }
}
