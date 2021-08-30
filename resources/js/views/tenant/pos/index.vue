<template>
    <div class="container-fluid p-0">
        <div class="row page-header pr-0 no-gutters" style="height:auto">
            <Keypress
                key-event="keyup"
                :key-code="112"
                @success="handleFn112"
            />
            <!-- <Keypress key-event="keyup" :key-code="113" @success="handleFn113" /> -->

            <!-- <h2 class="text-sm">POS</h2>
      <div class="right-wrapper pull-right">
        <h2 class="text-sm pr-5">T/C 3.321</h2>
        <h2 class="text-sm">{{user.name}}</h2>
      </div> -->
            <div class="col-md-4">
                <!-- <h2 class="text-sm">POS</h2> -->
                <h2>
                    <el-switch
                        v-model="search_item_by_barcode"
                        active-text="Buscar con escaner de código de barras"
                        @change="changeSearchItemBarcode"
                    ></el-switch>
                </h2>
            </div>
            <div class="col-md-4">
                <h2>
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Todas las categorías"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            @click="back()"
                            class="btn btn-custom btn-sm  mt-2 mr-2 mr-sm-0"
                        >
                            <i class="fa fa-border-all"></i>
                        </button>
                    </el-tooltip>
                </h2>
                <h2>
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Categorías y productos"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            :disabled="place == 'cat2'"
                            @click="setView('cat2')"
                            class="btn btn-custom btn-sm  mt-2 mr-2 mr-sm-0"
                        >
                            <i class="fa fa-bars"></i>
                        </button>
                    </el-tooltip>
                </h2>
                <h2>
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Listado de todos los productos"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            :disabled="place == 'cat3'"
                            @click="setView('cat3')"
                            class="btn btn-custom btn-sm  mt-2 mr-2 mr-sm-0"
                        >
                            <i class="fas fa-list-ul"></i>
                        </button>
                    </el-tooltip>
                </h2>
                <h2>
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Regresar"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            :disabled="place == 'cat'"
                            @click="back()"
                            class="btn btn-custom btn-sm  mt-2 mr-2 mr-sm-0"
                        >
                            <i class="fa fa-undo"></i>
                        </button>
                    </el-tooltip>
                </h2>
            </div>
            <div class="col-md-4">
                <div class="right-wrapper">
                    <h2 class="text-sm pr-5" style="font-size: 14px;">
                        T/C {{ form.exchange_rate_sale }}
                    </h2>
                    <h2 class="text-sm  pull-right" style="font-size: 14px;">{{ user.name }}</h2>
                </div>
            </div>
        </div>

        <div
            v-if="!is_payment"
            class="row col-lg-12 m-0 p-0"
            v-loading="loading"
        >
            <div class="col-lg-8 col-md-6 px-4 pt-3 hyo">
                <template v-if="!search_item_by_barcode">
                    <el-input
                        v-show="
                            place == 'prod' ||
                                place == 'cat2' ||
                                place == 'cat3'
                        "
                        placeholder="Buscar productos"
                        size="medium"
                        v-model="input_item"
                        @input="searchItems"
                        @keyup.native="keyupTabCustomer"
                        @keyup.enter.native="keyupEnterAddItem"
                        class="m-bottom"
                        ref="ref_search_items"
                    >
                        <el-button
                            slot="append"
                            icon="el-icon-plus"
                            @click.prevent="showDialogNewItem = true"
                        ></el-button>
                    </el-input>
                </template>

                <template v-else>
                    <el-input
                        v-show="
                            place == 'prod' ||
                                place == 'cat2' ||
                                place == 'cat3'
                        "
                        placeholder="Buscar productos"
                        size="medium"
                        v-model="input_item"
                        @change="searchItemsBarcode"
                        @keyup.native="keyupTabCustomer"
                        ref="ref_search_items"
                        class="m-bottom"
                    >
                        <el-button
                            slot="append"
                            icon="el-icon-plus"
                            @click.prevent="showDialogNewItem = true"
                        ></el-button>
                    </el-input>
                </template>

                <div v-if="place == 'cat2'" class="container testimonial-group">
                    <div class="row text-center flex-nowrap">
                        <div
                            v-for="(item, index) in categories"
                            @click="filterCategorie(item.id, true)"
                            :style="{ backgroundColor: item.color }"
                            :key="index"
                            class="col-sm-3 pointer"
                        >
                            {{ item.name }}
                        </div>
                    </div>
                </div>
                <br/>

                <div v-if="place == 'cat'" class="row no-gutters">
                    <template v-for="(item, index) in categories">
                        <div class="col" :key="index">
                            <div @click="filterCategorie(item.id)" class="card p-0 m-0 mb-1 mr-1 text-center">
                                <div
                                    :style="{ backgroundColor: item.color }"
                                    class="card-body pointer rounded-0"
                                    style="font-weight: bold;color: white;font-size: 18px;"
                                >
                                    {{ item.name }}
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div v-if="place == 'prod' || place == 'cat2'" class="row">
                    <template v-for="(item, index) in items">
                        <div v-bind:style="classObjectCol" :key="index">
                            <section class="card ">
                                <div
                                    class="card-body pointer px-2 pt-2"
                                    @click="clickAddItem(item, index)"
                                >
                                    <p
                                        class="font-weight-semibold mb-0"
                                        v-if="DescriptionLength(item) > 50"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        :title="item.description"
                                    >
                                        {{ item.description.substring(0, 50) }}
                                    </p>
                                    <p
                                        class="font-weight-semibold mb-0"
                                        v-if="DescriptionLength(item) <= 50"
                                    >
                                        {{ item.description }}
                                    </p>
                                    <img
                                        :src="item.image_url"
                                        class="img-thumbail img-custom"
                                    />
                                    <p
                                        class="text-muted font-weight-lighter mb-0"
                                    >
                                        <small>{{ item.internal_id }}</small>
                                        <template v-if="item.sets.length > 0">
                                            <br/>
                                            <small>
                                                {{ item.sets.join("-") }}
                                            </small>
                                        </template>

                                        <!-- <el-popover v-if="item.warehouses" placement="right" width="280"  trigger="hover">
                      <el-table  :data="item.warehouses">
                        <el-table-column width="150" property="warehouse_description" label="Ubicación"></el-table-column>
                        <el-table-column width="100" property="stock" label="Stock"></el-table-column>
                      </el-table>
                      <el-button slot="reference"><i class="fa fa-search"></i></el-button>
                    </el-popover> -->
                                    </p>
                                </div>
                                <div class="card-footer pointer text-center bg-primary">
                                    <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-danger m-1__2" @click="clickHistorySales(item.item_id)"><i class="fa fa-list"></i></button>
                  <button type="button" class="btn waves-effect waves-light btn-xs btn-success m-1__2" @click="clickHistoryPurchases(item.item_id)"><i class="fas fa-cart-plus"></i></button> -->
                                    <template v-if="!item.edit_unit_price">
                                        <h5
                                            class="font-weight-semibold text-right text-white"
                                        >
                                            <button
                                                v-if="configuration.options_pos && edit_unit_price"
                                                type="button"
                                                class="btn btn-xs btn-primary-pos"
                                                @click="clickOpenInputEditUP(index)">
                                                <span style="font-size:16px;">&#9998;</span>
                                            </button>
                                            ({{ item.unit_type_id }})
                                            {{ item.currency_type_symbol }}
                                            {{ item.sale_unit_price }}
                                        </h5>
                                    </template>
                                    <template v-else>
                                        <el-input
                                            min="0"
                                            v-model="item.edit_sale_unit_price"
                                            class="mt-3 mb-3"
                                            size="mini"
                                        >
                                            <el-button
                                                slot="append"
                                                icon="el-icon-check"
                                                type="primary"
                                                @click="
                                                    clickEditUnitPriceItem(
                                                        index
                                                    )
                                                "
                                            ></el-button>
                                            <el-button
                                                slot="append"
                                                icon="el-icon-close"
                                                type="danger"
                                                @click="
                                                    clickCancelUnitPriceItem(
                                                        index
                                                    )
                                                "
                                            ></el-button>
                                        </el-input>
                                    </template>
                                </div>
                                <div
                                    v-if="configuration.options_pos"
                                    class=" card-footer  bg-primary btn-group flex-wrap"
                                    style="width:100% !important; padding:0 !important; "
                                >
                                    <!-- <el-popover v-if="item.warehouses" placement="right" width="280"  trigger="hover">
                    <el-table  :data="item.warehouses">
                      <el-table-column width="150" property="warehouse_description" label="Ubicación"></el-table-column>
                      <el-table-column width="100" property="stock" label="Stock"></el-table-column>
                    </el-table>
                    <button type="button" style="width:100% !important;" slot="reference" class="btn btn-xs btn-default " @click="clickHistorySales(item.item_id)"><i class="fa fa-search"></i></button>
                  </el-popover> -->
                                    <!--<el-tooltip class="item" effect="dark" content="Visualizar stock" placement="bottom-end">
                    <button type="button" style="width:25% !important;"   class="btn btn-xs btn-primary-pos" @click="clickWarehouseDetail(item)">
                      <i class="fa fa-search"></i>
                    </button>
                  </el-tooltip>

                  <el-tooltip class="item" effect="dark" content="Visualizar historial de ventas del producto (precio venta) y cliente" placement="bottom-end">
                    <button type="button" style="width:25% !important;"   class="btn btn-xs btn-primary-pos" @click="clickHistorySales(item.item_id)"><i class="fa fa-list"></i></button>
                  </el-tooltip>

                  <el-tooltip class="item" effect="dark" content="Visualizar historial de compras del producto (precio compra)" placement="bottom-end">
                    <button type="button" style="width:25% !important;"  class="btn btn-xs btn-primary-pos" @click="clickHistoryPurchases(item.item_id)"><i class="fas fa-cart-plus"></i></button>
                  </el-tooltip>

                  <el-popover
                    placement="top-start"
                    title="Title"
                    width="400"
                    trigger="hover"
                    content="this is content, this is content, this is content">
                    <el-button slot="reference">Hov</el-button>
                </el-popover>-->

                                    <el-row style="width:100%">
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar stock"
                                                placement="bottom-end"
                                            >
                                                <button
                                                    style="width:100%"
                                                    type="button"
                                                    class="btn btn-xs btn-primary-pos"
                                                    @click="
                                                        clickWarehouseDetail(
                                                            item
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </el-tooltip>
                                        </el-col>
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar historial de ventas del producto (precio venta) y cliente"
                                                placement="bottom-end"
                                            >
                                                <button
                                                    type="button"
                                                    style="width:100%;"
                                                    class="btn btn-xs btn-primary-pos"
                                                    @click="
                                                        clickHistorySales(
                                                            item.item_id
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </el-tooltip>
                                        </el-col>
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar historial de compras del producto (precio compra)"
                                                placement="bottom-end"
                                            >
                                                <button
                                                    type="button"
                                                    style="width:100%"
                                                    class="btn btn-xs btn-primary-pos"
                                                    @click="
                                                        clickHistoryPurchases(
                                                            item.item_id
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fas fa-cart-plus"
                                                    ></i>
                                                </button>
                                            </el-tooltip>
                                        </el-col>
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar precios disponibles"
                                                placement="bottom-end"
                                            >
                                                <el-popover
                                                    placement="top"
                                                    title="Precios"
                                                    width="240"
                                                    trigger="click"
                                                >
                                                    <el-table
                                                        v-if="item.unit_type"
                                                        :data="item.unit_type"
                                                    >
                                                        <el-table-column
                                                            width="90"
                                                            label="Precio"
                                                        >
                                                            <template
                                                                slot-scope="{
                                                                    row
                                                                }"
                                                            >
                                                                <span
                                                                    v-if="
                                                                        row.price_default ==
                                                                            1
                                                                    "
                                                                >
                                                                    {{
                                                                        row.price1
                                                                    }}
                                                                </span>
                                                                <span
                                                                    v-else-if="
                                                                        row.price_default ==
                                                                            2
                                                                    "
                                                                >
                                                                    {{
                                                                        row.price2
                                                                    }}
                                                                </span>
                                                                <span
                                                                    v-else-if="
                                                                        row.price_default ==
                                                                            3
                                                                    "
                                                                >
                                                                    {{
                                                                        row.price3
                                                                    }}
                                                                </span>
                                                            </template>
                                                        </el-table-column>
                                                        <el-table-column
                                                            width="80"
                                                            label="Unidad"
                                                            property="unit_type_id"
                                                        ></el-table-column>
                                                        <el-table-column
                                                            width="80"
                                                            label=""
                                                        >
                                                            <template
                                                                slot-scope="{
                                                                    row
                                                                }"
                                                            >
                                                                <button
                                                                    @click="
                                                                        setPriceItem(
                                                                            row,
                                                                            index
                                                                        )
                                                                    "
                                                                    type="button"
                                                                    class="btn btn-custom btn-xs"
                                                                >
                                                                    <i
                                                                        class="fas fa-check"
                                                                    ></i>
                                                                </button>
                                                            </template>
                                                        </el-table-column>
                                                    </el-table>
                                                    <button
                                                        slot="reference"
                                                        type="button"
                                                        style="width:100%"
                                                        class="btn btn-xs btn-primary-pos"
                                                    >
                                                        <i
                                                            class="fa fa-money-bill-alt"
                                                        ></i>
                                                    </button>
                                                </el-popover>
                                            </el-tooltip>
                                        </el-col>
                                    </el-row>
                                </div>
                            </section>
                        </div>
                    </template>
                </div>

                <table-items
                    ref="table_items"
                    @clickAddItem="clickAddItem"
                    @clickWarehouseDetail="clickWarehouseDetail"
                    @clickHistorySales="clickHistorySales"
                    @clickHistoryPurchases="clickHistoryPurchases"
                    v-if="place == 'cat3'"
                    :records="items"
                    :typeUser="typeUser"
                    :visibleTagsCustomer="focusClienteSelect"
                ></table-items>

                <div v-if="place == 'prod' || place == 'cat2'" class="row">
                    <div class="col-md-12 text-center">
                        <el-pagination
                            @current-change="getRecords"
                            layout="total, prev, pager, next"
                            :total="pagination.total"
                            :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page"
                        >
                        </el-pagination>
                    </div>
                </div>
            </div>
            <div
                class="col-lg-4 col-md-6 bg-white m-0 p-0"
                style="height: calc(100vh - 110px)"
            >
                <div class="h-75 bg-light" style="overflow-y: auto">
                    <div class="row py-3 border-bottom m-0 p-0">
                        <div class="col-8">
                            <el-select
                                ref="select_person"
                                v-model="form.customer_id"
                                filterable
                                placeholder="Cliente"
                                @change="changeCustomer"
                                @keyup.native="keyupCustomer"
                                @keyup.enter.native="keyupEnterCustomer"
                                @focus="focusClienteSelect = true"
                                @blur="focusClienteSelect = false"
                            >
                                <el-option
                                    v-for="option in all_customers"
                                    :key="option.id"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                        </div>
                        <div class="col-4">
                            <div class="btn-group d-flex" role="group">
                                <a
                                    class="btn btn-sm btn-default w-100"
                                    @click.prevent="showDialogNewPerson = true"
                                >
                                    <i class="fas fa-plus fa-wf"></i>
                                </a>
                                <a
                                    class="btn btn-sm btn-default w-100"
                                    @click="clickDeleteCustomer"
                                >
                                    <i class="fas fa-trash fa-wf"></i>
                                </a>
                                <a
                                    class="btn btn-sm btn-default w-100"
                                    @click="selectCurrencyType"
                                >
                                    <template
                                        v-if="form.currency_type_id == 'PEN'"
                                    >
                                        <strong>S/</strong>
                                    </template>
                                    <template v-else>
                                        <strong>$</strong>
                                    </template>
                                    <!-- <i class="fa fa-usd" aria-hidden="true"></i> -->
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row py-1 border-bottom m-0 p-0">
                        <div class="col-12">
                            <table class="table table-sm table-borderless mb-0 pos-list-items">
                                <template v-for="(item, index) in form.items">
                                    <tr :key="index">
                                        <td style="width: 10px; text-align: center; vertical-align: top" class="pos-list-label">
                                            {{ item.unit_type_id }}
                                        </td>
                                        <td style="width: 80px; vertical-align: top">
                                            <el-input v-model="item.item.aux_quantity"
                                                      @input="clickAddItem(item, index, true)"
                                                      @keyup.enter.native="keyupEnterQuantity"></el-input>
                                            <!-- <el-input
                                                v-model="item.item.aux_quantity"
                                                :readonly="
                                                    item.item.calculate_quantity
                                                "
                                                class
                                                @input="
                                                    clickAddItem(
                                                        item,
                                                        index,
                                                        true
                                                    )
                                                "
                                                @keyup.enter.native="
                                                    keyupEnterQuantity
                                                "
                                            ></el-input> -->
                                            <!-- <el-input-number v-model="item.item.aux_quantity" @change="clickAddItem(item,index,true)" :min="1" :max="10"></el-input-number> -->
                                        </td>
                                        <td>
                                            <p class="item-description">
                                                {{ item.item.description }}
                                            </p>
                                            <small>
                                                {{ nameSets(item.item_id) }}
                                            </small>
                                            <!-- <p class="text-muted m-b-0"><small>Descuento 2%</small></p> -->
                                        </td>
                                        <!-- <td>
                      <p class="font-weight-semibold m-0 text-center">{{currency_type.symbol}}</p>
                    </td>
                    <td width="30%">
                      <p class="font-weight-semibold m-0 text-center">
                        <el-input
                          v-model="item.item.unit_price"
                          @blur="blurCalculateQuantity2(index)"
                        >
                        </el-input>
                      </p>
                    </td> -->

                                        <td style="width: 10px; text-align: center; vertical-align: top" class="pos-list-label">
<!--                                            <p-->
<!--                                                class="font-weight-semibold m-0 text-center"-->
<!--                                            >-->
                                                {{ currency_type.symbol }}
<!--                                            </p>-->
                                        </td>
                                        <td style="width: 80px; vertical-align: top">
<!--                                            <p class="font-weight-semibold m-0 text-center">-->
                                                <!-- {{currency_type.symbol}} {{item.total}} -->
                                            <template v-if="edit_unit_price">
                                                <el-input
                                                    v-model="item.total"
                                                    @input="calculateQuantity(index)"
                                                    @blur="blurCalculateQuantity(index)"
                                                    :readonly="!item.item.calculate_quantity">
                                                    <!--                                                     <template slot="prepend">{{ currency_type.symbol }}</template>-->
                                                </el-input>
                                            </template>
                                            <template v-else>
                                                {{ item.total }}
                                            </template>
<!--                                            </p>-->
                                        </td>
                                        <td class="text-right" style="width: 36px; padding-left: 0; padding-right: 0; vertical-align: top">
                                            <a class="btn btn-sm btn-default" @click="clickDeleteItem(index)">
                                                <i class="fas fa-trash fa-wf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="h-25 bg-light" style="overflow-y: auto">
                    <div
                        class="row border-top bg-light m-0 p-0 h-50 d-flex align-items-right pr-5 pt-2"
                    >
                        <div
                            class="col-md-12"
                            style="display: flex; flex-direction: column; align-items: flex-end;"
                        >
                            <table>
                                <tr
                                    v-if="form.total_exonerated > 0"
                                    class="font-weight-semibold  m-0"
                                >
                                    <td class="font-weight-semibold">
                                        OP.EXONERADAS
                                    </td>
                                    <td class="font-weight-semibold">:</td>
                                    <td class="text-right text-blue">
                                        {{ currency_type.symbol }}
                                        {{ form.total_exonerated }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="form.total_free > 0"
                                    class="font-weight-semibold  m-0"
                                >
                                    <td class="font-weight-semibold">
                                        OP.GRATUITAS
                                    </td>
                                    <td class="font-weight-semibold">:</td>
                                    <td class="text-right text-blue">
                                        {{ currency_type.symbol }}
                                        {{ form.total_free }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="form.total_unaffected > 0"
                                    class="font-weight-semibold  m-0"
                                >
                                    <td class="font-weight-semibold">
                                        OP.INAFECTAS
                                    </td>
                                    <td class="font-weight-semibold">:</td>
                                    <td class="text-right text-blue">
                                        {{ currency_type.symbol }}
                                        {{ form.total_unaffected }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="form.total_taxed > 0"
                                    class="font-weight-semibold  m-0"
                                >
                                    <td class="font-weight-semibold">
                                        OP.GRAVADA
                                    </td>
                                    <td class="font-weight-semibold">:</td>
                                    <td class="text-right text-blue">
                                        {{ currency_type.symbol }}
                                        {{ form.total_taxed }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="form.total_igv > 0"
                                    class="font-weight-semibold  m-0"
                                >
                                    <td class="font-weight-semibold">IGV</td>
                                    <td class="font-weight-semibold">:</td>
                                    <td class="text-right text-blue">
                                        {{ currency_type.symbol }}
                                        {{ form.total_igv }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="form.total_plastic_bag_taxes > 0"
                                    class="font-weight-semibold  m-0"
                                >
                                    <td class="font-weight-semibold">ICBPER</td>
                                    <td class="font-weight-semibold">:</td>
                                    <td class="text-right text-blue">
                                        {{ currency_type.symbol }}
                                        {{ form.total_plastic_bag_taxes }}
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- <div class="col-12 text-right px-0" v-if="form.total_taxed > 0">
              <h4 class="font-weight-semibold  m-0">
                <span class="font-weight-semibold">OP.GRAVADA: </span>
                <span class="text-blue">{{currency_type.symbol}} {{ form.total_taxed }}</span>
              </h4>
            </div>

            <div class="col-12 text-right px-0" v-if="form.total_free > 0">
              <h4 class="font-weight-semibold  m-0">
                <span class="font-weight-semibold">OP.GRATUITAS: </span>
                <span class="text-blue">{{currency_type.symbol}} {{ form.total_free }}</span>
              </h4>
            </div>

            <div class="col-12 text-right px-0" v-if="form.total_unaffected > 0">
              <h4 class="font-weight-semibold  m-0">
                <span class="font-weight-semibold">OP.INAFECTAS: </span>
                <span class="text-blue">{{currency_type.symbol}} {{ form.total_unaffected }}</span>
              </h4>
            </div>

            <div class="col-12 text-right px-0" v-if="form.total_exonerated > 0">
              <h4 class="font-weight-semibold  m-0">
                <span class="font-weight-semibold">OP.EXONERADAS: </span>
                <span class="text-blue">{{currency_type.symbol}} {{ form.total_exonerated }}</span>
              </h4>
            </div>

            <div class="col-12 text-right px-0" v-if="form.total_igv > 0">
              <h4 class="font-weight-semibold  m-0">
                <span class="font-weight-semibold">IGV: </span>
                <span class="text-blue">{{currency_type.symbol}} {{form.total_igv}}</span>
              </h4>
            </div> -->
                    </div>
                    <div
                        class="row text-white m-0 p-0 h-50 d-flex align-items-center"
                        @click="clickPayment"
                        v-bind:class="[
                            form.total > 0 ? 'bg-info pointer' : 'bg-dark'
                        ]"
                    >
                        <div class="col-6 text-center">
                            <i class="fas fa-chevron-circle-right fa fw h5"></i>
                            <span class="font-weight-semibold h5">PAGO</span>
                        </div>
                        <div class="col-6 text-center">
                            <h5 class="font-weight-semibold h5">
                                {{ currency_type.symbol }} {{ form.total }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <person-form
                :showDialog.sync="showDialogNewPerson"
                type="customers"
                :input_person="input_person"
                :external="true"
                :document_type_id="form.document_type_id"
            ></person-form>

            <item-form
                :showDialog.sync="showDialogNewItem"
                :external="true"
            ></item-form>
        </div>
        <template v-else>
            <payment-form
                :is_payment.sync="is_payment"
                :form="form"
                :currency-type-id-active="form.currency_type_id"
                :currency-type-active="currency_type"
                :exchange-rate-sale="form.exchange_rate_sale"
                :customer="customer"
                :soapCompany="soapCompany"
                :businessTurns="businessTurns"
            ></payment-form>
        </template>

        <history-sales-form
            :showDialog.sync="showDialogHistorySales"
            :item_id="history_item_id"
            :customer_id="form.customer_id"
        ></history-sales-form>

        <history-purchases-form
            :showDialog.sync="showDialogHistoryPurchases"
            :item_id="history_item_id"
        ></history-purchases-form>

        <warehouses-detail
            :showDialog.sync="showWarehousesDetail"
            :warehouses="warehousesDetail"
            :unit_type="unittypeDetail"
            :item_unit_types="[]"
        >
        </warehouses-detail>
    </div>
</template>
<style>
.el-select-dropdown__item.hover {
    /* background-color: red; */
    background-color: #e6e9ee;
}

/* The heart of the matter */
.testimonial-group > .row {
    overflow-x: auto;
    white-space: nowrap;
    overflow-y: hidden;
}

.testimonial-group > .row > .col-sm-3 {
    display: inline-block;
    float: none;
}

/* Decorations */
.col-sm-3 {
    height: 70px;
    margin-right: 0.5%;
    color: white;
    font-size: 18px;
    padding-bottom: 20px;
    padding-top: 18px;
    font-weight: bold;
}

.card-block {
    min-height: 220px;
}

.ex1 {
    overflow-x: scroll;
}

.cat_c {
    width: 100px;
    margin: 1%;
    padding: 3px;
    font-weight: bold;
    color: white;
    min-height: 90px;
}

.cat_c p {
    color: white;
}

.c-width {
    width: 80px !important;
    padding: 0 !important;
    margin-right: 0 !important;
}

.el-select-dropdown {
    max-width: 80% !important;
    margin-right: 1% !important;
}

.el-input-group__append {
    padding: 0 10px !important;
}
</style>

<script>
import Keypress from "vue-keypress";
import {calculateRowItem} from "../../../helpers/functions";
import PaymentForm from "./partials/payment.vue";
import ItemForm from "./partials/form.vue";
import {functions, exchangeRate} from "../../../mixins/functions";
import HistorySalesForm from "../../../../../modules/Pos/Resources/assets/js/views/history/sales.vue";
import HistoryPurchasesForm from "../../../../../modules/Pos/Resources/assets/js/views/history/purchases.vue";
import PersonForm from "../persons/form.vue";
import WarehousesDetail from "../items/partials/warehouses.vue";
import queryString from "query-string";
import TableItems from "./partials/table.vue";

export default {
    props: ["configuration", "soapCompany", "businessTurns", "typeUser"],
    components: {
        PaymentForm,
        ItemForm,
        HistorySalesForm,
        HistoryPurchasesForm,
        PersonForm,
        WarehousesDetail,
        Keypress,
        TableItems
    },
    mixins: [functions, exchangeRate],

    data() {
        return {
            place: "cat",
            history_item_id: null,
            search_item_by_barcode: false,
            warehousesDetail: [],
            unittypeDetail: [],
            input_person: {},
            showDialogHistoryPurchases: false,
            showDialogHistorySales: false,
            showDialogNewPerson: false,
            showDialogNewItem: false,
            loading: false,
            is_payment: false, //aq
            // is_payment: true,//aq
            showWarehousesDetail: false,
            resource: "pos",
            recordId: null,
            input_item: "",
            items: [],
            all_items: [],
            customers: [],
            affectation_igv_types: [],
            all_customers: [],
            establishment: null,
            currency_type: {},
            form_item: {},
            customer: {},
            row: {},
            user: {},
            form: {},
            categories: [],
            colors: ["#1cb973", "#bf7ae6", "#fc6304", "#9b4db4", "#77c1f3"],
            pagination: {},
            category_selected: "",
            focusClienteSelect: false
        };
    },
    async created() {
        await this.initForm();
        await this.getTables();
        this.events();

        await this.getFormPosLocalStorage();
        await this.initCurrencyType();
        this.customer = await this.getLocalStorageIndex("customer");

        if (document.querySelector(".sidebar-toggle")) {
            document.querySelector(".sidebar-toggle").click();
        }

        await this.selectDefaultCustomer();
    },

    computed: {
        classObjectCol() {
            let cols = this.configuration.colums_grid_item;

            let clase = "c3";
            switch (cols) {
                case 2:
                    clase = "50%";

                    break;
                case 3:
                    clase = "33.33%";

                    break;
                case 4:
                    clase = "25%";

                    break;
                case 5:
                    clase = "20%";

                    break;
                case 6:
                    clase = "16.66%";
                    break;
                default:
            }
            return {
                width: `${clase}`,
                padding: "5px"
            };
        },
        edit_unit_price() {
            if(this.typeUser === 'admin') {
                return true
            }
            if(this.typeUser === 'seller') {
                return this.configuration.allow_edit_unit_price_to_seller;
            }
            return false;
        }
    },
    methods: {
        keyupEnterQuantity() {
            this.initFocus();
        },
        handleFn112(response) {
            this.search_item_by_barcode = !this.search_item_by_barcode;
        },
        handleFn113() {
            this.setView("cat3");
        },
        initFocus() {
            this.$refs.ref_search_items.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        keyupTabCustomer(e) {
            // console.log(e.keyCode)
            if (e.keyCode === 9) {
                this.$refs.select_person.$el
                    .getElementsByTagName("input")[0]
                    .focus();
            }
        },
        keyupEnterAddItem() {
            if (this.place == "cat3") {
                return false;
            }

            if (this.items.length == 1) {
                this.clickAddItem(this.items[0], 0);
                this.filterItems();
                this.cleanInput();
            } else {
                this.$message.warning(
                    "No puede añadir directamente el producto al listado, hay más de uno ubicado en la búsqueda"
                );
            }
        },
        filterCategorie(id, mod = false) {
            if (id) {
                this.category_selected = id;
                this.getRecords();
            } else {
                this.category_selected = "";
                this.getRecords();
            }

            if (mod) {
                this.place = "cat2";
            } else {
                this.place = "prod";
            }
        },
        getRecords() {
            this.loading = true;
            return this.$http
                .get(
                    `/${this.resource}/items?${this.getQueryParameters()}&cat=${
                        this.category_selected
                    }`
                )
                .then(response => {
                    this.all_items = response.data.data;
                    this.filterItems();
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(
                        response.data.meta.per_page
                    );
                    this.loading = false;
                    if (response.data.meta.total > 0) {
                        this.pagination.total = response.data.meta.total;
                    } else {
                        this.pagination.total = 0;
                    }
                    this.fixItems();
                });
        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page
                    ? this.pagination.current_page
                    : 1,
                input_item: this.input_item,
                cat: this.category_selected,
                limit: this.limit
            });
        },
        getColor(i) {
            return this.colors[i % this.colors.length];
        },
        initCurrencyType() {
            this.currency_type = _.find(this.currency_types, {
                id: this.form.currency_type_id
            });
        },
        getFormPosLocalStorage() {
            let form_pos = localStorage.getItem("form_pos");
            form_pos = JSON.parse(form_pos);
            if (form_pos) {
                this.form = form_pos;
                this.initDateTimeIssue();
                // this.calculateTotal()
            }
        },
        initDateTimeIssue() {
            this.form.date_of_issue = moment().format("YYYY-MM-DD");
            this.form.time_of_issue = moment().format("HH:mm:ss");
            this.form.date_of_due = moment().format("YYYY-MM-DD");
        },
        setFormPosLocalStorage(form_param = null) {
            if (form_param) {
                localStorage.setItem("form_pos", JSON.stringify(form_param));
            } else {
                localStorage.setItem("form_pos", JSON.stringify(this.form));
            }
        },
        cancelFormPosLocalStorage() {
            localStorage.setItem("form_pos", JSON.stringify(null));
            this.setLocalStorageIndex("customer", null);
        },
        clickOpenInputEditUP(index) {
            this.items[index].edit_unit_price = true;
        },
        clickEditUnitPriceItem(index) {
            // console.log(index)
            let item_search = this.items[index];
            this.items[index].sale_unit_price = this.items[
                index
                ].edit_sale_unit_price;
            this.items[index].edit_unit_price = false;

            // console.log(item_search)
        },
        clickCancelUnitPriceItem(index) {
            // console.log(index)
            this.items[index].edit_unit_price = false;
        },
        setPriceItem(price, index) {
            let value = 0;
            switch (price.price_default) {
                case 1:
                    value = price.price1;
                    break;
                case 2:
                    value = price.price2;
                    break;
                case 3:
                    value = price.price3;
                    break;
            }

            this.items[index].sale_unit_price = value;
            this.items[index].unit_type_id = price.unit_type_id;
            this.items[index].presentation = price;
            this.$message.success("Precio seleccionado");
        },
        clickWarehouseDetail(item) {
            this.unittypeDetail = item.unit_type;
            this.warehousesDetail = item.warehouses;
            this.showWarehousesDetail = true;
        },
        clickHistoryPurchases(item_id) {
            this.history_item_id = item_id;
            this.showDialogHistoryPurchases = true;
            // console.log(item)
        },
        clickHistorySales(item_id) {
            if (!this.form.customer_id)
                return this.$message.error("Debe seleccionar el cliente");

            this.history_item_id = item_id;
            this.showDialogHistorySales = true;
            // console.log(item)
        },
        keyupEnterCustomer() {
            if (this.place == "cat3") {
                return false;
            }

            if (this.form.customer_id) {
                this.clickPayment();
                return;
            }

            if (this.input_person.number) {
                if (!isNaN(parseInt(this.input_person.number))) {
                    switch (this.input_person.number.length) {
                        case 8:
                            this.input_person.identity_document_type_id = "1";
                            this.showDialogNewPerson = true;
                            break;

                        case 11:
                            this.input_person.identity_document_type_id = "6";
                            this.showDialogNewPerson = true;
                            break;
                        default:
                            this.input_person.identity_document_type_id = "6";
                            this.showDialogNewPerson = true;
                            break;
                    }
                }
            }
        },
        keyupCustomer(e) {
            if (this.place == "cat3") {
                return false;
            }

            if (e.key !== "Enter") {
                this.input_person.number = this.$refs.select_person.$el.getElementsByTagName(
                    "input"
                )[0].value;
                let exist_persons = this.all_customers.filter(customer => {
                    let pos = customer.description.search(
                        this.input_person.number
                    );
                    return pos > -1;
                });

                this.input_person.number =
                    exist_persons.length == 0 ? this.input_person.number : null;
            }
        },
        calculateQuantity(index) {
            // console.log(this.form.items[index])
            if (this.form.items[index].item.calculate_quantity) {
                let quantity = _.round(
                    parseFloat(this.form.items[index].total) /
                    parseFloat(this.form.items[index].unit_price),
                    4
                );

                if (quantity) {
                    this.form.items[index].quantity = quantity;
                    this.form.items[index].item.aux_quantity = quantity;
                } else {
                    this.form.items[index].quantity = 0;
                    this.form.items[index].item.aux_quantity = 0;
                }
                // this.calculateTotal()
            }

            //  this.clickAddItem(this.form.items[index],index, true)
        },
        blurCalculateQuantity(index) {

            this.row = calculateRowItem(
                this.form.items[index],
                this.form.currency_type_id,
                1
            );

            // console.log(this.form.items[index])

            this.row["unit_type_id"] = this.form.items[index].unit_type_id;

            this.form.items[index] = this.row;
            this.calculateTotal();
            this.setFormPosLocalStorage();
        },
        blurCalculateQuantity2(index) {
            this.row = calculateRowItem(
                this.form.items[index],
                this.form.currency_type_id,
                1
            );
            this.form.items[index] = this.row;
            this.calculateTotal();
        },
        changeCustomer() {
            // console.log('clien 13')

            let customer = _.find(this.all_customers, {
                id: this.form.customer_id
            });
            this.customer = customer;

            if (this.configuration.default_document_type_03) {
                this.form.document_type_id = "03";
            } else {
                this.form.document_type_id =
                    customer.identity_document_type_id == "6" ? "01" : "03";
            }

            this.setLocalStorageIndex("customer", this.customer);
            this.setFormPosLocalStorage();
        },

        getLocalStorageIndex(key, re_default = null) {
            let ls_obj = localStorage.getItem(key);
            ls_obj = JSON.parse(ls_obj);

            if (ls_obj) {
                return ls_obj;
            }

            return re_default;
        },
        setLocalStorageIndex(key, obj) {
            localStorage.setItem(key, JSON.stringify(obj));
        },
        async events() {
            await this.$eventHub.$on("initInputPerson", () => {
                this.initInputPerson();
            });

            await this.$eventHub.$on(
                "eventSetFormPosLocalStorage",
                form_param => {
                    this.setFormPosLocalStorage(form_param);
                }
            );

            await this.$eventHub.$on("cancelSale", () => {
                this.is_payment = false;
                this.initForm();
                this.changeExchangeRate();
                this.cancelFormPosLocalStorage();
                this.selectDefaultCustomer();
                this.$nextTick(() => {
                    this.initFocus();
                });
            });

            // await this.$eventHub.$on("indexInitFocus", () => {
            //   if(!this.is_payment) this.initFocus()
            // });

            await this.$eventHub.$on("reloadDataPersons", customer_id => {
                this.reloadDataCustomers(customer_id);
                this.setFormPosLocalStorage();
            });

            await this.$eventHub.$on("reloadDataItems", item_id => {
                this.reloadDataItems(item_id);
            });

            await this.$eventHub.$on("saleSuccess", () => {
                // this.is_payment = false
                this.initForm();
                this.getTables();
                this.setFormPosLocalStorage();
            });
        },
        initForm() {
            this.form = {
                establishment_id: null,
                document_type_id: "03",
                series_id: null,
                prefix: null,
                number: "#",
                date_of_issue: moment().format("YYYY-MM-DD"),
                time_of_issue: moment().format("HH:mm:ss"),
                customer_id: null,
                currency_type_id: "PEN",
                purchase_order: null,
                exchange_rate_sale: 1,
                total_prepayment: 0,
                total_charge: 0,
                total_discount: 0,
                total_exportation: 0,
                total_free: 0,
                total_taxed: 0,
                total_unaffected: 0,
                total_exonerated: 0,
                total_igv: 0,
                total_base_isc: 0,
                total_isc: 0,
                total_base_other_taxes: 0,
                total_other_taxes: 0,
                total_plastic_bag_taxes: 0,
                total_taxes: 0,
                total_value: 0,
                total: 0,
                operation_type_id: "0101",
                date_of_due: moment().format("YYYY-MM-DD"),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                payments: [],
                hotel: {},
                additional_information: null,
                actions: {
                    format_pdf: "a4"
                },
                reference_data: null
            };

            this.initFormItem();
            this.changeDateOfIssue();
            this.initInputPerson();
        },
        initInputPerson() {
            this.input_person = {
                number: "",
                identity_document_type_id: ""
            };
        },
        initFormItem() {
            this.form_item = {
                item_id: null,
                item: {},
                affectation_igv_type_id: null,
                affectation_igv_type: {},
                has_isc: false,
                system_isc_type_id: null,
                calculate_quantity: false,
                percentage_isc: 0,
                suggested_price: 0,
                quantity: 1,
                aux_quantity: 1,
                unit_price_value: 0,
                unit_price: 0,
                charges: [],
                discounts: [],
                attributes: [],
                has_igv: false,
                has_plastic_bag_taxes: false,
            };
        },
        async clickPayment() {
            let flag = 0;
            this.form.items.forEach(row => {
                if (row.aux_quantity < 0 || row.total < 0 || isNaN(row.total)) {
                    flag++;
                }
            });

            if (flag > 0)
                return this.$message.error("Cantidad negativa o incorrecta");
            if (!this.form.customer_id)
                return this.$message.error("Seleccione un cliente");
            if (!this.form.items[0])
                return this.$message.error("Seleccione un producto");
            this.form.establishment_id = this.establishment.id;
            this.loading = true;
            await this.sleep(800);
            this.is_payment = true;
            this.loading = false;
        },
        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },
        clickDeleteCustomer() {
            this.form.customer_id = null;
            this.setFormPosLocalStorage();
        },
        async clickAddItem(item, index, input = false) {
            this.loading = true;
            let exchangeRateSale = this.form.exchange_rate_sale;

            // console.log(item.unit_type_id)
            // console.log(exist_item)
            // console.log(item)

            let exist_item = _.find(this.form.items, {
                item_id: item.item_id,
                unit_type_id: item.unit_type_id
            });

            console.log(exist_item)

            let pos = this.form.items.indexOf(exist_item);
            let response = null;

            if (exist_item) {

                if (input) {

                    response = await this.getStatusStock(
                        item.item_id,
                        exist_item.item.aux_quantity
                    );
                    if (!response.success) {
                        item.item.aux_quantity = item.quantity;
                        this.loading = false;
                        return this.$message.error(response.message);
                    }

                    exist_item.quantity = exist_item.item.aux_quantity;
                } else {

                    response = await this.getStatusStock(
                        item.item_id,
                        parseFloat(exist_item.item.aux_quantity) + 1
                    );
                    if (!response.success) {
                        this.loading = false;
                        return this.$message.error(response.message);
                    }

                    exist_item.quantity++;
                    exist_item.item.aux_quantity++;
                }

                // console.log(exist_item)
                let search_item_bd = await _.find(this.items, {
                    item_id: item.item_id
                });

                if (search_item_bd) {
                    exist_item.item.unit_price = parseFloat(
                        search_item_bd.sale_unit_price
                    );
                }

                let unit_price = exist_item.item.has_igv
                    ? exist_item.item.sale_unit_price
                    : exist_item.item.sale_unit_price * 1.18;
                // exist_item.unit_price = unit_price
                exist_item.item.unit_price = unit_price;

                exist_item.has_plastic_bag_taxes = exist_item.item.has_plastic_bag_taxes;

                this.row = calculateRowItem(
                    exist_item,
                    this.form.currency_type_id,
                    exchangeRateSale
                );


                this.row["unit_type_id"] = item.unit_type_id;

                this.form.items[pos] = this.row;
            } else {

                response = await this.getStatusStock(
                    item.item_id,
                    item.presentation
                        ? parseInt(item.presentation.quantity_unit)
                        : 1
                );
                if (!response.success) {
                    this.loading = false;
                    return this.$message.error(response.message);
                }

                this.form_item.item = item;
                this.form_item.unit_price_value = this.form_item.item.sale_unit_price;
                this.form_item.has_igv = this.form_item.item.has_igv;
                this.form_item.has_plastic_bag_taxes = this.form_item.item.has_plastic_bag_taxes;
                this.form_item.affectation_igv_type_id = this.form_item.item.sale_affectation_igv_type_id;
                this.form_item.quantity = 1;
                this.form_item.aux_quantity = 1;

                let unit_price = this.form_item.has_igv
                    ? this.form_item.unit_price_value
                    : this.form_item.unit_price_value * 1.18;

                this.form_item.unit_price = unit_price;
                this.form_item.item.unit_price = unit_price;
                this.form_item.item.presentation = item.presentation
                    ? item.presentation
                    : null;

                this.form_item.charges = [];
                this.form_item.discounts = [];
                this.form_item.attributes = [];
                this.form_item.affectation_igv_type = _.find(
                    this.affectation_igv_types,
                    {id: this.form_item.affectation_igv_type_id}
                );

                // console.log(this.form_item)
                this.row = calculateRowItem(
                    this.form_item,
                    this.form.currency_type_id,
                    exchangeRateSale
                );
                // console.log(this.row)

                // this.row['unit_type_id'] = item.presentation ? item.presentation.unit_type_id : 'NIU';

                this.row["unit_type_id"] = item.presentation
                    ? item.presentation.unit_type_id
                    : this.form_item.item.unit_type_id;

                this.form.items.unshift(this.row);
                item.aux_quantity = 1;
            }

            // console.log("pos", this.row);

            this.$notify({
                title: "",
                message: "Producto añadido!",
                type: "success",
                duration: 700
            });

            this.cleanInput();

            if (!input) {
                this.initFocus();
            }

            // console.log(this.row)
            // console.log(this.form.items)
            await this.calculateTotal();
            this.loading = false;

            await this.setFormPosLocalStorage();
        },
        async getStatusStock(item_id, quantity) {
            let data = {};
            if (!quantity) quantity = 0;
            await this.$http
                .get(`/${this.resource}/validate_stock/${item_id}/${quantity}`)
                .then(response => {
                    data = response.data;
                });
            return data;
        },
        async clickDeleteItem(index) {
            this.form.items.splice(index, 1);

            this.calculateTotal();

            await this.setFormPosLocalStorage();
        },

        calculateTotal() {

            let total_discount = 0;
            let total_charge = 0;
            let total_exportation = 0;
            let total_taxed = 0;
            let total_exonerated = 0;
            let total_unaffected = 0;
            let total_free = 0;
            let total_igv = 0;
            let total_value = 0;
            let total = 0;
            let total_plastic_bag_taxes = 0

            this.form.items.forEach(row => {
                total_discount += parseFloat(row.total_discount);
                total_charge += parseFloat(row.total_charge);

                if (row.affectation_igv_type_id === "10") {
                    total_taxed += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "20") {
                    total_exonerated += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "30") {
                    total_unaffected += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "40") {
                    total_exportation += parseFloat(row.total_value);
                }
                if (
                    ["10", "20", "30", "40"].indexOf(
                        row.affectation_igv_type_id
                    ) < 0
                ) {
                    total_free += parseFloat(row.total_value);
                }
                if (
                    ["10", "20", "30", "40"].indexOf(
                        row.affectation_igv_type_id
                    ) > -1
                ) {
                    total_igv += parseFloat(row.total_igv);
                    total += parseFloat(row.total);
                }
                total_value += parseFloat(row.total_value);
                total_plastic_bag_taxes += parseFloat(row.total_plastic_bag_taxes)

            });

            this.form.total_exportation = _.round(total_exportation, 2);
            this.form.total_exonerated = _.round(total_exonerated, 2);
            this.form.total_taxed = _.round(total_taxed, 2);
            this.form.total_exonerated = _.round(total_exonerated, 2);

            // this.form.total_taxed =
            //   _.round(total_taxed, 2) + this.form.total_exonerated;
            // this.form.total_exonerated = _.round(total_exonerated, 2)
            this.form.total_unaffected = _.round(total_unaffected, 2);
            this.form.total_free = _.round(total_free, 2);
            this.form.total_igv = _.round(total_igv, 2);
            this.form.total_value = _.round(total_value, 2);
            this.form.total_taxes = _.round(total_igv, 2);
            this.form.total_plastic_bag_taxes = _.round(total_plastic_bag_taxes, 2)
            // this.form.total = _.round(total, 2);
            this.form.total = _.round(total + this.form.total_plastic_bag_taxes, 2)

        },
        changeDateOfIssue() {
            // this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
            //     this.form.exchange_rate_sale = response
            // })
        },
        changeExchangeRate() {
            this.searchExchangeRateByDate(this.form.date_of_issue).then(
                response => {
                    this.form.exchange_rate_sale = response;
                }
            );
        },
        async getTables() {
            await this.$http.get(`/${this.resource}/tables`).then(response => {
                //this.all_items = response.data.items;
                this.affectation_igv_types =
                    response.data.affectation_igv_types;
                this.all_customers = response.data.customers;
                this.establishment = response.data.establishment;
                this.currency_types = response.data.currency_types;
                this.user = response.data.user;
                this.form.currency_type_id =
                    this.currency_types.length > 0
                        ? this.currency_types[0].id
                        : null;
                this.renderCategories(response.data.categories);
                // this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                // this.changeCurrencyType();
                //this.filterItems();
                this.changeDateOfIssue();
                this.changeExchangeRate();
            });
        },
        selectDefaultCustomer() {
            if (this.establishment.customer_id && !this.form.customer_id) {
                this.form.customer_id = this.establishment.customer_id;
                this.changeCustomer();
            }
        },
        renderCategories(source) {
            const contex = this;
            this.categories = source.map((obj, index) => {
                return {
                    id: obj.id,
                    name: obj.name,
                    color: contex.getColor(index)
                };
            });

            this.categories.unshift({
                id: null,
                name: "Todos",
                color: "#2C8DE3"
            });
        },
        async searchItems() {
            if (this.input_item.length > 0) {
                this.loading = true;
                let parameters = `input_item=${this.input_item}&cat=${
                    this.category_selected
                }`;

                await this.$http
                    .get(`/${this.resource}/search_items_cat?${parameters}`)
                    .then(response => {
                        if (response.data.data.length > 0) {
                            this.all_items = response.data.data;
                            this.filterItems();
                            this.pagination = response.data.meta;
                            this.pagination.per_page = parseInt(
                                response.data.meta.per_page
                            );
                            this.fixItems();
                            this.loading = false;
                        } else {
                            this.loading = false;
                            this.filterItems();
                        }
                    });
            } else {
                this.getRecords();
                this.filterItems();
            }
        },
        async searchItemsBarcode() {
            // console.log(query)
            // console.log("in:" + this.input_item)
            if (this.input_item.length > 1) {
                this.loading = true;
                let parameters = `input_item=${this.input_item}`;

                await this.$http
                    .get(`/${this.resource}/search_items?${parameters}`)
                    .then(response => {
                        console.log("buah");
                        this.items = response.data.items;
                        this.enabledSearchItemsBarcode();
                        this.loading = false;
                        if (this.items.length == 0) {
                            this.filterItems();
                        }
                    });
            } else {
                await this.filterItems();
            }
        },
        fixItems(){
            this.items = this.all_items.map(i => {
                /** Si description es vacio y hay nombre */
                if(i.name !== undefined) {
                    if ( i.description === undefined || i.description == null ) {
                        i.description = i.name;
                    }
                }
                /** Si description es vacio aun */
                if(i.description == null){
                    i.description = i.internal_id;
                }

                return i;
            });
            this.all_items =this.items;
        },
        enabledSearchItemsBarcode() {
            if (this.search_item_by_barcode) {
                if (this.items.length == 1) {
                    // console.log(this.items)
                    this.clickAddItem(this.items[0], 0);
                    this.filterItems();
                }

                this.cleanInput();
            }
        },
        changeSearchItemBarcode() {
            this.cleanInput();
        },
        cleanInput() {
            this.input_item = null;
        },
        filterItems() {
            if (this.place === "cat3") {
                this.items = this.all_items;
            } else {
                this.items = this.all_items.map(i => {
                    if (i.brand) {
                        i.description = `${i.description} - ${i.brand}`;
                    }
                    return i;
                });
            }
        },
        reloadDataCustomers(customer_id) {
            this.$http
                .get(`/${this.resource}/table/customers`)
                .then(response => {
                    this.all_customers = response.data;
                    this.form.customer_id = customer_id;
                    this.changeCustomer();
                });
        },
        reloadDataItems(item_id) {
            this.$http.get(`/${this.resource}/table/items`).then(response => {
                this.all_items = response.data;
                this.fixItems();
                this.filterItems();
            });
        },
        selectCurrencyType() {
            this.form.currency_type_id =
                this.form.currency_type_id === "PEN" ? "USD" : "PEN";
            this.changeCurrencyType();
        },
        async changeCurrencyType() {
            // console.log(this.form.currency_type_id)
            this.currency_type = await _.find(this.currency_types, {
                id: this.form.currency_type_id
            });
            let items = [];
            this.form.items.forEach(row => {
                items.push(
                    calculateRowItem(
                        row,
                        this.form.currency_type_id,
                        this.form.exchange_rate_sale
                    )
                );
            });
            this.form.items = items;
            this.calculateTotal();

            await this.setFormPosLocalStorage();
        },
        openFullWindow() {
            location.href = `/${this.resource}/pos_full`;
        },
        back() {
            this.all_items = [];
            this.place = "cat";
            this.loading = false;
        },
        async setView(view) {
            this.place = view;

            if (view == "cat3") {
                this.category_selected = "";
                await this.getRecords();
                this.$refs.table_items.reset();
            }
        },
        nameSets(id) {
            let row = this.items.find(x => x.item_id == id);
            if (row) {
                if (row.sets.length > 0) {
                    return row.sets.join("-");
                } else {
                    return "";
                }
            }
        },
        listReverse(items) {
            console.log(items);
            console.log(_.reverse(items));
            return _.reverse(items);
        },
        DescriptionLength(item){
            if(item.description === undefined) return 0;
            if(item.description == null) return 0;
            return item.description.length;
        }
    }
};
</script>
