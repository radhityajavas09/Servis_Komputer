<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PrintLayout extends Component
{
    public function __construct(
        public string $title = 'Cetak Dokumen - Servis Komputer',
        public ?string $backUrl = null,
    ) {}

    public function render(): View
    {
        return view('layouts.print');
    }
}
