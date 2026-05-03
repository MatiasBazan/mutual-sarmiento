<?php

namespace App\Http\Controllers;

use App\Models\Resumen;
use Illuminate\Http\Request;

class ResumenDownloadController extends Controller
{
    public function download(Request $request, Resumen $resumen): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_if(is_null($resumen->pdf_path), 404);

        $fullPath = storage_path('app/private/' . $resumen->pdf_path);

        if (!file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $resumen->pdf_path);
        }

        abort_unless(file_exists($fullPath), 404);

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="resumen_' . $resumen->periodo . '.pdf"',
        ]);
    }
}
