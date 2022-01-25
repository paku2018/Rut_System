<?php


namespace App\Export;

use App\Services\StatisticsService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class SalesExportExcel implements FromView
{
    protected $filter_data;

    function __construct($filter_data) {
        $this->filter_data = $filter_data;
    }

    public function view(): View
    {
        $service = new StatisticsService();
        $start_date = $this->filter_data['start_date'];
        $end_date = $this->filter_data['end_date'];

        $items = $service->getSales($this->filter_data['resId'], $start_date, $end_date);
        return view('excel.sales',compact('items', 'start_date', 'end_date'));
    }
}
