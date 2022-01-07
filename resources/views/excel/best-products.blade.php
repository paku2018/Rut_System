<table>
    <thead>
    <tr>
        <td colspan="3" align="center" valign="center" style="background: #ced4da;height: 30px">
            @lang('best_selling_product') {{ date('d/m/Y', strtotime($start_date))."~".date('d/m/Y', strtotime($end_date))}}
        </td>
    </tr>
    <tr>
        <th>@lang('product')</th>
        <th>@lang('amount')</th>
        <th>@lang('total')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ $item['product_name'] }}</td>
            <td>{{ $item['ordered_count'] }}</td>
            <td>{{ "$".$item['product_price'] * $item['ordered_count'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
