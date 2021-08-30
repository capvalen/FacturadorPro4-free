<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Análisis comercial</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                            <tr slot="heading">
                            <th class="">#</th>
                            <th class="">Cliente</th>
                            <th class="">T. Doc - N° Doc</th>
                            <th class="">Zona</th>
                            <th class="">Celular</th> 
                            <th class="">Primera compra</th> 
                            <th class="">Ultima compra</th> 
                            <th class="">Tiempo promedio</th> 
                            <th class="">Cant. Visita</th> 
                            <th class="">Total</th> 

                            <th v-for="c in columns">{{c.toUpperCase()}}</th>

                           <!-- <th class="">Cinta</th> 
                            <th class="">Disco</th> 
                            <th class="">Cuchilla</th> 
                            <th class="">Estelitado</th> 
                            <th class="">Servicio</th> 
                            <th class="">Accesorios</th> -->

                            <th class="">Contactar el</th> 
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td> 
                            <td>{{row.customer_name}}</td>
                            <td>{{row.customer_doc }} - {{ row.customer_number }}<br/></td> 
                            <td>{{row.zone}}</td> 
                            <td>{{row.telephone}}</td> 
                            <td>{{row.first_document_date}}</td> 
                            <td>{{row.last_document_date}}</td> 
                            <td>{{row.prom_difference_days}}</td> 
                            <td>{{row.quantity_visit}}</td> 
                            <td>{{row.total}}</td>

                            <td v-for="c in columns">{{row[c.toUpperCase()]}}</td>

                            <!--<td>{{row.cinta}}</td> 
                            <td>{{row.disco}}</td> 
                            <td>{{row.cuchilla}}</td> 
                            <td>{{row.estelitado}}</td> 
                            <td>{{row.servicio}}</td> 
                            <td>{{row.accesorios}}</td> -->

                            <td>{{row.contact_date}}</td> 
                          
                        </tr>
                        
                    </data-table>
                     
                    
                </div> 
        </div>
 
    </div>
</template>

<script>
 
    import DataTable from '../../components/DataTableCommercialAnalysis.vue'

    export default { 
        components: {DataTable},
        data() {
            return {
                resource: 'reports/commercial-analysis',                 
                form: {}, 
                columns: []

            }
        },
        async created() {

            await this.$http.get(`/${this.resource}/columns`).then((response) => {
                this.columns = response.data.categories
            });
        },
        methods: { 
            
        }
    }
</script>