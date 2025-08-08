<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;

class DompdfPdfService implements PdfService
{
    public function render(string $view, array $data = [], string $paper = 'A4', string $orientation = 'portrait'): string
    {
        $pdf = Pdf::loadView($view, $data)->setPaper($paper, $orientation);
        return $pdf->output();
    }
}
