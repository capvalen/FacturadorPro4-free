<table>
    @foreach($records as $row)
        @if($row['state_type_id'] == '11')
            <tr>
                <td>{{ $row['date_of_issue'] }}</td>
                <td>{{ $row['date_of_due'] }}</td>
                <td>{{ $row['document_type_id'] }}</td>
                <td>{{ $row['series'] }}</td>
                <td>{{ $row['number'] }}</td>
                <td>{{ $row['customer_identity_document_type_id'] }}</td>
                <td>{{ $row['customer_number'] }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>{{ $row['exchange_rate_sale'] }}</td>
                <td>{{ $row['db_date_issue'] }}</td>
                <td>{{ $row['db_document_type_id'] }}</td>
                <td>{{ $row['db_series'] }}</td>
                <td>{{ $row['db_number'] }}</td>
                <td>{{ $row['currency'] }}</td>
                <td>{{ $row['amount_usd'] }}</td>
                <td>{{ $row['date_of_due'] }}</td>
                <td>{{ $row['payment_condition'] }}</td>
                <td></td>
                <td></td>
                <td>{{ $row['account_taxed'] }}</td>
                <td></td>
                <td>{{ $row['account_total'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>18.00</td>
                <td>{{ $row['aditional_information'] }}</td>
                <td>{{ $row['payment_method'] }}</td>
                <td></td>
                <td></td>
            </tr>
        @else
            <tr>
                <td>{{ $row['date_of_issue'] }}</td>
                <td>{{ $row['date_of_due'] }}</td>
                <td>{{ $row['document_type_id'] }}</td>
                <td>{{ $row['series'] }}</td>
                <td>{{ $row['number'] }}</td>
                <td>{{ $row['customer_identity_document_type_id'] }}</td>
                <td>{{ $row['customer_number'] }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ $row['total_exportation'] }}</td>
                <td>{{ $row['total_taxed'] }}</td>
                <td>{{ $row['total_exonerated'] }}</td>
                <td>{{ $row['total_unaffected'] }}</td>
                <td>{{ $row['total_isc'] }}</td>
                <td>{{ $row['total_igv'] }}</td>
                <td>{{ $row['total_other_taxes'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['exchange_rate_sale'] }}</td>
                <td>{{ $row['db_date_issue'] }}</td>
                <td>{{ $row['db_document_type_id'] }}</td>
                <td>{{ $row['db_series'] }}</td>
                <td>{{ $row['db_number'] }}</td>
                <td>{{ $row['currency'] }}</td>
                <td>{{ $row['amount_usd'] }}</td>
                <td>{{ $row['date_of_due'] }}</td>
                <td>{{ $row['payment_condition'] }}</td>
                <td></td>
                <td></td>
                <td>{{ $row['account_taxed'] }}</td>
                <td></td>
                <td>{{ $row['account_total'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>18.00</td>
                <td>{{ $row['aditional_information'] }}</td>
                <td>{{ $row['payment_method'] }}</td>
                <td></td>
                <td></td>
            </tr>
        @endif
    @endforeach
</table>
