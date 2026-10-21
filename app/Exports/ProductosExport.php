<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ProductosExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $productos;
    protected $ventas_por_producto;
    protected $startDate;
    protected $endDate;

    public function __construct($productos, $ventas_por_producto, $startDate = null, $endDate = null)
    {
        $this->productos = $productos;
        $this->ventas_por_producto = $ventas_por_producto;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        // dd($this->startDate, $this->endDate);
    }

    public function view(): View
    {
        return view('informe.report', [
            'productos' => $this->productos,
            'ventas_por_producto' => $this->ventas_por_producto,
            'startDate' => $this->startDate ?? 'No especificada',
            'endDate' => $this->endDate ?? 'No especificada',
        ]);
    }

    // public function collection()
    // {
    //     return Producto::all();
    // }
}
