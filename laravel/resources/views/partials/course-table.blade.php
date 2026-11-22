{{-- $rows: iterable of [symbol => price] --}}
<table class="table table-sm table-bordered bg-white datatable">
    <thead><tr><th>Валютная пара</th><th>Курс</th></tr></thead>
    <tbody>
    @foreach ($rows as $symbol => $price)
        <tr class="{{ \App\Models\Course::highlightClass($symbol) }}">
            <td>{{ $symbol }}</td>
            <td data-order="{{ $price }}">{{ $price }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
