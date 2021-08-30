<table>
    @foreach($records as $row)
        <tr>
            @for($i = 65; $i <= 87; $i++)
            @php($col = 'col_'.chr($i))
            <td>{{ $row[$col] }}</td>
            @endfor
        </tr>
    @endforeach
</table>