// app/Http/Controllers/BarcodeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class BarcodeController extends Controller
{
public function printBarcode()
{
// Dummy data, gantilah dengan data produk yang sesuai dari database Anda
$products = [
['barcode' => '123456789', 'title' => 'Product A'],
['barcode' => '987654321', 'title' => 'Product B'],
// Tambahkan data produk lainnya sesuai kebutuhan
];

$pdf = PDF::loadView('barcode_pdf', compact('products'));
return $pdf->download('barcodes.pdf');
}
}