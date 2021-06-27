<?php

namespace App\Exports;

use App\factura;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class facturaReporte implements FromQuery
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function __construct(String $nit, String $fecha, String $total){
        $this->nit = $nit;
        $this->fecha = $fecha;
        $this->total = $total;
    }


    public function query()
    {
        return factura::query()->where(['nitEmisor' => $this->nit, 'fechaHoraEmision' => $this->fecha, 'grantotal' => $this->total]);
    }
}
