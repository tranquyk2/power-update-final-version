<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class HistoryExport implements FromView
{
    public function __construct(
        protected $histories
    )
    {
    }

    public function view(): View
    {
        return view('exports.histories', [
            'histories' => $this->histories
        ]);
    }
}
