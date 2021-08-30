<template>
    <div>
        <div v-if="loading_form">
            <form autocomplete="off" @submit.prevent="submit" class="row no-gutters">
                <div class="col-xl-9 col-md-9 col-12">
                    <div class="row card-header no-gutters align-items-start" style="background-color: #fff;">
                        <div class="col-xl-2 col-md-2 col-12">
                            <logo url="/" :path_logo="(company.logo != null) ? `/storage/uploads/logos/${company.logo}` : ''" :position_class="'text-left'"></logo>
                        </div>
                        <div class="col-xl-6 col-md-6 col-12 pl-2">
                            <address class="mb-0 mt-2" >
                                <span class="font-weight-bold">{{company.name}}</span>
                                <br>
                                <div v-if="establishment.address != '-'">{{ establishment.address }} </div>
                                <br>
                                {{establishment.email}} - <span v-if="establishment.telephone != '-'">{{establishment.telephone}}</span>
                            </address>
                        </div>
                        <div class="col-xl-4 col-md-4 col-12 align-self-end">
                            <div class="">
                                <div class="row">
                                    <div class="col-lg-6 align-self-end">
                                        <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                            <label class="control-label">Fec. Emisión</label>
                                            <el-date-picker v-model="form.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue" :picker-options="datEmision" :readonly="readonly_date_of_due"></el-date-picker>
                                            <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 align-self-end">
                                        <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                                            <label class="control-label">Fec. Vencimiento</label>
                                            <el-date-picker v-model="form.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="false" :readonly="readonly_date_of_due"></el-date-picker>
                                            <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body no-gutters">
                        <div class="row">
                            <div class="col-lg-4 align-self-end">
                                <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                                    <label class="control-label font-weight-bold text-info">Tipo comprobante</label>
                                    <el-select v-model="form.document_type_id" @change="changeDocumentType" popper-class="el-select-document_type" dusk="document_type_id" class="border-left rounded-left border-info">
                                        <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 d-none">
                                <div class="form-group" :class="{'has-danger': errors.establishment_id}">
                                    <label class="control-label">Establecimiento</label>
                                    <el-select v-model="form.establishment_id" @change="changeEstablishment">
                                        <el-option v-for="option in establishments" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.establishment_id" v-text="errors.establishment_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 align-self-end">
                                <div class="form-group" :class="{'has-danger': errors.series_id}">
                                    <label class="control-label">Serie</label>
                                    <el-select v-model="form.series_id">
                                        <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.series_id" v-text="errors.series_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 align-self-end">
                                <div class="form-group" :class="{'has-danger': errors.operation_type_id}">
                                    <label class="control-label">Tipo Operación
                                    <template v-if="(form.operation_type_id == '1001' || form.operation_type_id == '1004') && has_data_detraction" >
                                        <a href="#" @click.prevent="showDialogDocumentDetraction = true" class="text-center font-weight-bold text-info"> [+ Ver datos]</a>
                                    </template>

                                    </label>
                                    <el-select v-model="form.operation_type_id" @change="changeOperationType">
                                        <el-option v-for="option in operation_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.operation_type_id" v-text="errors.operation_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 align-self-end">
                                <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                    <label class="control-label">Moneda</label>
                                    <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                        <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 align-self-end">
                                <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                    <label class="control-label">Tipo de cambio
                                        <el-tooltip class="item" effect="dark" content="Tipo de cambio del día, extraído de SUNAT" placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-input :disabled="isUpdate" v-model="form.exchange_rate_sale"></el-input>
                                    <small class="form-control-feedback" v-if="errors.exchange_rate_sale" v-text="errors.exchange_rate_sale[0]"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-top no-gutters">
                        <div class="row">
                            <div class="form-group col-sm-6 mb-0" :class="{'has-danger': errors.customer_id}">
                                <label class="control-label font-weight-bold text-info">
                                    Cliente
                                    <a href="#" @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.customer_id" filterable remote class="border-left rounded-left border-info" popper-class="el-select-customers"
                                    dusk="customer_id"
                                    placeholder="Escriba el nombre o número de documento del cliente"
                                    :remote-method="searchRemoteCustomers"
                                    @keyup.enter.native="keyupCustomer"
                                    :loading="loading_search"
                                    @change="changeCustomer">

                                    <el-option v-for="option in customers" :key="option.id" :value="option.id" :label="option.description"></el-option>

                                </el-select>
                                <small class="form-control-feedback" v-if="errors.customer_id" v-text="errors.customer_id[0]"></small>
                            </div>
                            <div v-if="customer_addresses.length > 0" class="form-group col-sm-6 mb-0">
                                <label class="control-label font-weight-bold text-info">Dirección</label>
                                <el-select v-model="form.customer_address_id">
                                    <el-option v-for="option in customer_addresses" :key="option.id" :value="option.id" :label="option.address"></el-option>
                                </el-select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-top no-gutters p-0">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%" class="font-weight-bold">Descripción</th>
                                        <th class="text-center font-weight-bold">Unidad</th>
                                        <th class="text-right font-weight-bold">Cantidad</th>
                                        <th class="text-right font-weight-bold">Valor Unitario</th>
                                        <th class="text-right font-weight-bold">Precio Unitario</th>
                                        <th class="text-right font-weight-bold">Subtotal</th>
                                        <!--<th class="text-right font-weight-bold">Cargo</th>-->
                                        <th class="text-right font-weight-bold">Total</th>
                                        <th width="8%"></th>
                                    </tr>
                                </thead>
                                <tbody >
                                    <tr v-for="(row, index) in form.items" :key="index">
                                        <td>{{index + 1}}</td>
                                        <td>{{row.item.description}} {{row.item.presentation.hasOwnProperty('description') ? row.item.presentation.description : ''}}<br/><small>{{row.affectation_igv_type.description}}</small></td>
                                        <td class="text-center">{{row.item.unit_type_id}}</td>

                                        <td class="text-right">{{row.quantity}}</td>

                                        <td class="text-right">{{currency_type.symbol}} {{getFormatUnitPriceRow(row.unit_value)}}</td>
                                        <td class="text-right">{{currency_type.symbol}} {{getFormatUnitPriceRow(row.unit_price)}}</td>


                                        <td class="text-right">{{currency_type.symbol}} {{row.total_value}}</td>
                                        <td class="text-right">{{currency_type.symbol}} {{row.total}}</td>
                                        <td class="text-right">
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveItem(index)"><i class="fas fa-trash"></i></button>
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click="ediItem(row, index)" ><span style='font-size:10px;'>&#9998;</span> </button>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-center pt-3">
                                            <button type="button"
                                                class="btn waves-effect waves-light btn-primary btn-sm"
                                                @click.prevent="clickAddItemInvoice"
                                                style="width: 180px;">+ Agregar Producto</button>
                                        </td>
                                        <td colspan="2"></td>
                                        <td colspan="5" class="p-0">
                                            <table style="width: 100%;" class="table-sm text-right">
                                                <tr v-if="form.total_taxed > 0 && enabled_discount_global">
                                                    <td>
                                                        DESCUENTO
                                                        <template v-if="is_amount"> MONTO</template>
                                                        <template v-else> %</template>
                                                        <el-checkbox class="ml-1 mr-1" v-model="is_amount" @change="changeTypeDiscount"></el-checkbox>
                                                        :
                                                    </td>
                                                    <td>
                                                        <el-input class="input-custom" v-model="total_global_discount" @input="calculateTotal"></el-input>
                                                    </td>
                                                </tr>

                                                <template v-if="form.detraction">
                                                    <tr v-if="form.detraction.amount > 0">
                                                        <td width="60%">M. DETRACCIÓN:</td>
                                                        <td>{{ currency_type.symbol }} {{ form.detraction.amount }}</td>
                                                    </tr>
                                                </template>

                                                <tr v-if="form.total_exportation > 0">
                                                    <td>OP.EXPORTACIÓN:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_exportation }}</td>
                                                </tr>
                                                <tr v-if="form.total_free > 0">
                                                    <td>OP.GRATUITAS:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_free }}</td>
                                                </tr>
                                                <tr v-if="form.total_unaffected > 0">
                                                    <td>OP.INAFECTAS:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_unaffected }}</td>
                                                </tr>
                                                <tr v-if="form.total_exonerated > 0">
                                                    <td>OP.EXONERADAS:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_exonerated }}</td>
                                                </tr>
                                                <tr v-if="form.total_taxed > 0">
                                                    <td>OP.GRAVADA:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_taxed }}</td>
                                                </tr>
                                                <tr v-if="form.total_prepayment > 0">
                                                    <td>ANTICIPOS:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_prepayment }}</td>
                                                </tr>
                                                <tr v-if="form.total_igv > 0">
                                                    <td>IGV:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_igv }}</td>
                                                </tr>
                                                <tr v-if="form.total_plastic_bag_taxes > 0">
                                                    <td>ICBPER:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total_plastic_bag_taxes }}</td>
                                                </tr>

                                                <tr v-if="form.total > 0">
                                                    <td><strong>TOTAL A PAGAR</strong>:</td>
                                                    <td>{{ currency_type.symbol }} {{ form.total }}</td>
                                                </tr>

                                                <tr v-if="form.total > 0">
                                                    <td>CONDICIÓN DE PAGO:</td>
                                                    <td>
                                                        <el-select v-model="form.payment_condition_id" @change="changePaymentCondition" popper-class="el-select-document_type" dusk="document_type_id" style="max-width: 200px;">
                                                            <el-option value="02" label="Crédito"></el-option>
                                                            <el-option value="01" label="Contado"></el-option>
                                                        </el-select>
                                                    </td>
                                                </tr>

                                                <tr v-if="form.total > 0">
                                                    <td colspan="2" class="p-0">
                                                        <div v-if="form.payment_condition_id === '02'">
                                                            <table v-if="form.fee.length>0" class="text-left" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-left" style="width: 100px">Fecha</th>
                                                                    <th class="text-left" style="width: 100px">Monto</th>
                                                                    <th style="width: 30px"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="(row, index) in form.fee" :key="index">
                                                                    <td>
                                                                        <el-date-picker v-model="row.date" type="date"
                                                                                        value-format="yyyy-MM-dd"
                                                                                        format="dd/MM/yyyy"
                                                                                        :clearable="false"></el-date-picker>
                                                                    </td>
                                                                    <td>
                                                                        <el-input v-model="row.amount"></el-input>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button"
                                                                                class="btn waves-effect waves-light btn-xs btn-danger"
                                                                                v-if="index > 0"
                                                                                @click.prevent="clickRemoveFee(index)">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <label class="control-label">
                                                                            <a href="#" @click.prevent="clickAddFee" class=""><i class="fa fa-plus font-weight-bold text-info"></i> <span style="color: #777">Agregar cuota</span></a>

                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div v-if="!is_receivable && form.payment_condition_id === '01'">
                                                            <table class="text-left">
                                                                <thead>
                                                                <tr>
                                                                    <th v-if="form.payments.length>0" style="width: 120px">Método de pago</th>
                                                                    <template v-if="enabled_payments">
                                                                        <th v-if="form.payments.length>0" style="width: 120px">Destino
                                                                            <el-tooltip class="item" effect="dark" content="Aperture caja o cuentas bancarias" placement="top-start">
                                                                                <i class="fa fa-info-circle"></i>
                                                                            </el-tooltip>
                                                                        </th>
                                                                        <th v-if="form.payments.length>0" style="width: 100px">Referencia</th>
                                                                        <th v-if="form.payments.length>0" style="width: 100px">Monto</th>
                                                                        <th style="width: 30px"></th>
                                                                    </template>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="(row, index) in form.payments" :key="index">
                                                                    <td>
                                                                        <el-select v-model="row.payment_method_type_id" @change="changePaymentMethodType(index)">
                                                                            <el-option v-for="option in payment_method_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                                        </el-select>
                                                                    </td>
                                                                    <template v-if="enabled_payments">
                                                                        <td>
                                                                            <el-select v-model="row.payment_destination_id" filterable >
                                                                                <el-option v-for="option in payment_destinations" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                                            </el-select>
                                                                        </td>
                                                                        <td>
                                                                            <el-input v-model="row.reference"></el-input>
                                                                        </td>
                                                                        <td>
                                                                            <el-input v-model="row.payment"></el-input>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </template>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <label class="control-label">
                                                                            <a href="#" @click.prevent="clickAddPayment" class=""><i class="fa fa-plus font-weight-bold text-info"></i> <span style="color: #777">Agregar pago</span></a>

                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button style="min-width: 180px" class="btn btn-default" @click.prevent="close()">Cancelar</button>
                        <el-button style="min-width: 180px" class="submit btn btn-primary" native-type="submit" :loading="loading_submit" v-if="form.items.length > 0 && this.dateValid">{{ btnText }}</el-button>
                    </div>
                </div>
                <div class="card card-transparent col-xl-3 col-md-3 col-12 pl-md-2 mt-0">
                    <div class="card-body d-flex align-items-start no-gutters">
                        <div class="col-12">
                            <div class="card-body p-2">
                                <div class="col-12 py-2 px-0">
                                    <div class="form-group">
                                        <label class="control-label">Vendedor</label>
                                        <el-select v-model="form.seller_id" :disabled="typeUser == 'seller'">
                                            <el-option v-for="option in sellers" :key="option.id" :value="option.id" :label="option.name"></el-option>
                                        </el-select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2 border-top">
                                <div class="col-12 py-2 px-0">
                                        <div class="row no-gutters">
                                            <div class="col-10">¿Es comprobante de contingencia?</div>
                                            <div class="col-2">
                                                <el-switch v-model="is_contingency" @change="changeEstablishment"></el-switch>
                                            </div>
                                        </div>
                                    </div>
                                <template v-if="!is_client">
                                    <div class="col-12 py-2 px-0">
                                        <div class="row no-gutters">
                                            <div class="col-10">¿Es un pago anticipado?</div>
                                            <div class="col-2">
                                                <el-switch v-model="form.has_prepayment" v-if="!prepayment_deduction" @change="changeHasPrepayment"></el-switch>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 py-2 px-0">
                                        <div class="row no-gutters">
                                            <div class="col-10">Deducción de los pagos anticipados</div>
                                            <div class="col-2">
                                                <el-switch v-model="prepayment_deduction" v-if="!form.has_prepayment" @change="changePrepaymentDeduction"></el-switch>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <el-select v-if="form.has_prepayment || prepayment_deduction" v-model="form.affectation_type_prepayment" @change="changeAffectationTypePrepayment" class="mb-2">
                                                <el-option :key="10" :value="10" label="Gravado"></el-option>
                                                <el-option :key="20" :value="20" label="Exonerado"></el-option>
                                                <el-option :key="30" :value="30" label="Inafecto"></el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                    <template v-if="!is_client">
                                        <div class="" v-if="prepayment_deduction">
                                            <div class="form-group">
                                                <table style="width: 100%">
                                                    <tr v-for="(row,index) in form.prepayments" :key="index">
                                                        <td>
                                                            <el-select v-model="row.document_id" filterable @change="changeDocumentPrepayment(index)">
                                                                <el-option v-for="option in prepayment_documents" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                            </el-select>
                                                        </td>
                                                        <td>
                                                            <el-input v-model="row.amount" @input="inputAmountPrepayment(index)"></el-input>
                                                        </td>
                                                        <td align="right">

                                                            <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemovePrepayment(index)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <label class="control-label">
                                                    <a href="#" @click.prevent="clickAddPrepayment" class=""><i class="fa fa-plus font-weight-bold text-info"></i> <span style="color: #777">Agregar comprobante anticipado</span></a>
                                                </label>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                            <el-collapse v-model="activePanel" accordion>
                                <el-collapse-item name="1" >
                                    <template slot="title">
                                        <span class="ml-2">Información Adicional</span>
                                    </template>
                                    <div class="row p-2 border-top">
                                        <div class="col-12">
                                            <template v-if="!isActiveBussinessTurn('tap')">
                                                <template v-if="!is_client">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            Guías
                                                        </label>
                                                        <table style="width: 100%">
                                                            <tr v-for="(guide,index) in form.guides">
                                                                <td>
                                                                    <el-select v-model="guide.document_type_id">
                                                                        <el-option v-for="option in document_types_guide" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                                    </el-select>
                                                                </td>
                                                                <td>
                                                                    <el-input v-model="guide.number"></el-input>
                                                                </td>
                                                                <td align="right">
                                                                    <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveGuide(index)">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <label class="control-label">
                                                                        <a href="#" @click.prevent="clickAddGuide" class=""><i class="fa fa-plus font-weight-bold text-info"></i> <span style="color: #777">Agregar guía</span></a>

                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <template v-if="!is_client">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            Guías
                                                        </label>
                                                        <table style="width: 100%">
                                                            <tr v-for="(guide,index) in form.guides">
                                                                <td>
                                                                    <el-select v-model="guide.document_type_id">
                                                                        <el-option v-for="option in document_types_guide" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                                    </el-select>
                                                                </td>
                                                                <td>
                                                                    <el-input v-model="guide.number"></el-input>
                                                                </td>
                                                                <td align="right">
                                                                    <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickRemoveGuide(index)">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <label class="control-label">
                                                                        <a href="#" @click.prevent="clickAddGuide" class=""><i class="fa fa-plus font-weight-bold text-info"></i> <span style="color: #777">Agregar guía</span></a>

                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                        <div class="col-12 py-2 border-top">
                                            <div class="form-group" :class="{'has-danger': errors.purchase_order}">
                                                <label class="control-label">Orden de Compra</label>
                                                <el-input
                                                    type="textarea"
                                                    v-model="form.purchase_order">
                                                </el-input>
                                                <small class="form-control-feedback" v-if="errors.purchase_order" v-text="errors.purchase_order[0]"></small>
                                            </div>
                                        </div>
                                        <div class="col-12 py-2 border-top">
                                            <div class="form-group">
                                                <label class="control-label">Observaciones</label>
                                                <el-input
                                                        type="textarea"
                                                        autosize
                                                        v-model="form.additional_information">
                                                </el-input>
                                            </div>
                                        </div>
                                        <div class="col-md-12 py-2 border-top">
                                            <div class="form-group" :class="{'has-danger': errors.plate_number}">
                                                <label class="control-label">N° Placa</label>
                                                <el-input type="textarea" v-model="form.plate_number">
                                                </el-input>
                                                <small class="form-control-feedback" v-if="errors.plate_number" v-text="errors.plate_number[0]"></small>
                                            </div>
                                        </div>
                                        <div class="col-12 py-2 border-top">
                                            <span class="mr-3">Mostrar términos y condiciones.</span>
                                            <el-switch v-model="form.show_terms_condition"></el-switch>
                                        </div>
                                    </div>
                                </el-collapse-item>
                            </el-collapse>
                            <div class="" v-if="isActiveBussinessTurn('hotel')">
                                <el-tooltip class="item my-2" effect="dark" content="Datos personales para reserva de hospedaje" placement="bottom-end">
                                    <button class="btn btn-primary btn-block" @click.prevent="clickAddDocumentHotel">Datos de reserva</button>
                                </el-tooltip>
                            </div>
                            <div class="" v-if="isActiveBussinessTurn('transport')">
                                <el-tooltip class="item my-2" effect="dark" content="Datos para transporte de pasajeros" placement="bottom-end">
                                    <button class="btn btn-primary btn-block" @click.prevent="clickAddDocumentTransport">Datos de transporte</button>
                                </el-tooltip>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <document-form-item :showDialog.sync="showDialogAddItem"
            :recordItem="recordItem"
            :isEditItemNote="false"
            :operation-type-id="form.operation_type_id"
            :currency-type-id-active="form.currency_type_id"
            :exchange-rate-sale="form.exchange_rate_sale"
            :typeUser="typeUser"
            :configuration="configuration"
            :editNameProduct="configuration.edit_name_product"
            @add="addRow"></document-form-item>

        <person-form :showDialog.sync="showDialogNewPerson"
            type="customers"
            :external="true"
            :input_person="input_person"
            :document_type_id = form.document_type_id></person-form>

        <document-options :showDialog.sync="showDialogOptions"
            :recordId="documentNewId"
            :isContingency="is_contingency"
            :isUpdate="isUpdate"
            :showClose="false"
            :configuration="configuration"></document-options>


        <document-hotel-form
            :showDialog.sync="showDialogFormHotel"
            :hotel="form.hotel"
            @addDocumentHotel="addDocumentHotel"
            ></document-hotel-form>

        <document-transport-form
            :showDialog.sync="showDialogFormTransport"
            :transport="form.transport"
            @addDocumentTransport="addDocumentTransport"
            ></document-transport-form>

        <document-detraction
            :detraction="form.detraction"
            :total="form.total"
            :operation-type-id="form.operation_type_id"
            :currency-type-id-active="form.currency_type_id"
            :exchange-rate-sale="form.exchange_rate_sale"
            :showDialog.sync="showDialogDocumentDetraction"
            @addDocumentDetraction="addDocumentDetraction" ></document-detraction>
    </div>
</template>

<style>
.input-custom{
    width: 50% !important;
}

.el-textarea__inner {
    height: 65px !important;
    min-height: 65px !important;
}
.card-header + .card-body {
    border-radius: 0px;
}
.card-body {
    border-radius: 0px;
}
.el-collapse-item__content {
    padding-bottom: 0px;
}
.content-body {
    padding: 20px;
}
</style>
<script>
    import DocumentFormItem from './partials/item.vue'
    import PersonForm from '../persons/form.vue'
    import DocumentOptions from '../documents/partials/options.vue'
    import {functions, exchangeRate} from '../../../mixins/functions'
    import {calculateRowItem} from '../../../helpers/functions'
    import Logo from '../companies/logo.vue'
    import DocumentHotelForm from '../../../../../modules/BusinessTurn/Resources/assets/js/views/hotels/form.vue'
    import DocumentTransportForm from '../../../../../modules/BusinessTurn/Resources/assets/js/views/transports/form.vue'
    import DocumentDetraction from './partials/detraction.vue'
    import moment from 'moment'

    export default {
        props: ['idUser', 'typeUser', 'configuration', 'documentId', 'isUpdate'],
        components: {DocumentFormItem, PersonForm, DocumentOptions, Logo, DocumentHotelForm, DocumentDetraction, DocumentTransportForm},
        mixins: [functions, exchangeRate],
        data() {
            return {
                datEmision: {
                  disabledDate(time) {
                    return time.getTime() > moment();
                  }
                },
                dateValid:false,
                input_person:{},
                showDialogDocumentDetraction:false,
                has_data_detraction:false,
                showDialogFormHotel:false,
                showDialogFormTransport:false,
                is_client:false,
                recordItem: null,
                resource: 'documents',
                showDialogAddItem: false,
                showDialogNewPerson: false,
                showDialogOptions: false,
                loading_submit: false,
                loading_form: false,
                errors: {},
                form: {},
                document_types: [],
                currency_types: [],
                discount_types: [],
                charges_types: [],
                all_customers: [],
                business_turns: [],
                form_payment: {},
                document_types_guide: [],
                customers: [],
                sellers: [],
                company: null,
                document_type_03_filter: null,
                operation_types: [],
                establishments: [],
                payment_method_types: [],
                establishment: null,
                all_series: [],
                series: [],
                prepayment_documents: [],
                currency_type: {},
                documentNewId: null,
                prepayment_deduction:false,
                activePanel: 0,
                total_global_discount:0,
                loading_search:false,
                is_amount:true,
                enabled_discount_global:false,
                user: null,
                is_receivable:false,
                is_contingency: false,
                cat_payment_method_types: [],
                select_first_document_type_03:false,
                detraction_types: [],
                all_detraction_types: [],
                customer_addresses:  [],
                payment_destinations:  [],
                form_cash_document: {},
                enabled_payments: true,
                readonly_date_of_due: false,
                seller_class: 'col-lg-6 pb-2',
                btnText: 'Generar',
                payment_conditions: []
            }
        },
        async created() {
            await this.initForm()
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.document_types = response.data.document_types_invoice;
                    this.document_types_guide = response.data.document_types_guide;
                    this.currency_types = response.data.currency_types
                    this.business_turns = response.data.business_turns
                    this.establishments = response.data.establishments
                    this.operation_types = response.data.operation_types
                    this.all_series = response.data.series
                    this.all_customers = response.data.customers
                    this.sellers = response.data.sellers
                    this.discount_types = response.data.discount_types
                    this.charges_types = response.data.charges_types
                    this.payment_method_types = response.data.payment_method_types
                    this.enabled_discount_global = response.data.enabled_discount_global
                    this.company = response.data.company;
                    this.user = response.data.user;
                    this.document_type_03_filter = response.data.document_type_03_filter;
                    this.select_first_document_type_03 = response.data.select_first_document_type_03
                    this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null;
                    this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null;
                    this.form.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null;
                    this.form.operation_type_id = (this.operation_types.length > 0)?this.operation_types[0].id:null;
                    this.form.seller_id = (this.sellers.length > 0)?this.idUser:null;
                    // this.prepayment_documents = response.data.prepayment_documents;
                    this.is_client = response.data.is_client;
                    // this.cat_payment_method_types = response.data.cat_payment_method_types;
                    // this.all_detraction_types = response.data.detraction_types;
                    this.payment_destinations = response.data.payment_destinations
                    this.payment_conditions = response.data.payment_conditions;

                    this.seller_class = (this.user == 'admin')?'col-lg-4 pb-2':'col-lg-6 pb-2';

                    this.selectDocumentType()

                    this.changeEstablishment()
                    this.changeDateOfIssue()
                    this.changeDocumentType()
                    this.changeDestinationSale()
                    this.changeCurrencyType()
                })
            this.loading_form = true
            this.$eventHub.$on('reloadDataPersons', (customer_id) => {
                this.reloadDataCustomers(customer_id)
            })
            this.$eventHub.$on('initInputPerson', () => {
                this.initInputPerson()
            });
            if (this.documentId) {
                this.btnText = 'Actualizar';
                this.loading_submit = true;
                await this.$http.get(`/documents/${this.documentId}/show`).then(response => {
                    this.onSetFormData(response.data.data);
                }).finally(() => this.loading_submit = false);
            }

            const itemsFromDispatches = localStorage.getItem('items');
            if (itemsFromDispatches) {
                const itemsParsed = JSON.parse(itemsFromDispatches);
                const items = itemsParsed.map(i => i.item_id);
                const params = {
                    items_id: items
                }
                localStorage.removeItem('items');
                await this.$http.get('/documents/search-items', { params }).then(response => {
                    const itemsResponse = response.data.items.map(i => {
                        i.affectation_igv_type = {
                            active: 1,
                            description: "Gravado - Operación Onerosa",
                            exportation: 0,
                            free: 0,
                            id: "10",
                        }
                        i.presentation = {};
                        i.unit_price = i.sale_unit_price;
                        i.item = {
                            amount_plastic_bag_taxes: i.amount_plastic_bag_taxes,
                            attributes: i.attributes,
                            brand: i.brand,
                            calculate_quantity: i.calculate_quantity,
                            category: i.category,
                            currency_type_id: i.currency_type_id,
                            currency_type_symbol: i.currency_type_symbol,
                            description: i.description,
                            full_description: i.full_description,
                            has_igv: i.has_igv,
                            has_plastic_bag_taxes: i.has_plastic_bag_taxes,
                            id: i.id,
                            internal_id: i.internal_id,
                            item_unit_types: i.item_unit_types,
                            lots: i.lots,
                            lots_enabled: i.lots_enabled,
                            lots_group: i.lots_group,
                            model: i.model,
                            presentation: {},
                            purchase_affectation_igv_type_id: i.purchase_affectation_igv_type_id,
                            purchase_unit_price: i.purchase_unit_price,
                            sale_affectation_igv_type_id: i.sale_affectation_igv_type_id,
                            sale_unit_price: i.sale_unit_price,
                            series_enabled: i.series_enabled,
                            stock: i.stock,
                            unit_price: i.sale_unit_price,
                            unit_type_id: i.unit_type_id,
                            warehouses: i.warehouses,
                        };
                        i.IdLoteSelected = null;
                        i.affectation_igv_type_id = "10";
                        i.discounts = [];
                        i.charges = [];
                        i.item_id = i.id;
                        i.unit_price_value = i.sale_unit_price;
                        i.input_unit_price_value = i.sale_unit_price;
                        i.quantity = itemsParsed.find(ip => ip.item_id == i.id).quantity;
                        i.warehouse_id = null;
                        return i;
                    });
                    this.form.items = itemsResponse.map(i => {
                        return calculateRowItem(i, this.form.currency_type_id, this.form.exchange_rate_sale)
                    });
                });
            }

            const itemsFromNotes = localStorage.getItem('itemsForNotes');
            if (itemsFromNotes) {
                const itemsParsed = JSON.parse(itemsFromNotes);
                const items = itemsParsed.map(i => i.id);
                const params = {
                    items_id: items
                }
                localStorage.removeItem('itemsForNotes');
                await this.$http.get('/documents/search-items', { params }).then(response => {
                    const itemsResponse = response.data.items.map(i => {
                        i.affectation_igv_type = {
                            active: 1,
                            description: "Gravado - Operación Onerosa",
                            exportation: 0,
                            free: 0,
                            id: "10",
                        }
                        i.presentation = {};
                        i.unit_price = i.sale_unit_price;
                        i.item = {
                            amount_plastic_bag_taxes: i.amount_plastic_bag_taxes,
                            attributes: i.attributes,
                            brand: i.brand,
                            calculate_quantity: i.calculate_quantity,
                            category: i.category,
                            currency_type_id: i.currency_type_id,
                            currency_type_symbol: i.currency_type_symbol,
                            description: i.description,
                            full_description: i.full_description,
                            has_igv: i.has_igv,
                            has_plastic_bag_taxes: i.has_plastic_bag_taxes,
                            id: i.id,
                            internal_id: i.internal_id,
                            item_unit_types: i.item_unit_types,
                            lots: i.lots,
                            lots_enabled: i.lots_enabled,
                            lots_group: i.lots_group,
                            model: i.model,
                            presentation: {},
                            purchase_affectation_igv_type_id: i.purchase_affectation_igv_type_id,
                            purchase_unit_price: i.purchase_unit_price,
                            sale_affectation_igv_type_id: i.sale_affectation_igv_type_id,
                            sale_unit_price: i.sale_unit_price,
                            series_enabled: i.series_enabled,
                            stock: i.stock,
                            unit_price: i.sale_unit_price,
                            unit_type_id: i.unit_type_id,
                            warehouses: i.warehouses,
                        };
                        i.IdLoteSelected = null;
                        i.affectation_igv_type_id = "10";
                        i.discounts = [];
                        i.charges = [];
                        i.item_id = i.id;
                        i.unit_price_value = i.sale_unit_price;
                        i.input_unit_price_value = i.sale_unit_price;
                        i.quantity = itemsParsed.find(ip => ip.id == i.id).quantity;
                        i.warehouse_id = null;
                        return i;
                    });
                    this.form.items = itemsResponse.map(i => {
                        return calculateRowItem(i, this.form.currency_type_id, this.form.exchange_rate_sale)
                    });
                });
            }
            const clientfromDispatchesOrNotes = localStorage.getItem('client');
            if (clientfromDispatchesOrNotes) {
                const client = JSON.parse(clientfromDispatchesOrNotes);
                if (client.identity_document_type_id == 1) {
                    this.form.document_type_id = '03'
                } else if (client.identity_document_type_id == 6) {
                    this.form.document_type_id = '01'
                }
                this.searchRemoteCustomers(client.number);
                this.form.customer_id = client.id;
                this.changeEstablishment();
                this.filterSeries();
                this.filterCustomers();
                this.changeCurrencyType()
                localStorage.removeItem('client');
            }
            const dispatchesNumbersFromDispatches = localStorage.getItem('dispatches');
            if (dispatchesNumbersFromDispatches) {
                this.form.dispatches_relateds = JSON.parse(dispatchesNumbersFromDispatches);
                localStorage.removeItem('dispatches')
            }
            const notesNumbersFromNotes = localStorage.getItem('notes');
            if (notesNumbersFromNotes) {
                this.form.sale_notes_relateds = JSON.parse(notesNumbersFromNotes);
                localStorage.removeItem('notes')
            }
        },
        methods: {
            async onSetFormData(data) {
                this.form.establishment_id = data.establishment_id;
                this.form.document_type_id = data.document_type_id;
                this.form.id = data.id;
                this.form.hash = data.hash;
                this.form.number = data.number;
                this.form.date_of_issue = moment(data.date_of_issue).format('YYYY-MM-DD');
                this.form.time_of_issue = data.time_of_issue;
                this.form.customer_id = data.customer_id;
                this.form.currency_type_id = data.currency_type_id;
                this.form.exchange_rate_sale = data.exchange_rate_sale;
                this.form.additional_information = this.onPrepareAdditionalInformation(data.additional_information);
                this.form.external_id = data.external_id;
                this.form.filename = data.filename;
                this.form.group_id = data.group_id;
                this.form.perception = data.perception;
                this.form.note = data.note;
                this.form.plate_number = data.plate_number;
                this.form.payments = data.payments;
                this.form.prepayments = data.prepayments || [];
                this.form.legends = [];
                this.form.detraction = data.detraction;
                this.form.affectation_type_prepayment = data.affectation_type_prepayment;
                this.form.purchase_order =  data.purchase_order;
                this.form.pending_amount_prepayment = data.pending_amount_prepayment || 0;
                this.form.payment_method_type_id = data.payment_method_type_id;
                this.form.charges = data.charges || [];
                this.form.discounts = data.discounts || [];
                this.form.seller_id = data.seller_id;
                this.form.items = this.onPrepareItems(data.items);
                // this.form.series = data.series; //form.series no llena el selector
                this.series = this.onSetSeries(data.document_type_id, data.series);
                this.form.state_type_id = data.state_type_id;
                this.form.total_discount = parseFloat(data.total_discount);
                this.form.total_exonerated = parseFloat(data.total_exonerated);
                this.form.total_exportation = parseFloat(data.total_exportation);
                this.form.total_free = parseFloat(data.total_free);
                this.form.total_igv = parseFloat(data.total_igv);
                this.form.total_isc = parseFloat(data.total_isc);
                this.form.total_base_isc = parseFloat(data.total_base_isc);
                this.form.total_base_other_taxes = parseFloat(data.total_base_other_taxes);
                this.form.total_other_taxes = parseFloat(data.total_other_taxes);
                this.form.total_plastic_bag_taxes = parseFloat(data.total_plastic_bag_taxes);
                this.form.total_prepayment = parseFloat(data.total_prepayment);
                this.form.total_taxed = parseFloat(data.total_taxed);
                this.form.total_taxes = parseFloat(data.total_taxes);
                this.form.total_unaffected = parseFloat(data.total_unaffected);
                this.form.total_value = parseFloat(data.total_value);
                this.form.total_charge = parseFloat(data.total_charge);
                this.form.total = parseFloat(data.total);
                this.form.series_id = this.onSetSeriesId(data.document_type_id, data.series);
                this.form.operation_type_id = data.invoice.operation_type_id;
                this.form.terms_condition = data.terms_condition || '';
                this.form.guides = data.guides || [];
                this.form.show_terms_condition = data.terms_condition ? true : false;
                this.form.attributes = [];
                this.form.customer = data.customer;
                this.form.has_prepayment = false;
                this.form.actions = {
                    format_pdf:'a4',
                };
                this.form.hotel = {};
                this.form.transport = {};
                this.form.customer_address_id = null;
                this.form.type = 'invoice';
                this.form.invoice = {
                    operation_type_id: data.invoice.operation_type_id,
                    date_of_due: data.invoice.date_of_due,
                };
                this.form.payment_condition_id = '01';
                this.form.fee = [];

                if (! data.guides) {
                    this.clickAddInitGuides();
                }

                this.establishment = data.establishment;

                this.changeDateOfIssue();
                this.filterCustomers();
                this.changeDestinationSale();
                this.calculateTotal();
                this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
            },
            onPrepareAdditionalInformation(data) {
                if (typeof data === 'object') {
                    if (data[0]) {
                        return data;
                    }
                    return null;
                }
                return null;
            },
            onPrepareItems(items) {
                return items.map(i => {
                    i.unit_price_value = i.unit_value;
                    i.input_unit_price_value = i.unit_value;
                    i.discounts = i.discounts || [];
                    i.charges = i.charges || [];
                    i.item.id = i.item_id;
                    i.additional_information = this.onPrepareAdditionalInformation(i.additional_information);
                    return i;
                });
            },
            onSetSeriesId(documentType, serie) {
                const find = this.all_series.find(s => s.document_type_id == documentType && s.number == serie);
                if (find) {
                    return find.id;
                }
                return null;
            },
            onSetSeries(documentType, serie) {
                const find = this.all_series.find(s => s.document_type_id == documentType && s.number == serie);
                if (find) {
                    return [find];
                }
                return [];
            },
            getPrepayment(index){
                return _.find(this.prepayment_documents, {id: this.form.prepayments[index].document_id})
            },
            inputAmountPrepayment(index){

                let prepayment = this.getPrepayment(index)

                if(parseFloat(this.form.prepayments[index].amount) > parseFloat(prepayment.amount)){

                    this.form.prepayments[index].amount = prepayment.amount
                    this.$message.error('El monto debe ser menor o igual al del anticipo');

                }

                this.form.prepayments[index].total = (this.form.affectation_type_prepayment == 10) ? _.round(this.form.prepayments[index].amount * 1.18, 2) : this.form.prepayments[index].amount

                this.changeTotalPrepayment()

            },
            changeDestinationSale() {
                if(this.configuration.destination_sale && this.payment_destinations.length > 0) {
                    let cash = _.find(this.payment_destinations, {id : 'cash'})
                    if (cash) {
                        this.form.payments[0].payment_destination_id = cash.id
                    } else {
                        this.form.payment_destination_id = this.payment_destinations[0].id
                        this.form.payments[0].payment_destination_id = this.payment_destinations[0].id
                    }
                }
            },
            changePaymentDestination(index){
                // if(this.form.payments[index].payment_method_type_id=='01'){
                //     this.payment_destinations = this.cash
                // }else{
                //     this.payment_destinations = this.payment_destinations
                // }
            },
            changeEnabledPayments(){
                // this.clickAddPayment()
                // this.form.date_of_due = this.form.date_of_issue
                // this.readonly_date_of_due = false
                // this.form.payment_method_type_id = null
            },
            changePaymentMethodType(index){

                let payment_method_type = _.find(this.payment_method_types, {'id':this.form.payments[index].payment_method_type_id})

                if(payment_method_type.number_days){

                    this.form.date_of_due =  moment().add(payment_method_type.number_days,'days').format('YYYY-MM-DD')
                    // this.form.payments = []
                    this.enabled_payments = false
                    this.readonly_date_of_due = true
                    this.form.payment_method_type_id = payment_method_type.id

                }else if(payment_method_type.id == '09'){

                    this.form.payment_method_type_id = payment_method_type.id
                    this.form.date_of_due = this.form.date_of_issue
                    // this.form.payments = []
                    this.enabled_payments = false

                }else{

                    this.form.date_of_due = this.form.date_of_issue
                    this.readonly_date_of_due = false
                    this.form.payment_method_type_id = null
                    this.enabled_payments = true

                }

            },
            selectDocumentType(){
                this.form.document_type_id = (this.select_first_document_type_03) ? '03':'01'
            },
            keyupCustomer(){

                if(this.input_person.number){

                    if(!isNaN(parseInt(this.input_person.number))){

                        switch (this.input_person.number.length) {
                            case 8:
                                this.input_person.identity_document_type_id = '1'
                                this.showDialogNewPerson = true
                                break;

                            case 11:
                                this.input_person.identity_document_type_id = '6'
                                this.showDialogNewPerson = true
                                break;
                            default:
                                this.input_person.identity_document_type_id = '6'
                                this.showDialogNewPerson = true
                                break;
                        }
                    }
                }
            },
            addDocumentDetraction(detraction) {
                this.form.detraction = detraction
                // this.has_data_detraction = (detraction.pay_constancy || detraction.detraction_type_id || detraction.payment_method_id || (detraction.amount && detraction.amount >0)) ? true:false
                this.has_data_detraction = (detraction) ? detraction.has_data_detraction:false
            },
            clickAddItemInvoice(){
                this.recordItem = null
                this.showDialogAddItem = true
            },
            getFormatUnitPriceRow(unit_price){
                return _.round(unit_price, 6)
                // return unit_price.toFixed(6)
            },
            discountGlobalPrepayment(){

                let global_discount = 0
                this.form.prepayments.forEach((item)=>{
                    global_discount += parseFloat(item.amount)
                })

                // let base = (this.form.affectation_type_prepayment == 10) ? parseFloat(this.form.total_taxed):parseFloat(this.form.total_exonerated)
                let base = 0

                switch (this.form.affectation_type_prepayment) {
                    case 10:
                        base = parseFloat(this.form.total_taxed)
                        break;
                    case 20:
                        base = parseFloat(this.form.total_exonerated)
                        break;
                    case 30:
                        base = parseFloat(this.form.total_unaffected)
                        break;
                }

                let amount = _.round(parseFloat(global_discount), 2)
                let factor = _.round(amount/base, 4)

                this.form.total_prepayment = _.round(global_discount, 2)

                if(this.form.affectation_type_prepayment == 10){


                    let discount = _.find(this.form.discounts,{'discount_type_id':'04'})

                    if(global_discount>0 && !discount){
                        this.form.total_discount =  _.round(amount,2)
                        this.form.total_taxed =  _.round(base - amount,2)
                        this.form.total_value =  _.round(base - amount,2)
                        this.form.total_igv =  _.round(this.form.total_value * 0.18,2)
                        this.form.total_taxes =  _.round(this.form.total_igv,2)
                        this.form.total =  _.round(this.form.total_value + this.form.total_taxes,2)

                        this.form.discounts.push({
                                discount_type_id: "04",
                                description: "Descuentos globales por anticipos gravados que afectan la base imponible del IGV/IVAP",
                                factor: factor,
                                amount: amount,
                                base: base
                            })

                    }else{

                        let pos = this.form.discounts.indexOf(discount);

                        if(pos > -1){

                            this.form.total_discount =  _.round(amount,2)
                            this.form.total_taxed =  _.round(base - amount,2)
                            this.form.total_value =  _.round(base - amount,2)
                            this.form.total_igv =  _.round(this.form.total_value * 0.18,2)
                            this.form.total_taxes =  _.round(this.form.total_igv,2)
                            this.form.total =  _.round(this.form.total_value + this.form.total_taxes,2)

                            this.form.discounts[pos].base = base
                            this.form.discounts[pos].amount = amount
                            this.form.discounts[pos].factor = factor

                        }

                    }

                }else if(this.form.affectation_type_prepayment == 20){

                    let exonerated_discount = _.find(this.form.discounts,{'discount_type_id':'05'})


                    this.form.total_discount =  _.round(amount,2)
                    this.form.total_exonerated =  _.round(base - amount,2)
                    this.form.total_value =  this.form.total_exonerated
                    this.form.total =  this.form.total_value

                    if(global_discount>0 && !exonerated_discount){
                        this.form.discounts.push({
                                discount_type_id: '05',
                                description: 'Descuentos globales por anticipos exonerados',
                                factor: factor,
                                amount: amount,
                                base: base
                            })

                    }else{

                        let position = this.form.discounts.indexOf(exonerated_discount);

                        if(position > -1){

                            this.form.discounts[position].base = base
                            this.form.discounts[position].amount = amount
                            this.form.discounts[position].factor = factor

                        }

                    }

                }else if(this.form.affectation_type_prepayment == 30){

                    let unaffected_discount = _.find(this.form.discounts,{'discount_type_id':'06'})

                    this.form.total_discount =  _.round(amount,2)
                    this.form.total_unaffected =  _.round(base - amount,2)
                    this.form.total_value =  this.form.total_unaffected
                    this.form.total =  this.form.total_value

                    if(global_discount>0 && !unaffected_discount){
                        this.form.discounts.push({
                                discount_type_id: '06',
                                description: 'Descuentos globales por anticipos inafectos',
                                factor: factor,
                                amount: amount,
                                base: base
                            })
                    } else {
                        let position = this.form.discounts.indexOf(unaffected_discount);
                        if(position > -1){
                            this.form.discounts[position].base = base
                            this.form.discounts[position].amount = amount
                            this.form.discounts[position].factor = factor

                        }

                    }
                }

            },
            async changeDocumentPrepayment(index){

                let prepayment = await _.find(this.prepayment_documents, {id: this.form.prepayments[index].document_id})

                this.form.prepayments[index].number = prepayment.description
                this.form.prepayments[index].document_type_id = prepayment.document_type_id
                this.form.prepayments[index].amount = prepayment.amount
                this.form.prepayments[index].total = prepayment.total

                await this.changeTotalPrepayment()


            },
            clickAddPrepayment(){
                this.form.prepayments.push({
                    document_id:null,
                    number: null,
                    document_type_id:  null,
                    amount: 0,
                    total: 0,
                });

                this.changeTotalPrepayment()
            },
            clickRemovePrepayment(index) {

                this.form.prepayments.splice(index, 1)
                this.changeTotalPrepayment()
                if(this.form.prepayments.length == 0)
                    this.deletePrepaymentDiscount()

            },
            async changePrepaymentDeduction(){

                this.form.prepayments = []
                this.form.total_prepayment = 0
                await this.deletePrepaymentDiscount()

                if(this.prepayment_deduction){

                    await this.initialValueATPrepayment()
                    await this.changeTotalPrepayment()
                    await this.getDocumentsPrepayment()

                }
                else{

                    // this.form.total_prepayment = 0
                    // await this.deletePrepaymentDiscount()
                    this.cleanValueATPrepayment()
                }

            },
            setPendingAmount(){
                this.form.pending_amount_prepayment = this.form.has_prepayment ? this.form.total:0
            },
            initialValueATPrepayment(){
                this.form.affectation_type_prepayment = (!this.form.affectation_type_prepayment) ? 10 : this.form.affectation_type_prepayment
            },
            cleanValueATPrepayment(){
                this.form.affectation_type_prepayment = null
            },
            changeHasPrepayment(){

                if(this.form.has_prepayment){
                    this.initialValueATPrepayment()
                }else{
                    this.cleanValueATPrepayment()
                }

                this.setPendingAmount()

            },
            async changeAffectationTypePrepayment(){

                await this.initialValueATPrepayment()

                if(this.prepayment_deduction){

                    this.form.total_prepayment = 0
                    await this.deletePrepaymentDiscount()
                    await this.changePrepaymentDeduction()
                }

            },
            async deletePrepaymentDiscount(){

                let discount = await _.find(this.form.discounts, {'discount_type_id':'04'})
                let discount_exonerated = await _.find(this.form.discounts, {'discount_type_id':'05'})
                let discount_unaffected = await _.find(this.form.discounts, {'discount_type_id':'06'})

                let pos = this.form.discounts.indexOf(discount)
                if (pos > -1) {
                    this.form.discounts.splice(pos, 1)
                    this.changeTotalPrepayment()
                }

                let pos_exonerated = this.form.discounts.indexOf(discount_exonerated)
                if (pos_exonerated > -1) {
                    this.form.discounts.splice(pos_exonerated, 1)
                    this.changeTotalPrepayment()
                }

                let pos_unaffected = this.form.discounts.indexOf(discount_unaffected)
                if (pos_unaffected > -1) {
                    this.form.discounts.splice(pos_unaffected, 1)
                    this.changeTotalPrepayment()
                }

            },
            getDocumentsPrepayment(){
                this.$http.get(`/${this.resource}/prepayments/${this.form.affectation_type_prepayment}`).then((response) => {
                    this.prepayment_documents = response.data
                })
            },
            changeTotalPrepayment(){
                this.calculateTotal()
            },
            isActiveBussinessTurn(value){
                return (_.find(this.business_turns,{'value':value})) ? true:false
            },
            clickAddDocumentHotel(){
                this.showDialogFormHotel = true
            },
            clickAddDocumentTransport(){
                this.showDialogFormTransport = true
            },

            addDocumentHotel(hotel) {
                this.form.hotel = hotel
            },
            addDocumentTransport(transport) {
                this.form.transport = transport
            },
            changeIsReceivable(){

            },
            clickAddPayment() {

                this.form.payments.push({
                    id: null,
                    document_id: null,
                    date_of_payment:  moment().format('YYYY-MM-DD'),
                    payment_method_type_id: '01',
                    reference: null,
                    payment_destination_id: this.getPaymentDestinationId(),
                    payment: 0,
                });

            },
            getPaymentDestinationId() {

                if(this.configuration.destination_sale && this.payment_destinations.length > 0) {

                    let cash = _.find(this.payment_destinations, {id : 'cash'})

                    return (cash) ? cash.id : this.payment_destinations[0].id

                }

                return null

            },
            clickCancel(index) {
                this.form.payments.splice(index, 1);
            },
            ediItem(row, index) {
                row.indexi = index
                this.recordItem = row
                this.showDialogAddItem = true
            },
            searchRemoteCustomers(input) {

                if (input.length > 0) {
                    this.loading_search = true
                    let parameters = `input=${input}&document_type_id=${this.form.document_type_id}&operation_type_id=${this.form.operation_type_id}`

                    this.$http.get(`/${this.resource}/search/customers?${parameters}`)
                            .then(response => {
                                this.customers = response.data.customers
                                this.loading_search = false
                                this.input_person.number = null

                                if(this.customers.length == 0){
                                    this.filterCustomers()
                                    this.input_person.number = input
                                }
                            })
                } else {
                    this.filterCustomers()
                    this.input_person.number = null
                }

            },
            initForm() {
                this.errors = {}
                this.form = {
                    establishment_id: null,
                    document_type_id: null,
                    series_id: null,
                    seller_id: this.idUser,
                    number: '#',
                    date_of_issue: moment().format('YYYY-MM-DD'),
                    time_of_issue: moment().format('HH:mm:ss'),
                    customer_id: null,
                    currency_type_id: null,
                    purchase_order: null,
                    exchange_rate_sale: 0,
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
                    operation_type_id: null,
                    date_of_due: moment().format('YYYY-MM-DD'),
                    items: [],
                    charges: [],
                    discounts: [],
                    attributes: [],
                    guides: [],
                    payments: [],
                    prepayments: [],
                    legends: [],
                    detraction: {},
                    additional_information:null,
                    plate_number:null,
                    has_prepayment:false,
                    affectation_type_prepayment:null,
                    actions: {
                        format_pdf:'a4',
                    },
                    hotel: {},
                    transport: {},
                    customer_address_id:null,
                    pending_amount_prepayment:0,
                    payment_method_type_id:null,
                    show_terms_condition: true,
                    terms_condition: '',
                    payment_condition_id: '01',
                    fee: []
                }

                this.form_cash_document = {
                    document_id: null,
                    sale_note_id: null
                }

                this.clickAddPayment()
                this.clickAddInitGuides()
                this.is_receivable = false
                this.total_global_discount = 0
                this.is_amount = true
                this.prepayment_deduction = false
                this.imageDetraction = {}
                this.$eventHub.$emit('eventInitForm')

                this.initInputPerson()

                if(!this.configuration.restrict_receipt_date){
                  this.datEmision = {}
                }

                this.enabled_payments = true
                this.readonly_date_of_due = false
            },
            initInputPerson(){
                this.input_person = {
                    number:null,
                    identity_document_type_id:null
                }
            },
            resetForm() {
                this.activePanel = 0
                this.initForm()
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null
                this.form.establishment_id = (this.establishments.length > 0)?this.establishments[0].id:null
                this.form.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
                this.form.operation_type_id = (this.operation_types.length > 0)?this.operation_types[0].id:null
                this.form.seller_id = (this.sellers.length > 0)?this.idUser:null;
                this.selectDocumentType()
                this.changeEstablishment()
                this.changeDocumentType()
                this.changeDateOfIssue()
                this.changeCurrencyType()
                // this.changeDestinationSale()

            },
            async changeOperationType() {
                this.form.customer_id = null
                await this.filterCustomers();
                await this.setDataDetraction();
            },
            // async filterDetractionTypes(){
            //     this.detraction_types =  await _.filter(this.all_detraction_types, {'operation_type_id':this.form.operation_type_id})
            // },
            async setDataDetraction(){

                if(this.form.operation_type_id === '1001'){

                    this.showDialogDocumentDetraction = true

                    // this.$message.warning('Sujeta a detracción');
                    // await this.filterDetractionTypes();
                    let legend = await _.find(this.form.legends,{'code':'2006'})
                    if(!legend) this.form.legends.push({code:'2006', value:'Operación sujeta a detracción'})
                    this.form.detraction.bank_account = this.company.detraction_account

                }else if(this.form.operation_type_id === '1004'){

                    this.showDialogDocumentDetraction = true
                    let legend = await _.find(this.form.legends,{'code':'2006'})
                    if(!legend) this.form.legends.push({code:'2006', value:'Operación Sujeta a Detracción - Servicios de Transporte - Carga'})
                    this.form.detraction.bank_account = this.company.detraction_account

                }else{

                    _.remove(this.form.legends,{'code':'2006'})
                    this.form.detraction = {}

                }
            },
            async changeDetractionType(){
                if(this.form.detraction){
                    this.form.detraction.amount = (this.form.currency_type_id == 'PEN') ? _.round(parseFloat(this.form.total) * (parseFloat(this.form.detraction.percentage)/100),2) : _.round((parseFloat(this.form.total) * this.form.exchange_rate_sale) * (parseFloat(this.form.detraction.percentage)/100),2)
                }
            },
            validateDetraction(){

                if(['1001', '1004'].includes(this.form.operation_type_id)){

                    let detraction = this.form.detraction

                    let tot = (this.form.currency_type_id == 'PEN') ? this.form.total:(this.form.total * this.form.exchange_rate_sale)
                    let total_restriction = (this.form.operation_type_id == '1001') ? 700 : 400

                    if(tot <= total_restriction)
                        return {success:false, message:`El importe de la operación debe ser mayor a S/ ${total_restriction}.00 o equivalente en USD`}

                    if(!detraction.detraction_type_id)
                        return {success:false, message:'El campo bien o servicio sujeto a detracción es obligatorio'}

                    if(!detraction.payment_method_id)
                        return {success:false, message:'El campo método de pago - detracción es obligatorio'}

                    if(!detraction.bank_account)
                        return {success:false, message:'El campo cuenta bancaria es obligatorio'}

                    if(detraction.amount <= 0)
                        return {success:false, message:'El campo total detracción debe ser mayor a cero'}

                }

                return {success:true}

            },
            changeEstablishment() {
                this.establishment = _.find(this.establishments, {'id': this.form.establishment_id})
                this.filterSeries()
                this.selectDefaultCustomer()
            },
            async selectDefaultCustomer(){

                if(this.establishment.customer_id){

                    await this.$http.get(`/${this.resource}/search/customer/${this.establishment.customer_id}`).then((response) => {
                        this.all_customers = response.data.customers
                    })

                    await this.filterCustomers()
                    this.form.customer_id = (this.customers.length > 0) ? this.establishment.customer_id : null

                }

            },
            changeDocumentType() {
                this.filterSeries();
                this.cleanCustomer();
                this.filterCustomers();
            },
            cleanCustomer(){
                this.form.customer_id = null
            },
            changeDateOfIssue() {
              let minDate = moment().subtract(7, 'days')
              if(moment(this.form.date_of_issue) < minDate && this.configuration.restrict_receipt_date) {
                this.$message.error('No puede seleccionar una fecha menor a 6 días.');
                this.dateValid=false
              } else { this.dateValid = true }
                this.form.date_of_due = this.form.date_of_issue
                if (! this.isUpdate) {
                    this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                        this.form.exchange_rate_sale = response
                    });
                }
            },
            assignmentDateOfPayment(){
                this.form.payments.forEach((payment)=>{
                    payment.date_of_payment = this.form.date_of_issue
                })
            },
            filterSeries() {
                this.form.series_id = null
                this.series = _.filter(this.all_series, {'establishment_id': this.form.establishment_id,
                                                         'document_type_id': this.form.document_type_id,
                                                         'contingency': this.is_contingency});
                this.form.series_id = (this.series.length > 0)?this.series[0].id:null
            },
            filterCustomers() {
                if (['0101', '1001', '1004'].includes(this.form.operation_type_id)) {

                    if(this.form.document_type_id === '01') {
                        this.customers = _.filter(this.all_customers, {'identity_document_type_id': '6'})
                    } else {
                        if(this.document_type_03_filter) {
                            this.customers = _.filter(this.all_customers, (c) => { return c.identity_document_type_id !== '6' })
                        } else {
                            this.customers = this.all_customers
                        }
                    }

                } else {
                    this.customers = this.all_customers
                }
            },
            clickAddInitGuides() {
                this.form.guides.push({
                    document_type_id: '09',
                    number: null
                },{
                    document_type_id: '31',
                    number: null
                })
            },
            clickAddGuide() {
                this.form.guides.push({
                    document_type_id: null,
                    number: null
                })
            },
            clickRemoveGuide(index) {
                this.form.guides.splice(index, 1)
            },
            addRow(row) {
                if(this.recordItem) {
                    //this.form.items.$set(this.recordItem.indexi, row)
                    this.form.items[this.recordItem.indexi] = row
                    this.recordItem = null
                }
                else{
                      this.form.items.push(JSON.parse(JSON.stringify(row)));
                }

                this.calculateTotal();
            },
            clickRemoveItem(index) {
                this.form.items.splice(index, 1)
                this.calculateTotal()
            },
            changeCurrencyType() {
                this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                let items = []
                this.form.items.forEach((row) => {
                    items.push(calculateRowItem(row, this.form.currency_type_id, this.form.exchange_rate_sale))
                });
                this.form.items = items
                this.calculateTotal()
            },
            calculateTotal() {
                let total_discount = 0
                let total_charge = 0
                let total_exportation = 0
                let total_taxed = 0
                let total_exonerated = 0
                let total_unaffected = 0
                let total_free = 0
                let total_igv = 0
                let total_value = 0
                let total = 0
                let total_plastic_bag_taxes = 0
                this.form.items.forEach((row) => {
                    total_discount += parseFloat(row.total_discount)
                    total_charge += parseFloat(row.total_charge)

                    if (row.affectation_igv_type_id === '10') {
                        total_taxed += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '20') {
                        total_exonerated += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '30') {
                        total_unaffected += parseFloat(row.total_value)
                    }
                    if (row.affectation_igv_type_id === '40') {
                        total_exportation += parseFloat(row.total_value)
                    }
                    if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) < 0) {
                        total_free += parseFloat(row.total_value)
                    }
                    if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) > -1) {
                        total_igv += parseFloat(row.total_igv)
                        total += parseFloat(row.total)
                    }
                    total_value += parseFloat(row.total_value)
                    total_plastic_bag_taxes += parseFloat(row.total_plastic_bag_taxes)

                    if (['13', '14', '15'].includes(row.affectation_igv_type_id)) {

                        let unit_value = (row.total_value/row.quantity) / (1 + row.percentage_igv / 100)
                        let total_value_partial = unit_value * row.quantity
                        row.total_taxes = row.total_value - total_value_partial
                        row.total_igv = row.total_value - total_value_partial
                        row.total_base_igv = total_value_partial
                        total_value -= row.total_value

                    }

                });

                this.form.total_exportation = _.round(total_exportation, 2)
                this.form.total_taxed = _.round(total_taxed, 2)
                this.form.total_exonerated = _.round(total_exonerated, 2)
                this.form.total_unaffected = _.round(total_unaffected, 2)
                this.form.total_free = _.round(total_free, 2)
                this.form.total_igv = _.round(total_igv, 2)
                this.form.total_value = _.round(total_value, 2)
                this.form.total_taxes = _.round(total_igv, 2)
                this.form.total_plastic_bag_taxes = _.round(total_plastic_bag_taxes, 2)
                // this.form.total = _.round(total, 2)
                this.form.total = _.round(total + this.form.total_plastic_bag_taxes, 2)

                if(this.enabled_discount_global)
                    this.discountGlobal()

                if(this.prepayment_deduction)
                    this.discountGlobalPrepayment()

                if(['1001', '1004'].includes(this.form.operation_type_id))
                    this.changeDetractionType()

                this.setTotalDefaultPayment()
                this.setPendingAmount()

                this.calculateFee();
            },
            setTotalDefaultPayment(){

                if(this.form.payments.length > 0){

                    this.form.payments[0].payment = this.form.total
                }
            },
            changeTypeDiscount(){
                this.calculateTotal()
            },
            discountGlobal(){

                let base = this.form.total_taxed

                let amount = (this.is_amount) ? parseFloat(this.total_global_discount) : parseFloat(this.total_global_discount)/100 * base
                let factor = (this.is_amount) ? _.round(amount/base,5) : _.round(parseFloat(this.total_global_discount)/100,5)

                if(this.total_global_discount>0 && this.form.discounts.length == 0){

                    this.form.discounts.push({
                            discount_type_id: "02",
                            description: "Descuento Global afecta a la base imponible",
                            factor: 0,
                            amount: 0,
                            base: 0
                        })

                }


                if(this.form.discounts.length){

                    this.form.total_discount =  _.round(amount,2)
                    this.form.total_value =  _.round(base - amount,2)
                    this.form.total_igv =  _.round(this.form.total_value * 0.18,2)
                    this.form.total_taxes =  _.round(this.form.total_igv,2)
                    this.form.total =  _.round(this.form.total_value + this.form.total_taxes,2)

                    this.form.total_taxed =  this.form.total_value

                    this.form.discounts[0].base = base
                    this.form.discounts[0].amount = _.round(amount,2)
                    this.form.discounts[0].factor = factor
                }
            },
            async deleteInitGuides(){
                await _.remove(this.form.guides,{'number':null})
            },
            async asignPlateNumberToItems(){

                if(this.form.plate_number){

                    await this.form.items.forEach(item => {

                        let at = _.find(item.attributes, {'attribute_type_id': '5010'})

                        if(!at){
                            item.attributes.push({
                                attribute_type_id: '5010',
                                description: "Numero de Placa",
                                value: this.form.plate_number,
                                start_date: null,
                                end_date: null,
                                duration: null,
                            })
                        }

                    });

                }
            },
            async validateAffectationTypePrepayment() {

                let not_equal_affectation_type = 0

                await this.form.items.forEach(item => {
                    if(item.affectation_igv_type_id != this.form.affectation_type_prepayment){
                        not_equal_affectation_type++
                    }
                });

                return {
                    success: (not_equal_affectation_type > 0) ? false:true,
                    message: 'Los items deben tener tipo de afectación igual al seleccionado en el anticipo'
                }
            },
            validatePaymentDestination(){

                let error_by_item = 0

                this.form.payments.forEach((item)=>{

                    if(!['05', '08', '09'].includes(item.payment_method_type_id)){
                        if(item.payment_destination_id == null) error_by_item++;
                    }

                })

                return  {
                    error_by_item : error_by_item,
                }

            },
            async submit() {
                if (this.form.show_terms_condition) {
                    this.form.terms_condition = this.configuration.terms_condition_sale;
                }
                if(this.form.has_prepayment || this.prepayment_deduction){
                    let error_prepayment = await this.validateAffectationTypePrepayment()
                    if(!error_prepayment.success)
                        return this.$message.error(error_prepayment.message);
                }


                if(this.is_receivable){
                    this.form.payments = []
                }else{
                    let validate = await this.validate_payments()
                    if(validate.acum_total > parseFloat(this.form.total) || validate.error_by_item > 0) {
                        return this.$message.error('Los montos ingresados superan al monto a pagar o son incorrectos');
                    }

                    let validate_payment_destination = await this.validatePaymentDestination()

                    if(validate_payment_destination.error_by_item > 0) {
                        return this.$message.error('El destino del pago es obligatorio');
                    }

                }

                await this.deleteInitGuides()
                await this.asignPlateNumberToItems()

                let val_detraction = await this.validateDetraction()
                if(!val_detraction.success)
                    return this.$message.error(val_detraction.message);

                if(!this.enabled_payments){
                    this.form.payments = []
                }

                this.loading_submit = true
                let path = `/${this.resource}`;
                if (this.isUpdate) {
                    path = `/${this.resource}/${this.form.id}/update`;
                }
                this.$http.post(path, this.form).then(response => {
                    if (response.data.success) {
                        this.$eventHub.$emit('reloadDataItems', null)
                        this.resetForm();
                        this.documentNewId = response.data.data.id;
                        this.showDialogOptions = true;

                        this.form_cash_document.document_id = response.data.data.id;

                        // this.savePaymentMethod();
                        this.saveCashDocument();
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        this.$message.error(error.response.data.message);
                    }
                }).finally(() => {
                    this.loading_submit = false;
                });
            },
            saveCashDocument(){
                this.$http.post(`/cash/cash_document`, this.form_cash_document)
                    .then(response => {
                        if (!response.data.success) {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch(error => console.log(error))
            },
            validate_payments(){

                //eliminando items de pagos
                for (let index = 0; index < this.form.payments.length; index++) {
                    if(parseFloat(this.form.payments[index].payment) === 0)
                        this.form.payments.splice(index, 1)
                }

                let error_by_item = 0
                let acum_total = 0

                this.form.payments.forEach((item)=>{
                    acum_total += parseFloat(item.payment)
                    if(item.payment <= 0 || item.payment == null) error_by_item++;
                })

                return  {
                    error_by_item : error_by_item,
                    acum_total : acum_total
                }

            },

            close() {
                location.href = (this.is_contingency) ? `/contingencies` : `/${this.resource}`
            },
            reloadDataCustomers(customer_id) {
                // this.$http.get(`/${this.resource}/table/customers`).then((response) => {
                //     this.customers = response.data
                //     this.form.customer_id = customer_id
                // })
                this.$http.get(`/${this.resource}/search/customer/${customer_id}`).then((response) => {
                    this.customers = response.data.customers
                    this.form.customer_id = customer_id
                })
            },
            changeCustomer() {
                this.customer_addresses = [];
                let customer = _.find(this.customers, {'id': this.form.customer_id});
                this.customer_addresses = customer.addresses;
                if(customer.address)
                {
                    this.customer_addresses.unshift({
                        id:null,
                        address: customer.address
                    })
                }


                /*if(this.customer_addresses.length > 0) {
                    let address = _.find(this.customer_addresses, {'main' : 1});
                    this.form.customer_address_id = address.id;
                }*/
            },
            changePaymentCondition() {
                this.form.fee = [];
                this.form.payments = [];
                if(this.form.payment_condition_id === '01') {
                    this.clickAddPayment();
                }
                if(this.form.payment_condition_id === '02') {
                    this.clickAddFee();
                }
            },
            clickAddFee() {
                this.form.fee.push({
                    id: null,
                    date: moment().format('YYYY-MM-DD'),
                    currency_type_id: this.form.currency_type_id,
                    amount: 0,
                });
                this.calculateFee();
            },
            clickRemoveFee(index) {
                this.form.fee.splice(index, 1);
                this.calculateFee();
            },
            calculateFee() {
                let fee_count = this.form.fee.length;
                let total = this.form.total;
                let accumulated = 0;
                let amount = _.round(total / fee_count, 2);
                _.forEach(this.form.fee, row => {
                    accumulated += amount;
                    if (total - accumulated < 0) {
                        amount = _.round(total - accumulated + amount, 2);
                    }
                    row.amount = amount;
                })
            }
        }
    }
</script>
