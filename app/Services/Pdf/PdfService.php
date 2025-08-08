<?php

namespace App\Services\Pdf;

interface PdfService
{
    public function render(string $view, array $data = [], string $paper = 'A4', string $orientation = 'portrait'): string;
}
