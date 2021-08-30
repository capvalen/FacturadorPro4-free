function calculateRowItem(row_old, currency_type_id_new, exchange_rate_sale) {
    // console.log(currency_type_id_new, exchange_rate_sale)

    let currency_type_id_old = row_old.item.currency_type_id
    let unit_price = parseFloat(row_old.item.unit_price)
    // } else {
    //     unit_price = parseFloat(row_old.item.unit_price) * 1.18
    // }
    let warehouse_id = row_old.warehouse_id

    // console.log(row_old)

    if (currency_type_id_old === 'PEN' && currency_type_id_old !== currency_type_id_new)
    {
        unit_price = unit_price / exchange_rate_sale;
    }

    if (currency_type_id_new === 'PEN' && currency_type_id_old !== currency_type_id_new)
    {
        unit_price = unit_price * exchange_rate_sale;
    }

    // unit_price = _.round(unit_price, 4);

    // $table->increments('id');
    // $table->unsignedInteger('document_id');
    // $table->unsignedInteger('item_id');
    // $table->json('item');
    // $table->integer('quantity');
    // $table->decimal('unit_value', 12, 2);
    //
    // $table->char('affectation_igv_type_id', 2);
    // $table->decimal('total_base_igv', 12, 2);
    // $table->decimal('percentage_igv', 12, 2);
    // $table->decimal('total_igv', 12, 2);
    //
    // $table->char('system_isc_type_id', 2)->nullable();
    // $table->decimal('total_base_isc', 12, 2)->default(0);
    // $table->decimal('percentage_isc', 12, 2)->default(0);
    // $table->decimal('total_isc', 12, 2)->default(0);
    //
    // $table->decimal('total_base_other_taxes', 12, 2)->default(0);
    // $table->decimal('percentage_other_taxes', 12, 2)->default(0);
    // $table->decimal('total_other_taxes', 12, 2)->default(0);
    // $table->decimal('total_taxes', 12, 2);
    //
    // $table->char('price_type_id', 2);
    // $table->decimal('unit_price', 12, 2);
    //
    // $table->decimal('total_value', 12, 2);
    // $table->decimal('total', 12, 2);
    //
    // $table->json('attributes')->nullable();
    // $table->json('charges')->nullable();
    // $table->json('discounts')->nullable();

    let row = {
        item_id: row_old.item.id,
        // item_description: row_old.item.description,
        item: row_old.item,
        currency_type_id: currency_type_id_new,
        quantity: row_old.quantity,
        unit_value: 0,
        affectation_igv_type_id: row_old.affectation_igv_type_id,
        affectation_igv_type: row_old.affectation_igv_type,
        total_base_igv: 0,
        percentage_igv: 18,
        total_igv: 0,
        system_isc_type_id: null,
        total_base_isc: 0,
        percentage_isc: 0,
        total_isc: 0,
        total_base_other_taxes: 0,
        percentage_other_taxes: 0,
        total_other_taxes: 0,
        total_plastic_bag_taxes: 0,
        total_taxes: 0,
        price_type_id: '01',
        unit_price: unit_price,
        input_unit_price_value: row_old.input_unit_price_value,
        total_value: 0,
        total_discount: 0,
        total_charge: 0,
        total: 0,
        attributes: row_old.attributes,
        charges: row_old.charges,
        discounts: row_old.discounts,
        warehouse_id: warehouse_id,
        name_product_pdf: row_old.name_product_pdf
    };

    let percentage_igv = 18
    let unit_value = row.unit_price

    if (row.affectation_igv_type_id === '10') {
        unit_value = row.unit_price / (1 + percentage_igv / 100)
    }

    // row.unit_value = _.round(unit_value, 4)

    row.unit_value = unit_value

    let total_value_partial = unit_value * row.quantity


    /* Discounts */
    let discount_base = 0
    let discount_no_base = 0
    // row.discounts.forEach((discount, index) => {
    //     discount.percentage = parseFloat(discount.percentage)
    //     discount.factor = discount.percentage / 100
    //     discount.base = _.round(total_value_partial, 2)
    //     discount.amount = _.round(discount.base * discount.factor, 2)
    //     if (discount.discount_type.base) {
    //         discount_base += discount.amount
    //     } else {
    //         discount_no_base += discount.amount
    //     }
    //     row.discounts.splice(index, discount)
    // })

    row.discounts.forEach((discount, index) => {

        if(discount.is_amount){

            if (discount.discount_type.base) {

                discount.base = _.round(total_value_partial, 2)
                //amount and percentage are equals in input
                discount.amount = _.round(discount.percentage, 2)

                discount.percentage =  _.round(100 * (parseFloat(discount.amount) / parseFloat(discount.base)),2)

                discount.factor = _.round(discount.percentage / 100, 2)

                discount_base += discount.amount

            } else {

                let aux_total_line = row.unit_price * row.quantity
                let affectation_igv_type_exonerated = ['20','21','30','31','32','33','34','35','36','37']

                if (!affectation_igv_type_exonerated.includes(row.affectation_igv_type_id)) {
                    total_value_partial = (aux_total_line - discount.percentage) / (1 + percentage_igv / 100)
                }else{
                    total_value_partial = aux_total_line - discount.percentage
                }

                discount.base = _.round(aux_total_line, 2)
                //amount and percentage are equals in input
                discount.amount = _.round(discount.percentage, 2)

                discount.percentage =  _.round(100 * (parseFloat(discount.amount) / parseFloat(discount.base)),2)

                discount.factor = _.round(discount.percentage / 100, 2)

                // discount_no_base += discount.amount
            }

        }else{

            discount.percentage = parseFloat(discount.percentage)
            discount.factor = discount.percentage / 100
            discount.base = _.round(total_value_partial, 2)
            discount.amount = _.round(discount.base * discount.factor, 2)
            if (discount.discount_type.base) {
                discount_base += discount.amount
            } else {
                discount_no_base += discount.amount
            }

        }

        row.discounts.splice(index, discount)
    })

    // console.log('total base discount:'+discount_base)
    // console.log('total no base discount:'+discount_no_base)




    /* Charges */
    let charge_base = 0
    let charge_no_base = 0
    row.charges.forEach((charge, index) => {
        charge.percentage = parseFloat(charge.percentage)
        charge.factor = charge.percentage / 100
        charge.base = _.round(total_value_partial, 2)
        charge.amount = _.round(charge.base * charge.factor, 2)
        if (charge.charge_type.base) {
            charge_base += charge.amount
        } else {
            charge_no_base += charge.amount
        }
        row.charges.splice(index, charge)
    })
    // console.log('total base charge:'+charge_base)
    // console.log('total no base charge:'+charge_no_base)

    let total_isc = 0
    let total_other_taxes = 0

    let total_discount = discount_base + discount_no_base
    let total_charge = charge_base + charge_no_base
    let total_value = total_value_partial - total_discount + total_charge
    let total_base_igv = total_value_partial - discount_base + total_isc

    // console.log(total_base_igv, total_value)

    let total_igv = 0

    if (row.affectation_igv_type_id === '10') {
        total_igv = total_base_igv * percentage_igv / 100
    }
    if (row.affectation_igv_type_id === '20') { //Exonerated
        total_igv = 0
    }
    if (row.affectation_igv_type_id === '30') { //Unaffected
        total_igv = 0
    }

    let total_taxes = total_igv + total_isc + total_other_taxes
    let total = total_value + total_taxes

    row.total_charge = _.round(total_charge, 2)
    row.total_discount = _.round(total_discount, 2)
    row.total_charge = _.round(total_charge, 2)
    row.total_value = _.round(total_value, 2)
    row.total_base_igv = _.round(total_base_igv, 2)
    row.total_igv =  _.round(total_igv, 2)
    row.total_taxes = _.round(total_taxes, 2)
    row.total = _.round(total, 2)

    if (row.affectation_igv_type.free) {
        row.price_type_id = '02'
        row.unit_value = 0
        // row.total_value = 0
        row.total = 0
    }

    //impuesto bolsa
    if(row_old.has_plastic_bag_taxes){
        row.total_plastic_bag_taxes = _.round(row.quantity * row.item.amount_plastic_bag_taxes, 1)
    }

    // console.log(row)
    return row
}

export {calculateRowItem}
