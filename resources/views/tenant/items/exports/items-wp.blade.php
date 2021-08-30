<html>
@if(!empty($records))

        <span>ID</span>,
        <span>SKU</span>,
        <span>Nombre</span>,
        <span>Publicado</span>,
        <span>Descripción</span>,
        <span>Categoría</span>,
        <span>Precio</span>,
        <span>Inventario</span>
        @foreach($records as $record)
        <br>
            {{$record->id}},
            {{$record->internal_id}},
            {{$record->second_name}},
            {{$record->description}},
            {{$record->name }},
            {{$record->category_id != '' ? $record->category->name : '' }},
            {{$record->sale_unit_price }},
            {{$record->stock}}
        @endforeach
        <br>
@endif

</html>
