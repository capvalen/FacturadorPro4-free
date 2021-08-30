<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <a href="{{route('tenant.dashboard.index')}}" class="logo pt-2 pt-md-0">
            @if($vc_company->logo)
                <img src="{{ asset('storage/uploads/logos/'.$vc_company->logo) }}" alt="Logo" />
            @else
                <img src="{{asset('logo/700x300.jpg')}}" alt="Logo" />
            @endif
        </a>
        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    @if(in_array('dashboard', $vc_modules))
                    <li class="{{ ($path[0] === 'dashboard')?'nav-active':'' }}">
                        <a class="nav-link" href="{{ route('tenant.dashboard.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                            <span>DASHBOARD</span>
                        </a>
                    </li>
                    @endif

                    {{-- Ventas --}}
                    @if(in_array('documents', $vc_modules))
                    <li class="
                        nav-parent
                        {{ ($path[0] === 'documents')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'summaries')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'voided')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'quotations')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'sale-notes')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'contingencies')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'incentives')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'order-notes')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'sale-opportunities')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'contracts')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'production-orders')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'technical-services')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'user-commissions')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'regularize-shipping')?'nav-active nav-expanded':'' }}
                        ">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span>VENTAS</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(auth()->user()->type != 'integrator' && $vc_company->soap_type_id != '03')
                                @if(in_array('documents', $vc_modules))
                                    @if(in_array('new_document', $vc_module_levels))
                                        <li class="{{ ($path[0] === 'documents' && $path[1] === 'create')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.documents.create')}}">Comprobante electrónico</a>
                                        </li>
                                    @endif
                                @endif
                            @endif

                            @if(in_array('documents', $vc_modules) && $vc_company->soap_type_id != '03')
                                @if(in_array('list_document', $vc_module_levels))
                                    <li class="{{ ($path[0] === 'documents' && $path[1] != 'create' && $path[1] != 'not-sent'&& $path[1] != 'regularize-shipping')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.documents.index')}}">Listado de comprobantes</a>
                                    </li>
                                @endif
                            @endif

                            @if(in_array('sale_notes', $vc_module_levels))
                                    <li class="{{ ($path[0] === 'sale-notes')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.sale_notes.index')}}">Notas de Venta</a>
                                    </li>
                                @endif

                            @if(in_array('documents', $vc_modules) && $vc_company->soap_type_id != '03')

                                @if(in_array('document_not_sent', $vc_module_levels))
                                    <li class="{{ ($path[0] === 'documents' && $path[1] === 'not-sent')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.documents.not_sent')}}">
                                            Comprobantes no enviados
                                        </a>
                                    </li>
                                @endif
                                @if(in_array('regularize_shipping', $vc_module_levels))
                                <li class="{{ ($path[0] === 'documents' && $path[1] === 'regularize-shipping')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.documents.regularize_shipping')}}">
                                        CPE pendientes de rectificación
                                    </a>
                                </li>
                                @endif
                            @endif

                            @if(auth()->user()->type != 'integrator' && in_array('documents', $vc_modules) )

                                @if(auth()->user()->type != 'integrator' && in_array('document_contingengy', $vc_module_levels) && $vc_company->soap_type_id != '03')
                                <li class="{{ ($path[0] === 'contingencies' )?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.contingencies.index')}}">
                                        Documentos de contingencia
                                    </a>
                                </li>
                                @endif

                                @if(in_array('summary_voided', $vc_module_levels) && $vc_company->soap_type_id != '03')

                                    <li class="nav-parent
                                        {{ ($path[0] === 'summaries')?'nav-active nav-expanded':'' }}
                                        {{ ($path[0] === 'voided')?'nav-active nav-expanded':'' }}
                                        ">
                                        <a class="nav-link" href="#">
                                            Resúmenes y Anulaciones
                                        </a>
                                        <ul class="nav nav-children">
                                            <li class="{{ ($path[0] === 'summaries')?'nav-active':'' }}">
                                                <a class="nav-link" href="{{route('tenant.summaries.index')}}">
                                                    Resúmenes
                                                </a>
                                            </li>
                                            <li class="{{ ($path[0] === 'voided')?'nav-active':'' }}">
                                                <a class="nav-link" href="{{route('tenant.voided.index')}}">
                                                    Anulaciones
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                @if(in_array('sale-opportunity', $vc_module_levels))
                                    <li class="{{ ($path[0] === 'sale-opportunities')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.sale_opportunities.index')}}">
                                            Oportunidad de venta
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('quotations', $vc_module_levels))

                                    <li class="{{ ($path[0] === 'quotations')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.quotations.index')}}">
                                            Cotizaciones
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('contracts', $vc_module_levels))
                                    <li class="nav-parent
                                        {{ ($path[0] === 'contracts')?'nav-active nav-expanded':'' }}
                                        {{ ($path[0] === 'production-orders')?'nav-active nav-expanded':'' }}
                                        ">
                                        <a class="nav-link" href="#">
                                            Contratos
                                        </a>
                                        <ul class="nav nav-children">
                                            <li class="{{ ($path[0] === 'contracts')?'nav-active':'' }}">
                                                <a class="nav-link" href="{{route('tenant.contracts.index')}}">
                                                    Listado
                                                </a>
                                            </li>
                                            <li class="{{ ($path[0] === 'production-orders')?'nav-active':'' }}">
                                                <a class="nav-link" href="{{route('tenant.production_orders.index')}}">
                                                    Ordenes de Producción
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                @if(in_array('order-note', $vc_module_levels))
                                    <li class="{{ ($path[0] === 'order-notes')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.order_notes.index')}}">
                                            Pedidos
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('technical-service', $vc_module_levels))
                                    <li class="{{ ($path[0] === 'technical-services')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.technical_services.index')}}">
                                            Servicio de soporte técnico
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('incentives', $vc_module_levels))

                                    <li class="nav-parent
                                        {{ ($path[0] === 'incentives')?'nav-active nav-expanded':'' }}
                                        {{ ($path[0] === 'user-commissions')?'nav-active nav-expanded':'' }}
                                        ">
                                        <a class="nav-link" href="#">
                                            Comisiones
                                        </a>
                                        <ul class="nav nav-children">
                                            <li class="{{ ($path[0] === 'user-commissions')?'nav-active':'' }}">
                                                <a class="nav-link" href="{{route('tenant.user_commissions.index')}}">
                                                    Vendedores
                                                </a>
                                            </li>
                                            <li class="{{ ($path[0] === 'incentives')?'nav-active':'' }}">
                                                <a class="nav-link" href="{{route('tenant.incentives.index')}}">Productos</a>
                                            </li>
                                        </ul>
                                    </li>

                                @endif



                            @endif

                        </ul>
                    </li>
                    @endif

                    {{-- POS --}}
                    @if(auth()->user()->type != 'integrator')
                        @if(in_array('pos', $vc_modules))
                        <li class="nav-parent
                        {{ ($path[0] === 'pos')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'cash')?'nav-active nav-expanded':'' }}
                        ">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                <span>POS</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('pos', $vc_module_levels))
                                <li class="{{ ($path[0] === 'pos'  )?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.pos.index')}}">Punto de venta</a>
                                </li>
                                @endif
                                @if(in_array('cash', $vc_module_levels))
                                <li class="{{ ($path[0] === 'cash')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.cash.index')}}">Caja chica POS</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                    @endif

                    {{-- Tienda virtual --}}
                    @if(in_array('ecommerce', $vc_modules))
                    <li class="nav-parent
                        {{ in_array($path[0], ['ecommerce','items_ecommerce', 'tags', 'promotions', 'orders', 'configuration'])?'nav-active nav-expanded':'' }}"
                    >
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>Tienda Virtual</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('ecommerce', $vc_module_levels))
                            <li class="">
                                <a class="nav-link" onclick="window.open( '{{ route("tenant.ecommerce.index") }} ')">Ir a Tienda</a>
                            </li>
                            @endif
                            @if(in_array('ecommerce_orders', $vc_module_levels))
                            <li class="{{ ($path[0] === 'orders')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant_orders_index')}}">Pedidos</a>
                            </li>
                            @endif
                            @if(in_array('ecommerce_items', $vc_module_levels))
                            <li class="{{ ($path[0] === 'items_ecommerce')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.items_ecommerce.index')}}">Productos Tienda Virtual</a>
                            </li>
                            @endif
                            @if(in_array('ecommerce_tags', $vc_module_levels))
                            <li class="{{ ($path[0] === 'tags')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.tags.index')}}">Tags - Categorias(Etiquetas)</a>
                            </li>
                            @endif
                            @if(in_array('ecommerce_promotions', $vc_module_levels))
                            <li class="{{ ($path[0] === 'promotions')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.promotion.index')}}">Promociones(Banners)</a>
                            </li>
                            @endif
                            @if(in_array('ecommerce_settings', $vc_module_levels))
                            <li class="{{ ($path[1] === 'configuration')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant_ecommerce_configuration')}}">Configuración</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    {{-- Productos --}}
                    @if(in_array('items', $vc_modules))
                    <li class="nav-parent
                        {{ ($path[0] === 'items')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'services')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'categories')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'brands')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'item-lots')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'item-sets')?'nav-active nav-expanded':'' }}
                        ">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span>Productos/Servicios</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('items', $vc_module_levels))
                            <li class="{{ ($path[0] === 'items')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.items.index')}}">Productos</a>
                            </li>
                            @endif
                            @if(in_array('items_packs', $vc_module_levels))
                            <li class="{{ ($path[0] === 'item-sets'  )?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.item_sets.index')}}">Conjuntos/Packs/Promociones</a>
                            </li>
                            @endif
                            @if(in_array('items_services', $vc_module_levels))
                            <li class="{{ ($path[0] === 'services')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.services')}}">Servicios</a>
                            </li>
                            @endif
                            @if(in_array('items_categories', $vc_module_levels))
                            <li class="{{ ($path[0] === 'categories')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.categories.index')}}">Categorías</a>
                            </li>
                            @endif
                            @if(in_array('items_brands', $vc_module_levels))
                            <li class="{{ ($path[0] === 'brands')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.brands.index')}}">Marcas</a>
                            </li>
                            @endif
                            @if(in_array('items_lots', $vc_module_levels))
                            <li class="{{ ($path[0] === 'item-lots')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.item-lots.index')}}">Series</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- Clientes --}}
                    @if(in_array('persons', $vc_modules))
                    <li class="nav-parent
                        {{ ($path[0] === 'persons' && $path[1] === 'customers')?'nav-active nav-expanded':'' }}
                        {{ $path[0] === 'person-types' ? 'nav-active nav-expanded' : '' }}
                        ">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                            <span>Clientes</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('clients', $vc_module_levels))
                            <li class="{{ ($path[0] === 'persons' && $path[1] === 'customers')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.persons.index', ['type' => 'customers'])}}">Clientes</a>
                            </li>
                            @endif
                            @if(in_array('clients_types', $vc_module_levels))
                            <li class="{{ ($path[0] === 'person-types')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.person_types.index')}}">Tipos de clientes</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()->type != 'integrator')
                        @if(in_array('purchases', $vc_modules))
                        <li class="
                            nav-parent
                            {{ ($path[0] === 'purchases')?'nav-active nav-expanded':'' }}
                            {{ ($path[0] === 'persons' && $path[1] === 'suppliers')?'nav-active nav-expanded':'' }}
                            {{ ($path[0] === 'expenses')?'nav-active nav-expanded':'' }}
                            {{ ($path[0] === 'purchase-quotations')?'nav-active nav-expanded':'' }}
                            {{ ($path[0] === 'purchase-orders')?'nav-active nav-expanded':'' }}
                            {{ ($path[0] === 'fixed-asset')?'nav-active nav-expanded':'' }}
                            ">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                <span>Compras</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('purchases_create', $vc_module_levels))
                                <li class="{{ ($path[0] === 'purchases' && $path[1] === 'create')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.purchases.create')}}">Nuevo</a>
                                </li>
                                @endif
                                @if(in_array('purchases_list', $vc_module_levels))
                                <li class="{{ ($path[0] === 'purchases' && $path[1] != 'create')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.purchases.index')}}">Listado</a>
                                </li>
                                @endif
                                @if(in_array('purchases_orders', $vc_module_levels))
                                <li class="{{ ($path[0] === 'purchase-orders')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.purchase-orders.index')}}">Ordenes de compra</a>
                                </li>
                                @endif
                                @if(in_array('purchases_expenses', $vc_module_levels))
                                <li class="{{ ($path[0] === 'expenses' )?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.expenses.index')}}">Gastos diversos</a>
                                </li>
                                @endif
                                @if(in_array('purchases_quotations', $vc_module_levels) || in_array('purchases_suppliers', $vc_module_levels))
                                <li class="nav-parent
                                    {{ ($path[0] === 'persons' && $path[1] === 'suppliers')?'nav-active nav-expanded':'' }}
                                    {{ ($path[0] === 'purchase-quotations')?'nav-active nav-expanded':'' }}
                                    ">
                                    <a class="nav-link" href="#">
                                        Proveedores
                                    </a>
                                    <ul class="nav nav-children">
                                        @if(in_array('purchases_suppliers', $vc_module_levels))
                                        <li class="{{ ($path[0] === 'persons' && $path[1] === 'suppliers')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.persons.index', ['type' => 'suppliers'])}}">Listado</a>
                                        </li>
                                        @endif
                                        @if(in_array('purchases_quotations', $vc_module_levels))
                                        <li class="{{ ($path[0] === 'purchase-quotations')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.purchase-quotations.index')}}">Solicitar cotización</a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @if(in_array('purchases_fixed_assets_purchases', $vc_module_levels) || in_array('purchases_fixed_assets_items', $vc_module_levels))
                                <li class="nav-parent {{ ($path[0] === 'fixed-asset' )?'nav-active nav-expanded' : '' }}">
                                    <a class="nav-link" href="#">Activos fijos</a>
                                    <ul class="nav nav-children">
                                        @if(in_array('purchases_fixed_assets_items', $vc_module_levels))
                                        <li class="{{ ($path[0] === 'fixed-asset' && $path[1] === 'items')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.fixed_asset_items.index')}}">Ítems</a>
                                        </li>
                                        @endif
                                        @if(in_array('purchases_fixed_assets_purchases', $vc_module_levels))
                                        <li class="{{ ($path[0] === 'fixed-asset' && $path[1] === 'purchases')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.fixed_asset_purchases.index')}}">Compras</a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Inventario --}}
                        @if(in_array('inventory', $vc_modules))
                        <li class="nav-parent
                            {{ (in_array($path[0], ['inventory', 'moves', 'transfers', 'devolutions']) |($path[0] === 'reports' && in_array($path[1], ['kardex', 'inventory', 'valued-kardex'])))?'nav-active nav-expanded':'' }}
                        ">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-archive"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                                <span>Inventario</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('inventory', $vc_module_levels))
                                <li class="{{ ($path[0] === 'inventory')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('inventory.index')}}">Movimientos</a>
                                </li>
                                @endif
                                @if(in_array('inventory_transfers', $vc_module_levels))
                                <li class="{{ ($path[0] === 'transfers')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('transfers.index')}}">Traslados</a>
                                </li>
                                @endif
                                @if(in_array('inventory_devolutions', $vc_module_levels))
                                <li class="{{ ($path[0] === 'devolutions')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('devolutions.index')}}">Devoluciones</a>
                                </li>
                                @endif
                                @if(in_array('inventory_report_kardex', $vc_module_levels))
                                <li class="{{(($path[0] === 'reports') && ($path[1] === 'kardex')) ? 'nav-active' : ''}}">
                                    <a class="nav-link" href="{{route('reports.kardex.index')}}">Reporte Kardex</a>
                                </li>
                                @endif
                                @if(in_array('inventory_report', $vc_module_levels))
                                <li class="{{(($path[0] === 'reports') && ($path[1] == 'inventory')) ? 'nav-active' : ''}}">
                                    <a class="nav-link" href="{{route('reports.inventory.index')}}">Reporte Inventario</a>
                                </li>
                                @endif
                                @if(in_array('inventory_report_kardex', $vc_module_levels))
                                {{-- <li class="{{ ($path[0] === 'warehouses')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('warehouses.index')}}">Almacenes</a>
                                </li> --}}
                                <li class="{{(($path[0] === 'reports') && ($path[1] === 'valued-kardex')) ? 'nav-active' : ''}}">
                                    <a class="nav-link" href="{{route('reports.valued_kardex.index')}}">Kardex valorizado</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                    @endif

                    @if(in_array('establishments', $vc_modules))
                    <li class="nav-parent {{ in_array($path[0], ['users', 'establishments'])?'nav-active nav-expanded':'' }}">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Usuarios/Locales & Series</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('users', $vc_module_levels))
                            <li class="{{ ($path[0] === 'users')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.users.index')}}">Usuarios</a>
                            </li>
                            @endif
                            @if(in_array('users_establishments', $vc_module_levels))
                            <li class="{{ ($path[0] === 'establishments')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.establishments.index')}}">Establecimientos</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(in_array('advanced', $vc_modules) && $vc_company->soap_type_id != '03')
                    <li class="
                        nav-parent
                        {{ ($path[0] === 'retentions')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'dispatches')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'perceptions')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'drivers')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'dispatchers')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'order-forms')?'nav-active nav-expanded':'' }}

                        ">
                        <a class="nav-link" href="#">
                            <i class="fas fa-receipt" aria-hidden="true"></i>
                            <span>Comprobantes avanzados</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('advanced_retentions', $vc_module_levels))
                            <li class="{{ ($path[0] === 'retentions')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.retentions.index')}}">Retenciones</a>
                            </li>
                            @endif
                            @if(in_array('advanced_dispatches', $vc_module_levels))
                            <li class="{{ ($path[0] === 'dispatches')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.dispatches.index')}}">Guías de remisión</a>
                            </li>
                            @endif
                            @if(in_array('advanced_perceptions', $vc_module_levels))
                            <li class="{{ ($path[0] === 'perceptions')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.perceptions.index')}}">Percepciones</a>
                            </li>
                            @endif
                            @if(in_array('advanced_order_forms', $vc_module_levels))
                            <li class="nav-parent
                                {{ ($path[0] === 'order-forms')?'nav-active nav-expanded':'' }}
                                {{ ($path[0] === 'drivers')?'nav-active nav-expanded':'' }}
                                {{ ($path[0] === 'dispatchers')?'nav-active nav-expanded':'' }}
                                ">
                                <a class="nav-link" href="#">Ordenes de pedido</a>
                                <ul class="nav nav-children">
                                    <li class="{{ ($path[0] === 'order-forms')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.order_forms.index')}}">Listado</a>
                                    </li>
                                    <li class="{{ ($path[0] === 'drivers')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.order_forms.drivers.index')}}">Conductores</a>
                                    </li>
                                    <li class="{{ ($path[0] === 'dispatchers')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.order_forms.dispatchers.index')}}">Transportistas</a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if(in_array('reports', $vc_modules))
                    <li class="{{  ($path[0] === 'reports' && in_array($path[1], ['purchases', 'search','sales','customers','items', 'general-items','consistency-documents', 'quotations', 'sale-notes','cash','commissions','document-hotels', 'validate-documents', 'document-detractions','commercial-analysis', 'order-notes-consolidated', 'order-notes-general', 'sales-consolidated', 'user-commissions', 'fixed-asset-purchases', 'massive-downloads'])) ? 'nav-active' : ''}} {{ $path[0] === 'list-reports' ? 'nav-active' : '' }}">
                        <a class="nav-link" href="{{ url('list-reports') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                            <span>Reportes</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('accounting', $vc_modules))
                    <li class="nav-parent {{ ($path[0] === 'account')?'nav-active nav-expanded':'' }}">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            <span>Contabilidad</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('account_report', $vc_module_levels))
                            <li class="{{(($path[0] === 'account') && ($path[1] === 'format')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{ route('tenant.account_format.index') }}">Exportar reporte</a>
                            </li>
                            @endif
                            @if(in_array('account_formats', $vc_module_levels))
                            <li class="{{(($path[0] === 'account') && ($path[1] == ''))   ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{ route('tenant.account.index') }}">Exportar formatos - Sis. Contable</a>
                            </li>
                            @endif
                            @if(in_array('account_summary', $vc_module_levels))
                            <li class="{{(($path[0] === 'account') && ($path[1] == 'summary-report'))   ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{ route('tenant.account_summary_report.index') }}">Reporte resumido - Ventas</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(in_array('finance', $vc_modules))

                    <li class="nav-parent {{ $path[0] === 'finances' && in_array($path[1], ['global-payments', 'balance','payment-method-types', 'unpaid', 'to-pay', 'income', 'movements']) ? 'nav-active nav-expanded' : ''}}">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            <span>Finanzas</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('finances_movements', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'movements')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.movements.index')}}">Movimientos</a>
                            </li>
                            @endif
                            @if(in_array('finances_incomes', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'income')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.income.index')}}">Ingresos</a>
                            </li>
                            @endif
                            @if(in_array('finances_unpaid', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'unpaid')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.unpaid.index')}}">Cuentas por cobrar</a>
                            </li>
                            @endif
                            @if(in_array('finances_to_pay', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'to-pay')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.to_pay.index')}}">Cuentas por pagar</a>
                            </li>
                            @endif
                            @if(in_array('finances_payments', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'global-payments')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.global_payments.index')}}">Pagos</a>
                            </li>
                            @endif
                            @if(in_array('finances_balance', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'balance')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.balance.index')}}">Balance</a>
                            </li>
                            @endif
                            @if(in_array('finances_payment_method_types', $vc_module_levels))
                            <li class="{{(($path[0] === 'finances') && ($path[1] == 'payment-method-types')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.finances.payment_method_types.index')}}">Ingresos y Egresos - M. Pago</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(in_array('configuration', $vc_modules))
                    <li class="{{in_array($path[0], ['list-platforms', 'list-cards', 'list-currencies', 'list-bank-accounts', 'list-banks', 'list-attributes', 'list-detractions', 'list-units', 'list-payment-methods', 'list-incomes', 'list-payments', 'company_accounts', 'list-vouchers-type',     'companies', 'advanced', 'tasks', 'inventories','bussiness_turns','offline-configurations','series-configurations','configurations', 'login-page', 'list-settings']) ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{ url('list-settings') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Configuración</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('cuenta', $vc_modules))
                    <li class=" nav-parent
                        {{ ($path[0] === 'cuenta')?'nav-active nav-expanded':'' }}">
                        <a class="nav-link" href="#">
                            <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                            <span>Mis Pagos</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('account_users_settings', $vc_module_levels))
                            <li class="{{ (($path[0] === 'cuenta') && ($path[1] === 'configuration')) ?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.configuration.index')}}">Configuracion</a>
                            </li>
                            @endif
                            @if(in_array('account_users_list', $vc_module_levels))
                            <li class="{{ (($path[0] === 'cuenta') && ($path[1] === 'payment_index')) ?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.payment.index')}}">Lista de Pagos</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if(in_array('hotels', $vc_modules) || in_array('documentary-procedure', $vc_modules))
                    <li class="nav-description">Módulos extras</li>
                    @endif
                    @if(in_array('hotels', $vc_modules))
                    <li class=" nav-parent {{ ($path[0] === 'hotels') ? 'nav-active nav-expanded' : '' }}">
                        <a class="nav-link" href="#">
                            <i class="fas fa-building" aria-hidden="true"></i>
                            <span>Hoteles</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('hotels_reception', $vc_module_levels))
                            <li class="{{ (($path[0] === 'hotels') && ($path[1] === 'reception')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ url('hotels/reception') }}">Recepción</a>
                            </li>
                            @endif
                            @if(in_array('hotels_rates', $vc_module_levels))
                            <li class="{{ (($path[0] === 'hotels') && ($path[1] === 'rates')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ url('hotels/rates') }}">Tarifas</a>
                            </li>
                            @endif
                            @if(in_array('hotels_floors', $vc_module_levels))
                            <li class="{{ (($path[0] === 'hotels') && ($path[1] === 'floors')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ url('hotels/floors') }}">Pisos</a>
                            </li>
                            @endif
                            @if(in_array('hotels_cats', $vc_module_levels))
                            <li class="{{ (($path[0] === 'hotels') && ($path[1] === 'categories')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ url('hotels/categories') }}">Categorías</a>
                            </li>
                            @endif
                            @if(in_array('hotels_rooms', $vc_module_levels))
                            <li class="{{ (($path[0] === 'hotels') && ($path[1] === 'rooms')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ url('hotels/rooms') }}">Habitaciones</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if(in_array('documentary-procedure', $vc_modules))
                    <li class=" nav-parent {{ ($path[0] === 'documentary-procedure') ? 'nav-active nav-expanded' : '' }}">
                        <a class="nav-link" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            <span>Trámite documentario</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('documentary_offices', $vc_module_levels))
                            <li class="{{ (($path[0] === 'documentary-procedure') && ($path[1] === 'offices')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ route('documentary.offices') }}">Oficinas</a>
                            </li>
                            @endif
                            @if(in_array('documentary_process', $vc_module_levels))
                            <li class="{{ (($path[0] === 'documentary-procedure') && ($path[1] === 'processes')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ route('documentary.processes') }}">Procesos</a>
                            </li>
                            @endif
                            @if(in_array('documentary_documents', $vc_module_levels))
                            <li class="{{ (($path[0] === 'documentary-procedure') && ($path[1] === 'documents')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ route('documentary.documents') }}">Tipos de Documento</a>
                            </li>
                            @endif
                            @if(in_array('documentary_actions', $vc_module_levels))
                            <li class="{{ (($path[0] === 'documentary-procedure') && ($path[1] === 'actions')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ route('documentary.actions') }}">Acciones</a>
                            </li>
                            @endif
                            @if(in_array('documentary_files', $vc_module_levels))
                            <li class="{{ (($path[0] === 'documentary-procedure') && ($path[1] === 'files')) ? 'nav-active' : '' }}">
                                <a class="nav-link" href="{{ route('documentary.files') }}">Expedientes</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>
</aside>
