<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\factura;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\facturaReporte;
use Illuminate\Support\Facades\Mail;
use App\Mail\reporteGeneral;


class jwtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function setDataXml(Request $request){

        foreach ($request->xml()->SAT as $key => $value) {

            $fel = new factura;

            $fel->codigoMoneda =  $value->DTE->DatosEmision->DatosGenerales['CodigoMoneda'];
            $fel->fechaHoraEmision = $value->DTE->DatosEmision->DatosGenerales['FechaHoraEmision'];
            $fel->tipo = $value->DTE->DatosEmision->DatosGenerales['Tipo'];
            $fel->afiliacionIVA = $value->DTE->DatosEmision->Emisor['AfiliacionIVA'];
            $fel->codigoEstablecimiento = $value->DTE->DatosEmision->Emisor['CodigoEstablecimiento'];
            $fel->correoEmisor = $value->DTE->DatosEmision->Emisor['CorreoEmisor'];
            $fel->nitEmisor = $value->DTE->DatosEmision->Emisor['NITEmisor'];
            $fel->nombreComercial = $value->DTE->DatosEmision->Emisor['NombreComercial'];
            $fel->nombreEmisor = $value->DTE->DatosEmision->Emisor['NombreEmisor'];
            $fel->direccionEmisor = $value->DTE->DatosEmision->Emisor->DireccionEmisor->Direccion;
            $fel->correoReceptor = $value->DTE->DatosEmision->Receptor->CorreoReceptor;
            $fel->idReceptor = $value->DTE->DatosEmision->Receptor['IDReceptor'];
            $fel->nombreReceptor = $value->DTE->DatosEmision->Receptor['NombreReceptor'];
            $fel->direccionReceptor = $value->DTE->DatosEmision->Receptor->DireccionReceptor->Direccion;
            $fel->cantidad = $value->DTE->DatosEmision->Items->Item->Cantidad;
            $fel->descripcion = $value->DTE->DatosEmision->Items->Item->Descripcion;
            $fel->precio = $value->DTE->DatosEmision->Items->Item->Precio;
            $fel->descuento = $value->DTE->DatosEmision->Items->Item->Descuento;
            $fel->total = $value->DTE->DatosEmision->Items->Item->Total;
            $fel->granTotal = $value->DTE->DatosEmision->Totales->GranTotal;
            $fel->save();
        }

        return response()->json($fel,200);
       
    }


    public function makeReporte(Request $request){
        $formato = strtolower($request->formatoSalida);

        if($formato === 'csv'){
            $csv = $this->getReportCsv($request->nit,$request->fecha,$request->total);

            $path = 'app/csv/'.$csv;
            Mail::to($request->correo)->send(new reporteGeneral($path), function ($message){
                $message->from($request->correo,'envio');
            });

            return 'ok';
        }elseif($formato === 'pdf'){
            $pdf = $this->getReportePdf($request->nit,$request->fecha,$request->total);

            $path = 'app/pdf/'.$pdf;
            Mail::to($request->correo)->send(new reporteGeneral($path), function ($message){
                $message->from($request->correo,'envio');
            });

            return 'ok';
        }elseif($formato === 'txt'){
            $txt = $this->getReporteTxt($request->nit,$request->fecha,$request->total);

            $path = 'app/txt/'.$txt;
            Mail::to($request->correo)->send(new reporteGeneral($path), function ($message){
                $message->from($request->correo,'envio');
            });
            return 'ok';
        }else{
            return response()->json('formato no permitido',200);
        }
    }

    public function getReportCsv(String $nit, String $fecha, String $total){

        $name_file = 'reporte.csv';

        Excel::store(new facturaReporte($nit, $fecha, $total), $name_file, 'csv');

        $file = Storage::disk('csv')->path($name_file);


        $headers = array(
            'Content-Type: application/csv',
          );

        // return response()->download($file,$name_file,$headers);
        // return response()->json($name_file,200);
        return $name_file;
    }


    public function getReportePdf(String $nit, String $fecha, String $total){

        $data = factura::where(['nitEmisor' => $nit, 'fechaHoraEmision' => $fecha, 'grantotal' => $total])->get();

        

        $html = '<table>
                    <thead>
                        <tr>
                            <th>Moneda</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Nit Emisor</th>
                            <th>Emisor</th>
                            <th>Direccion emisor</th>
                            <th>Receptor</th>
                            <th>Direccion Receptor</th>
                            <th>Descripcion</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($data as $key => $value) {
            $html .= '  <tr>
                            <td>'.$value->codigoMoneda.'</td>
                            <td>'.$value->fechaHoraEmision.'</td>
                            <td>'.$value->tipo.'</td>
                            <td>'.$value->nitEmisor.'</td>
                            <td>'.$value->nombreEmisor.'</td>
                            <td>'.$value->direccionEmisor.'</td>
                            <td>'.$value->nombreReceptor.'</td>
                            <td>'.$value->direccionReceptor.'</td>
                            <td>'.$value->descripcion.'</td>
                            <td>'.$value->granTotal.'</td>
                        </tr>';
        }

        
        $html .=' </tbody>
                </table>';


        $name_file = 'reporte.pdf';
        $pdf = \PDF::loadHTML($html);
        Storage::disk('pdf')->put($name_file, $pdf->output());

        $file = Storage::disk('pdf')->path($name_file);
        

        // return response()->json($name_file,200);
        return $name_file;
    }

    public function getReporteTxt(String $nit, String $fecha, String $total){

        $data = factura::where(['nitEmisor' => $nit, 'fechaHoraEmision' => $fecha, 'grantotal' => $total])->get();

        $txt = "";

        foreach ($data as $key => $value) {
            $txt .= $value->codigoMoneda;
            $txt .= $value->fechaHoraEmision;
            $txt .= $value->tipo;
            $txt .= $value->nitEmisor;
            $txt .= $value->nombreEmisor;
            $txt .= $value->direccionEmisor;
            $txt .= $value->nombreReceptor;
            $txt .= $value->direccionReceptor;
            $txt .= $value->descripcion;
            $txt .= $value->granTotal;
            $txt .= "\n";
        }

        $name_file = 'reporte.txt';

        Storage::disk('txt')->put($name_file, $txt);

        $file = Storage::disk('txt')->path($name_file);

        // return response()->json($name_file,200);   
        return $name_file;  
    }


}
