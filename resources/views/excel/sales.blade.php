<table>
    <thead>
    <tr>
        <td colspan="6" align="center" valign="center" style="background: #ced4da;height: 30px">
            @lang('sales') {{ date('d/m/Y', strtotime($start_date))."~".date('d/m/Y', strtotime($end_date))}}
        </td>
    </tr>
    <tr>
        <th>@lang('ID')</th>
        <th>@lang('table')</th>
        <th>@lang('consumption')</th>
        <th>@lang('tip')</th>
        <th>@lang('shipping')</th>
        <th>@lang('creation_date')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->table ? $item->table->name : '' }}</td>
            <td>{{ $item->consumption }}</td>
            <td>{{ $item->tip }}</td>
            <td>{{ $item->shipping }}</td>
            <td>{{ date('Y-m-d', strtotime($item->created_at)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
