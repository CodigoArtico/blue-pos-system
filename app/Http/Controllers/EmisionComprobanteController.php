<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;

class EmisionComprobanteController extends Controller
{
    //
    protected $see;

    public function __construct()
    {
        // Obtener el certificado activo
        $certificado = Certificado::where('estado', '1')->first();

        if (!$certificado) {
            abort(404, 'Certificado no encontrado o no está activo.');
        }

        // Configurar Greenter
        $this->see = new See();
        $this->see->setService(SunatEndpoints::FE_BETA); // Cambia a FE_PRODUCCION para producción
        $this->see->setCertificate(file_get_contents(storage_path('app/public/certificados/' . $certificado->certificado)));
        $this->see->setClaveSOL($certificado->ruc, $certificado->usuario_sunat, $certificado->password_sunat);
    }

    public function emitirFactura()
{
    try {
        // Obtener el certificado activo
        $certificado = Certificado::where('estado', 'activo')->first();

        if (!$certificado) {
            return response()->json(['estado' => 'Error', 'mensaje' => 'Certificado no encontrado o no está activo.'], 404);
        }

        // Configurar Greenter
        $see = new See();
        $see->setService(SunatEndpoints::FE_BETA); // Cambia a FE_PRODUCCION para producción
        $see->setCertificate(file_get_contents(storage_path('app/public/certificados/' . $certificado->certificado)));
        $see->setClaveSOL($certificado->ruc, $certificado->usuario_sunat, $certificado->password_sunat);

        // Cliente
        $client = (new Client())
            ->setTipoDoc('6') // RUC
            ->setNumDoc('20000000001')
            ->setRznSocial('EMPRESA X');

        // Emisor
        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('LIMA')
            ->setUrbanizacion('-')
            ->setDireccion('Av. Villa Nueva 221')
            ->setCodLocal('0000'); // Código de establecimiento de SUNAT

        $company = (new Company())
            ->setRuc('20123456789')
            ->setRazonSocial('GREEN SAC')
            ->setNombreComercial('GREEN')
            ->setAddress($address);

        // Factura
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta interna
            ->setTipoDoc('01') // Factura electrónica
            ->setSerie('F001')
            ->setCorrelativo('3')
            ->setFechaEmision(new \DateTime('now', new \DateTimeZone('America/Lima')))
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda('PEN') // Soles
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas(100.00)
            ->setMtoIGV(18.00)
            ->setTotalImpuestos(18.00)
            ->setValorVenta(100.00)
            ->setSubTotal(118.00)
            ->setMtoImpVenta(118.00);

        // Detalle del producto
        $item = (new SaleDetail())
            ->setCodProducto('P001')
            ->setUnidad('NIU')
            ->setCantidad(2)
            ->setDescripcion('PRODUCTO 1')
            ->setMtoBaseIgv(100.00)
            ->setPorcentajeIgv(18.00)
            ->setIgv(18.00)
            ->setTipAfeIgv('10') // Gravado
            ->setTotalImpuestos(18.00)
            ->setMtoValorVenta(100.00)
            ->setMtoValorUnitario(50.00)
            ->setMtoPrecioUnitario(59.00);

        $legend = (new Legend())
            ->setCode('1000')
            ->setValue('SON CIENTO DIECIOCHO CON 00/100 SOLES');

        $invoice->setDetails([$item])->setLegends([$legend]);

        // Enviar a SUNAT
        $result = $see->send($invoice);

        // Ruta de almacenamiento
        $path = storage_path('facturas');

        // Crear carpeta si no existe
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Guardar XML firmado
        file_put_contents($path . '/' . $invoice->getName() . '.xml', $see->getFactory()->getLastXml());

        // Validar conexión con SUNAT
        if (!$result->isSuccess()) {
            return response()->json([
                'estado' => 'Error',
                'codigo_error' => $result->getError()->getCode(),
                'mensaje_error' => $result->getError()->getMessage(),
            ]);
        }

        // Guardar CDR ZIP
        $cdrZipPath = $path . '/R-' . $invoice->getName() . '.zip';
        file_put_contents($cdrZipPath, $result->getCdrZip());

        // Extraer contenido del ZIP
        $zip = new ZipArchive;
        $cdrXmlContent = '';

        $cdrXmlFileName = 'R-' . $invoice->getName() . '.xml'; // Nombre del archivo XML

        if ($zip->open($cdrZipPath) === TRUE) {
            if ($zip->locateName($cdrXmlFileName) !== false) {
                $zip->extractTo($path, $cdrXmlFileName);
                $cdrXmlContent = file_get_contents($path . '/' . $cdrXmlFileName);
            }
            $zip->close();
        } else {
            return response()->json(['estado' => 'Error', 'mensaje' => 'No se pudo abrir el ZIP.']);
        }

        // Calcular hash del CDR
        $hash = hash('sha1', $cdrXmlContent);

        // Lectura del CDR
        $cdr = $result->getCdrResponse();
        $code = (int)$cdr->getCode();

        $message = match (true) {
            $code === 0 => 'ESTADO: ACEPTADA',
            $code >= 2000 && $code <= 3999 => 'ESTADO: RECHAZADA',
            default => 'Excepción - Código inválido'
        };

        // Devolver respuesta
        return response()->json([
            'estado' => $message,
            'descripcion' => $cdr->getDescription(),
            'cdr_xml' => $cdrXmlContent,
            'hash' => $hash,
        ]);
    } catch (\Exception $e) {
        Log::error('Error al emitir factura: ' . $e->getMessage());
        return response()->json(['estado' => 'Error', 'mensaje' => 'Ocurrió un error al procesar la solicitud.'], 500);
    }
}
}
