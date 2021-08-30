<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0"> {{ title }}</h3>
            </div>
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-12">
                        
                       <template  v-for="(option,ind) in records">
                            <template v-if="option.id === 3">
                                <el-checkbox class="plan_documents d-block" :disabled="true"  v-model="option.active"  :label="option.id"  :key="ind"  @change="submit(option.id)">{{option.name+' (Pronto)'}}</el-checkbox>
                            </template>
                            <template v-else>
                                <el-checkbox class="plan_documents d-block"  v-model="option.active"  :label="option.id"  :key="ind"  @change="submit(option.id)">{{option.name}}</el-checkbox>
                            </template>
                       </template>
                    </div>
                </div>
            </div>
 
        </div>
    </div>
</template>

<script> 

    export default {
        data() {
            return {
                title: null, 
                business_turns:[],
                resource: 'bussiness_turns',
                records: [],
            }
        },
        async created() {
            
            this.title = 'Giros de negocio'
            await this.getRecords()
        },
        methods: {
            
            submit(id) {
                this.loading_submit = true;
                
                this.$http.post(`/${this.resource}`,{id}).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.getRecords()
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            getRecords(){
                this.$http.get(`/${this.resource}/records`)
                    .then(response => { 
                        this.records = response.data    
                    }) 
            }
        }
    }
</script>
