<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $departments = User::distinct('department')->pluck('department')->filter();
        return view('reports.index', compact('departments'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department' => 'nullable|string'
        ]);

        $query = Order::with(['user', 'supplier', 'items'])
            ->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);

        if ($request->department) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $orders = $query->get();

        $data = [
            'orders' => $orders,
            'start_date' => Carbon::parse($request->start_date)->format('d/m/Y'),
            'end_date' => Carbon::parse($request->end_date)->format('d/m/Y'),
            'department' => $request->department ?: 'Todos los departamentos',
            'total' => $orders->sum('total')
        ];

        if ($request->format === 'pdf') {
            $pdf = PDF::loadView('reports.pdf', $data);
            return $pdf->download('reporte-ordenes.pdf');
        }

        return view('reports.show', $data);
    }
}
