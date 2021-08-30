<div class="header-bottom sticky-header">
    <div class="container d-flex">
        <nav class="main-nav flex-grow-1">
            <ul class="menu sf-arrows">

                @foreach ($items as $item)
                <li><a href="{{ route("tenant.ecommerce.category", ['tagid' => $item->id]) }}">{{ $item->name }}</a></li>
                @endforeach

            </ul>
        </nav>
    </div><!-- End .header-bottom -->
</div><!-- End .header-bottom -->
