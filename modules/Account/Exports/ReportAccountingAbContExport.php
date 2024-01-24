<?php

namespace Modules\Account\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ReportAccountingAbContExport implements FromView, WithCustomCsvSettings
{
	use Exportable;

	protected $data;


    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ','
        ];
    }

    public function data($data)
	{
		$this->data = $data;

		return $this;
	}

	public function view(): View
	{
		return view('account::accounting.templates.excel_abcont', $this->data);
	}
}
