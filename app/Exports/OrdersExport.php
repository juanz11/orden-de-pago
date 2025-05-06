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
            'Total USD',
            'Total BS',
            'Estado'
        ];
    }

    public function map($order): array
    {
        $items = $order->items->map(function($item) {
            return $item->description . ' (' . $item->quantity . ' x $' . number_format($item->unit_price, 2) . ')';
        })->join("\n");

        $exchange_rate = config('app.exchange_rate', 35.50); // Tasa de cambio por defecto
        $total_usd = $order->total / $exchange_rate; // Convertir de BSF a USD

        return [
            $order->created_at->format('d/m/Y'),
            $order->user->name,
            $order->user->department,
            $order->supplier ? $order->supplier->name : $order->other_supplier,
            $items,
            '$' . number_format($total_usd, 2),
            'BS ' . number_format($order->total, 2),
            ucfirst($order->status)
        ];
    }
}
