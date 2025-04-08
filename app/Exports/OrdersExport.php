<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Usuario',
            'Departamento',
            'Proveedor',
            'Items',
            'Total',
            'Estado'
        ];
    }

    public function map($order): array
    {
        $items = $order->items->map(function($item) {
            return $item->description . ' (' . $item->quantity . ' x $' . number_format($item->unit_price, 2) . ')';
        })->join("\n");

        return [
            $order->created_at->format('d/m/Y'),
            $order->user->name,
            $order->user->department,
            $order->supplier ? $order->supplier->name : $order->other_supplier,
            $items,
            '$' . number_format($order->total, 2),
            ucfirst($order->status)
        ];
    }
}
