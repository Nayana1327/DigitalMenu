<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrderExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings():array{
        return[
            'Order No.',
            'Table No',
            'Date of Order',
            'Waiter Name',
            'Food Item Ordered',
            'Total Amount',
            'Status'
        ];
    } 
    
    public function collection()
    {
        return Order::all();
    }
}
