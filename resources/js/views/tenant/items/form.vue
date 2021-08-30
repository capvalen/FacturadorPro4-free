<template>
    <el-dialog width="65%" :title="titleDialog" :visible="showDialog" :close-on-click-modal="false" @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Nombre<span class="text-danger">*</span></label>
                            <el-input v-model="form.description" dusk="description"></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>

                     <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.second_name}">
                            <label class="control-label">Nombre secundario </label>
                            <el-input v-model="form.second_name" dusk="second_name"></el-input>
                            <small class="form-control-feedback" v-if="errors.second_name" v-text="errors.second_name[0]"></small>
                        </div>
                    </div>

                     <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Descripción</label>
                            <el-input v-model="form.name" dusk="name"></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.model}">
                            <label class="control-label">Modelo</label>
                            <el-input v-model="form.model" dusk="model"></el-input>
                            <small class="form-control-feedback" v-if="errors.model" v-text="errors.model[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.unit_type_id}">
                            <label class="control-label">Unidad</label>
                            <el-select v-model="form.unit_type_id" dusk="unit_type_id">
                                <el-option v-for="option in unit_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.unit_type_id" v-text="errors.unit_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                            <label class="control-label">Moneda</label>
                            <el-select v-model="form.currency_type_id" dusk="currency_type_id">
                                <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.sale_unit_price}">
                            <label class="control-label">Precio Unitario (Venta) <span class="text-danger">*</span></label>
                            <el-input v-model="form.sale_unit_price" dusk="sale_unit_price" @input="calculatePercentageOfProfitBySale"></el-input>
                            <small class="form-control-feedback" v-if="errors.sale_unit_price" v-text="errors.sale_unit_price[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.sale_affectation_igv_type_id}">
                            <label class="control-label">Tipo de afectación (Venta)</label>
                            <el-select v-model="form.sale_affectation_igv_type_id" @change="changeAffectationIgvType">
                                <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.sale_affectation_igv_type_id" v-text="errors.sale_affectation_igv_type_id[0]"></small>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.category_id}">
                            <label class="control-label">
                                Categoría
                            </label>

                            <a href="#" v-if="form_category.add == false" class="control-label font-weight-bold text-info" @click="form_category.add = true"> [ + Nuevo]</a>
                            <a href="#" v-if="form_category.add == true" class="control-label font-weight-bold text-info" @click="saveCategory()"> [ + Guardar]</a>
                            <a href="#" v-if="form_category.add == true" class="control-label font-weight-bold text-danger" @click="form_category.add = false"> [ Cancelar]</a>
                            <el-input   v-if="form_category.add == true" v-model="form_category.name" dusk="item_code" style="margin-bottom:1.5%;"></el-input>

                            <el-select v-if="form_category.add == false" v-model="form.category_id" filterable clearable>
                                <el-option v-for="option in categories" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.category_id" v-text="errors.category_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.brand_id}">
                            <label class="control-label">
                                Marca
                            </label>

                            <a href="#" v-if="form_brand.add == false" class="control-label font-weight-bold text-info" @click="form_brand.add = true"> [ + Nuevo]</a>
                            <a href="#" v-if="form_brand.add == true" class="control-label font-weight-bold text-info" @click="saveBrand()"> [ + Guardar]</a>
                            <a href="#" v-if="form_brand.add == true" class="control-label font-weight-bold text-danger" @click="form_brand.add = false"> [ Cancelar]</a>
                            <el-input   v-if="form_brand.add == true" v-model="form_brand.name" dusk="item_code" style="margin-bottom:1.5%;"></el-input>

                            <el-select v-if="form_brand.add == false" v-model="form.brand_id" filterable clearable >
                                <el-option v-for="option in brands" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.brand_id" v-text="errors.brand_id[0]"></small>
                        </div>
                    </div>

                    <div v-show="form.unit_type_id !='ZZ'" class="col-md-3 center-el-checkbox">
                        <div class="form-group" :class="{'has-danger': errors.calculate_quantity}">
                            <el-checkbox v-model="form.calculate_quantity">Calcular cantidad por precio</el-checkbox><br>
                            <small class="form-control-feedback" v-if="errors.calculate_quantity" v-text="errors.calculate_quantity[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3 center-el-checkbox" v-show="show_has_igv">
                        <div class="form-group" :class="{'has-danger': errors.has_igv}">
                            <el-checkbox v-model="form.has_igv">Incluye Igv {{configuration.include_igv}}</el-checkbox><br>
                            <small class="form-control-feedback" v-if="errors.has_igv" v-text="errors.has_igv[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3 center-el-checkbox">
                        <div class="form-group" :class="{'has-danger': errors.has_plastic_bag_taxes}">
                            <el-checkbox v-model="form.has_plastic_bag_taxes">Impuesto a la Bolsa Plástica</el-checkbox><br>
                            <small class="form-control-feedback" v-if="errors.has_plastic_bag_taxes" v-text="errors.has_plastic_bag_taxes[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.internal_id}">
                            <label class="control-label">Código Interno
                                <el-tooltip class="item" effect="dark" content="Código interno de la empresa para el control de sus productos" placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.internal_id" dusk="internal_id"></el-input>
                            <small class="form-control-feedback" v-if="errors.internal_id" v-text="errors.internal_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" :class="{'has-danger': errors.item_code}">
                            <label class="control-label">Código Sunat
                                <el-tooltip class="item" effect="dark" content="Código proporcionado por SUNAT, campo obligatorio para exportaciones" placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.item_code" dusk="item_code"></el-input>
                            <small class="form-control-feedback" v-if="errors.item_code" v-text="errors.item_code[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3" v-show="recordId==null && form.unit_type_id !='ZZ'">
                        <div class="form-group" :class="{'has-danger': errors.stock}">
                            <label class="control-label">Stock Inicial</label>
                            <el-input v-model="form.stock" ></el-input>
                            <small class="form-control-feedback" v-if="errors.stock" v-text="errors.stock[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3" v-show="form.unit_type_id !='ZZ'">
                        <div class="form-group" :class="{'has-danger': errors.stock_min}">
                            <label class="control-label">Stock Mínimo</label>
                            <el-input v-model="form.stock_min"></el-input>
                            <small class="form-control-feedback" v-if="errors.stock_min" v-text="errors.stock_min[0]"></small>
                        </div>
                    </div>

                    <div  v-show="form.unit_type_id !='ZZ'" class="col-md-3 center-el-checkbox" >
                        <div class="form-group"  >
                            <el-checkbox v-model="form.lots_enabled" @change="changeLotsEnabled">¿Maneja lotes?</el-checkbox><br>
                        </div>
                    </div>
                    <div class="col-md-3" v-show="form.unit_type_id !='ZZ' && form.lots_enabled">
                        <div class="form-group" :class="{'has-danger': errors.lot_code}">
                            <label class="control-label">Código lote</label>
                            <el-input v-model="form.lot_code"></el-input>
                            <small class="form-control-feedback" v-if="errors.lot_code" v-text="errors.lot_code[0]"></small>
                        </div>
                    </div>

                     <div  v-show="form.unit_type_id !='ZZ'" class="col-md-3 center-el-checkbox" >
                        <div class="form-group">
                            <el-checkbox v-model="form.series_enabled" @change="changeLotsEnabled">¿Maneja series?</el-checkbox><br>
                        </div>
                    </div>
                    <div class="col-md-3" v-show="form.unit_type_id !='ZZ' && form.series_enabled && !recordId">
                        <div class="form-group" :class="{'has-danger': errors.lot_code}">
                            <label class="control-label">Ingrese series</label>
                            <el-button style="margin-top:2%;" type="primary" icon="el-icon-edit-outline"  @click.prevent="clickLotcode"></el-button>
                            <small class="form-control-feedback" v-if="errors.lot_code" v-text="errors.lot_code[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3 center-el-checkbox" >
                        <div class="form-group"  >
                            <el-checkbox v-model="form.has_perception" @change="changeHasPerception">Incluye percepción</el-checkbox><br>
                        </div>
                    </div>
                    <div class="col-md-3 center-el-checkbox" v-show="form.has_perception">
                        <div class="form-group"  >
                            <label class="control-label">Porcentaje de percepción</label>
                            <el-input v-model="form.percentage_perception"></el-input>
                        </div>
                    </div>
                    <div  class="col-md-3" v-show="recordId==null" v-if="form.unit_type_id !='ZZ'">
                        <div class="form-group" :class="{'has-danger': errors.warehouse_id}">
                            <label class="control-label">
                                Almacén
                                <el-tooltip class="item" effect="dark" content="Si no selecciona almacén, se asignará por defecto el relacionado al establecimiento" placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-select v-model="form.warehouse_id" filterable >
                                <el-option v-for="option in warehouses" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.warehouse_id" v-text="errors.warehouse_id[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3" v-show="form.unit_type_id !='ZZ'">
                        <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                            <label class="control-label">Fec. Vencimiento</label>
                            <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="true"></el-date-picker>
                            <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-3" >
                        <div class="form-group" :class="{'has-danger': errors.line}">
                            <label class="control-label">
                               Línea de producto
                                <el-tooltip class="item" effect="dark" content="Grupo de productos que tienen una relación directa entre sí" placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.line" >
                            </el-input>
                            <small class="form-control-feedback" v-if="errors.line" v-text="errors.line[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-3" >
                        <div class="form-group" :class="{'has-danger': errors.barcode}">
                            <label class="control-label">Código de barra</label>
                            <el-input v-model="form.barcode" ></el-input>
                            <small class="form-control-feedback" v-if="errors.barcode" v-text="errors.barcode[0]"></small>
                        </div>
                    </div>
                    <div class="col-12" v-show="form.unit_type_id !='ZZ'">
                        <h5 class="separator-title">Precios por almacenes</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr v-for="w in warehouses" :key="w.id">
                                        <td>{{ w.description }}</td>
                                        <td width="150">
                                            <el-input placeholder="Precio" v-model="w.price" type="number" min="0" step="0.01"></el-input>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-show="form.unit_type_id !='ZZ'" class="col-md-12">
                        <h5 class="separator-title ">
                            Listado de precios
                            <el-tooltip class="item" effect="dark" content="Aplica para realizar compra/venta en presentacion de diferentes precios y/o cantidades" placement="top">
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                             <a href="#" class="control-label font-weight-bold text-info" @click="clickAddRow"> [ + Nuevo]</a>
                        </h5>
                    </div>

                    <div v-show="form.unit_type_id !='ZZ'" class="col-md-12" v-if="form.item_unit_types.length > 0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">Unidad</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">
                                        Factor
                                        <el-tooltip class="item" effect="dark" content="Cantidad de unidades" placement="top">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </th>
                                    <th class="text-center">Precio 1</th>
                                    <th class="text-center">Precio 2</th>
                                    <th class="text-center">Precio 3</th>
                                    <th class="text-center">P. Defecto</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row, index) in form.item_unit_types" :key="index">
                                    <template v-if="row.id">
                                        <td class="text-center">{{row.unit_type_id}}</td>
                                        <td class="text-center">{{row.description}}</td>
                                        <td class="text-center">{{row.quantity_unit}}</td>
                                        <td class="text-center"><el-input v-model="row.price1"></el-input></td>
                                        <td class="text-center"><el-input v-model="row.price2"></el-input></td>
                                        <td class="text-center"><el-input v-model="row.price3"></el-input></td>
                                        <td class="text-center">Precio {{row.price_default}}</td>
                                        <td class="series-table-actions text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td>
                                            <div class="form-group"  >
                                                <el-select v-model="row.unit_type_id" dusk="item_unit_type.unit_type_id">
                                                    <el-option v-for="option in unit_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                </el-select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" >
                                            <el-input v-model="row.description"></el-input>
                                        </div>
                                        </td>
                                        <td>
                                            <div class="form-group" >
                                                <el-input v-model="row.quantity_unit"></el-input>
                                                <!-- <small class="form-control-feedback" v-if="errors.quantity_unit" v-text="errors.quantity_unit[0]"></small> -->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" >
                                                <el-input v-model="row.price1"></el-input>
                                                <!-- <small class="form-control-feedback" v-if="errors.stock_min" v-text="errors.stock_min[0]"></small> -->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <el-input v-model="row.price2"></el-input>
                                                <!-- <small class="form-control-feedback" v-if="errors.stock_min" v-text="errors.stock_min[0]"></small> -->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <el-input v-model="row.price3"></el-input>
                                                <!-- <small class="form-control-feedback" v-if="errors.stock_min" v-text="errors.stock_min[0]"></small> -->
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <el-radio-group v-model="row.price_default">
                                                    <el-radio :label="1" class="d-block">Precio 1</el-radio>
                                                    <el-radio :label="2" class="d-block">Precio 2</el-radio>
                                                    <el-radio :label="3" class="d-block">Precio 3</el-radio>
                                                </el-radio-group>
                                            </div>
                                        </td>
                                        <td class="series-table-actions text-right">
                                            <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickSubmit(index)">
                                                <i class="fa fa-check"></i>
                                            </button> -->
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </template>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div v-if="attribute_types.length > 0" class="col-md-12">
                        <h5 class="separator-title ">
                           Atributos
                            <el-tooltip class="item" effect="dark" content="Diferentes presentaciones para la venta del producto" placement="top">
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                            <a href="#" class="control-label font-weight-bold text-info" @click.prevent="clickAddAttribute">[+ Agregar]</a>
                        </h5>
                    </div>
                    <div v-if="form.attributes.length > 0" class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row, index) in form.attributes" :key="index">
                                    <td>
                                        <el-select v-model="row.attribute_type_id" filterable @change="changeAttributeType(index)">
                                            <el-option v-for="option in attribute_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                        </el-select>
                                    </td>
                                    <td>
                                        <el-input v-model="row.value"></el-input>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger" @click.prevent="clickRemoveAttribute(index)">x</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <h5 class="separator-title">Campos adicionales</h5>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-md-3">
                            <div class="form-group" >
                                <label class="control-label">Imágen <span class="text-danger"></span></label>
                                <el-upload class="avatar-uploader"
                                        :data="{'type': 'items'}"
                                        :headers="headers"
                                        :action="`/${resource}/upload`"
                                        :show-file-list="false"
                                        :on-success="onSuccess">
                                    <img v-if="form.image_url" :src="form.image_url" class="avatar">
                                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                                </el-upload>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="row">

                                <div class="short-div col-md-8">
                                    <div class="form-group" :class="{'has-danger': errors.purchase_affectation_igv_type_id}">
                                        <label class="control-label">Tipo de afectación (Compra)</label>
                                        <el-select v-model="form.purchase_affectation_igv_type_id"  @change="changePurchaseAffectationIgvType">
                                            <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                        </el-select>
                                        <small class="form-control-feedback" v-if="errors.purchase_affectation_igv_type_id" v-text="errors.purchase_affectation_igv_type_id[0]"></small>
                                    </div>
                                </div>

                                <div class="short-div col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.purchase_unit_price}">
                                        <label class="control-label">Precio Unitario (Compra)</label>
                                        <el-input v-model="form.purchase_unit_price" dusk="purchase_unit_price" @input="calculatePercentageOfProfitByPurchase"></el-input>
                                        <small class="form-control-feedback" v-if="errors.purchase_unit_price" v-text="errors.purchase_unit_price[0]"></small>
                                    </div>
                                </div>
                                <div class="short-div col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.percentage_of_profit}">
                                        <label class="control-label">
                                            <el-checkbox v-model="enabled_percentage_of_profit" @change="changeEnabledPercentageOfProfit"></el-checkbox>
                                            Porcentaje de ganancia (%)
                                        </label>
                                        <el-input v-model="form.percentage_of_profit" :disabled="!enabled_percentage_of_profit" @input="calculatePercentageOfProfitByPercentage"></el-input>
                                        <small class="form-control-feedback" v-if="errors.percentage_of_profit" v-text="errors.percentage_of_profit[0]"></small>
                                    </div>
                                </div>

                                <div class="short-div col-md-4 center-el-checkbox"  v-show="purchase_show_has_igv">
                                    <div class="form-group" :class="{'has-danger': errors.purchase_has_igv}">
                                        <el-checkbox v-model="form.purchase_has_igv">Incluye Igv (Compra)</el-checkbox><br>
                                        <small class="form-control-feedback" v-if="errors.purchase_has_igv" v-text="errors.purchase_has_igv[0]"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>

        <lots-form
            :showDialog.sync="showDialogLots"
            :stock="form.stock"
            :recordId="recordId"
            :lots="form.lots"
            @addRowLot="addRowLot">
        </lots-form>

    </el-dialog>
</template>

<script>
    import LotsForm from './partials/lots.vue'

    export default {
        props: ['showDialog', 'recordId', 'external', 'type'],
        components: {LotsForm},

        data() {
            return {
                showDialogLots:false,
                form_category:{ add: false, name: null, id: null },
                form_brand:{ add: false, name: null, id: null },
                warehouses: [],
                loading_submit: false,
                showPercentagePerception: false,
                has_percentage_perception: false,
                percentage_perception:null,
                enabled_percentage_of_profit:false,
                titleDialog: null,
                resource: 'items',
                errors: {},
                headers: headers_token,
                form: {},
                configuration: {},
                unit_types: [],
                currency_types: [],
                system_isc_types: [],
                affectation_igv_types: [],
                categories: [],
                brands: [],
                accounts: [],
                show_has_igv:true,
                purchase_show_has_igv:true,
                have_account:false,
                item_unit_type:{
                        id:null,
                        unit_type_id:null,
                        quantity_unit:0,
                        price1:0,
                        price2:0,
                        price3:0,
                        price_default:2,

                },
                attribute_types:  []
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.unit_types = response.data.unit_types
                    this.accounts = response.data.accounts
                    this.currency_types = response.data.currency_types
                    this.system_isc_types = response.data.system_isc_types
                    this.affectation_igv_types = response.data.affectation_igv_types
                    this.warehouses = response.data.warehouses
                    this.categories = response.data.categories
                    this.brands = response.data.brands
                    this.attribute_types = response.data.attribute_types
                    this.configuration = response.data.configuration

                    this.form.sale_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                    this.form.purchase_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                })

            this.$eventHub.$on('submitPercentagePerception', (data)=>{
                this.form.percentage_perception = data
                if(!this.form.percentage_perception) this.has_percentage_perception = false
            })

            this.$eventHub.$on('reloadTables', ()=>{
                this.reloadTables()
            })

            await this.setDefaultConfiguration()

        },

        methods: {
            setDefaultConfiguration(){
                this.form.sale_affectation_igv_type_id = (this.configuration) ? this.configuration.affectation_igv_type_id : '10'

                this.$http.get(`/configurations/record`) .then(response => {
                    this.form.has_igv = response.data.data.include_igv
                    this.form.purchase_has_igv = response.data.data.include_igv
                })
            },
            clickAddAttribute() {
                this.form.attributes.push({
                    attribute_type_id: null,
                    description: null,
                    value: null,
                    start_date: null,
                    end_date: null,
                    duration: null,
                })
            },
            async reloadTables(){

                await this.$http.get(`/${this.resource}/tables`)
                    .then(response => {
                        this.unit_types = response.data.unit_types
                        this.accounts = response.data.accounts
                        this.currency_types = response.data.currency_types
                        this.system_isc_types = response.data.system_isc_types
                        this.affectation_igv_types = response.data.affectation_igv_types
                        this.warehouses = response.data.warehouses
                        this.categories = response.data.categories
                        this.brands = response.data.brands

                        this.form.sale_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                        this.form.purchase_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                    })
            },
            changeLotsEnabled(){

                // if(!this.form.lots_enabled){
                //     this.form.lot_code = null
                //     this.form.lots = []
                // }

            },
            addRowLot(lots){
                this.form.lots = lots
            },
            clickLotcode(){
                this.showDialogLots = true
            },
            changeHaveAccount(){
                if(!this.have_account) this.form.account_id = null
            },
            changeEnabledPercentageOfProfit(){
                // if(!this.enabled_percentage_of_profit) this.form.percentage_of_profit = 0
            },
            clickDelete(id) {

                this.$http.delete(`/${this.resource}/item-unit-type/${id}`)
                        .then(res => {
                            if(res.data.success) {
                                this.loadRecord()
                                this.$message.success('Se eliminó correctamente el registro')
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar eliminar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })

            },
            changeHasPerception(){
                if(!this.form.has_perception){
                    this.form.percentage_perception = null
                }
            },
            clickAddRow() {
                this.form.item_unit_types.push({
                    id: null,
                    description: null,
                    unit_type_id: 'NIU',
                    quantity_unit: 0,
                    price1: 0,
                    price2: 0,
                    price3: 0,
                    price_default: 2
                })
            },
            clickCancel(index) {
                this.form.item_unit_types.splice(index, 1)
            },
            initForm() {
                this.loading_submit = false,
                this.errors = {}
                this.form = {
                    id: null,
                    item_type_id: '01',
                    internal_id: null,
                    item_code: null,
                    item_code_gs1: null,
                    description: null,
                    name: null,
                    second_name: null,
                    unit_type_id: 'NIU',
                    currency_type_id: 'PEN',
                    sale_unit_price: 0,
                    purchase_unit_price: 0,
                    has_isc: false,
                    system_isc_type_id: null,
                    percentage_isc: 0,
                    suggested_price: 0,
                    sale_affectation_igv_type_id: null,
                    purchase_affectation_igv_type_id: null,
                    calculate_quantity: false,
                    stock: 0,
                    stock_min: 1,
                    has_igv: true,
                    has_perception: false,
                    item_unit_types:[],
                    percentage_of_profit: 0,
                    percentage_perception: 0,
                    image: null,
                    image_url: null,
                    temp_path: null,
                    is_set: false,
                    account_id: null,
                    category_id: null,
                    brand_id: null,
                    date_of_due:null,
                    lot_code:null,
                    line:null,
                    lots_enabled:false,
                    lots:[],
                    attributes: [],
                    series_enabled: false,
                    purchase_has_igv: true,
                    web_platform_id:null,
                    has_plastic_bag_taxes: false,
                }
                this.show_has_igv = true
                this.purchase_show_has_igv = true
                this.enabled_percentage_of_profit = false
            },
            onSuccess(response, file, fileList) {
                if (response.success) {
                    this.form.image = response.data.filename
                    this.form.image_url = response.data.temp_image
                    this.form.temp_path = response.data.temp_path
                } else {
                    this.$message.error(response.message)
                }
            },
            changeAffectationIgvType(){

                let affectation_igv_type_exonerated = [20,21,30,31,32,33,34,35,36,37]
                let is_exonerated = affectation_igv_type_exonerated.includes((parseInt(this.form.sale_affectation_igv_type_id)));

                if(is_exonerated){
                    this.show_has_igv = false
                    this.form.has_igv = true
                }else{
                    this.show_has_igv = true
                }

            },
            changePurchaseAffectationIgvType(){

                let affectation_igv_type_exonerated = [20,21,30,31,32,33,34,35,36,37]
                let is_exonerated = affectation_igv_type_exonerated.includes((parseInt(this.form.purchase_affectation_igv_type_id)));

                if(is_exonerated){
                    this.purchase_show_has_igv = false
                    this.form.purchase_has_igv = true
                }else{
                    this.purchase_show_has_igv = true
                }

            },
            resetForm() {
                this.initForm()
                this.form.sale_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                this.form.purchase_affectation_igv_type_id = (this.affectation_igv_types.length > 0)?this.affectation_igv_types[0].id:null
                this.setDefaultConfiguration()
            },
            create() {
                if (this.type) {
                    this.form.unit_type_id = 'ZZ';
                }
                this.titleDialog = (this.recordId)? 'Editar Producto':'Nuevo Producto'
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                            this.has_percentage_perception = (this.form.percentage_perception) ? true : false
                            this.changeAffectationIgvType()
                            this.changePurchaseAffectationIgvType()
                            const warehousePrices = response.data.data.warehouse_prices;
                            if (warehousePrices.length > 0) {
                                this.warehouses = this.warehouses.map(w => {
                                    const price = warehousePrices.find(wp => wp.warehouse_id === w.id);
                                    if (price) {
                                        var priceToJson = {...price};
                                        w.price = priceToJson.price;
                                    }
                                    return w;
                                });
                            } else {
                                this.warehouses = this.warehouses.map(w => {
                                    delete w.price;
                                    return w;
                                });
                            }
                        })
                }

            },
            loadRecord(){
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                            this.changeAffectationIgvType()
                            this.changePurchaseAffectationIgvType()
                        })
                }
            },
            calculatePercentageOfProfitBySale() {
                let difference = parseFloat(this.form.sale_unit_price) - parseFloat(this.form.purchase_unit_price);

                if(parseFloat(this.form.purchase_unit_price) === 0) {
                    this.form.percentage_of_profit = 0;
                } else {
                    if(this.enabled_percentage_of_profit) this.form.percentage_of_profit = difference / parseFloat(this.form.purchase_unit_price) * 100;
                }
            },
            calculatePercentageOfProfitByPurchase() {
                if(this.form.percentage_of_profit === '') {
                    this.form.percentage_of_profit = 0;
                }

                if(this.enabled_percentage_of_profit) this.form.sale_unit_price = (this.form.purchase_unit_price * (100 + parseFloat(this.form.percentage_of_profit))) / 100
            },
            calculatePercentageOfProfitByPercentage() {
                if(this.form.percentage_of_profit === '') {
                    this.form.percentage_of_profit = 0;
                }

                if(this.enabled_percentage_of_profit) this.form.sale_unit_price = (this.form.purchase_unit_price * (100 + parseFloat(this.form.percentage_of_profit))) / 100
            },
            validateItemUnitTypes(){

                let error_by_item = 0

                if(this.form.item_unit_types.length > 0){

                    this.form.item_unit_types.forEach(item => {

                        if(parseFloat(item.quantity_unit) < 0.0001){
                            error_by_item++
                        }

                    })

                }

                return error_by_item

            },
            async submit() {

                if(this.validateItemUnitTypes() > 0) return this.$message.error('El campo factor no puede ser menor a 0.0001');

                if(this.form.has_perception && !this.form.percentage_perception) return this.$message.error('Ingrese un porcentaje');

                if(this.form.lots_enabled){

                    if(!this.form.lot_code)
                        return this.$message.error('Código de lote es requerido');

                    if(!this.form.date_of_due)
                        return this.$message.error('Fecha de vencimiento es requerido si lotes esta habilitado.');
                }

                if (!this.recordId && this.form.series_enabled) {

                    if(this.form.lots.length > this.form.stock)
                        return this.$message.error('La cantidad de series registradas es superior al stock');

                    if(this.form.lots.length != this.form.stock)
                        return this.$message.error('La cantidad de series registradas son diferentes al stock');
                }

                this.loading_submit = true
                this.form.warehouses = this.warehouses.filter(w => w.price);
                await this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            if (this.external) {
                                this.$eventHub.$emit('reloadDataItems', response.data.id)
                            } else {
                                this.$eventHub.$emit('reloadData')
                            }
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.resetForm()
            },
            changeHasIsc() {
                this.form.system_isc_type_id = null
                this.form.percentage_isc = 0
                this.form.suggested_price = 0
            },
            changeSystemIscType() {
                if (this.form.system_isc_type_id !== '03') {
                    this.form.suggested_price = 0
                }
            },
            saveCategory()
            {
                this.form_category.add = false

                this.$http.post(`/categories`,  this.form_category)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.categories.push(response.data.data)
                        this.form_category.name = null
                    } else {
                        this.$message.error('No se guardaron los cambios')
                    }
                })
                .catch(error => {

                })
            },
            saveBrand()
            {
                this.form_brand.add = false

                this.$http.post(`/brands`,  this.form_brand)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.brands.push(response.data.data)
                        this.form_brand.name = null

                    } else {
                        this.$message.error('No se guardaron los cambios')
                    }
                })
                .catch(error => {

                })



            },
            changeAttributeType(index) {
                let attribute_type_id = this.form.attributes[index].attribute_type_id
                let attribute_type = _.find(this.attribute_types, {id: attribute_type_id})
                this.form.attributes[index].description = attribute_type.description
            },
            clickRemoveAttribute(index) {
                this.form.attributes.splice(index, 1)
            },
        }
    }
</script>
