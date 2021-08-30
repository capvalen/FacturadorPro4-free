<template> 
    <div>
        <vcl-table :rows="rows" :columns="columns"></vcl-table>
        <template v-if="!hideCircle">
            <br/><br/>
            <vue-content-loading v-bind="$attrs" :width="100" :height="100">
                <circle cx="50%" cy="50%" :r="radius"/>
            </vue-content-loading>
        </template>
    </div>
</template>


<script>
    
    import { VclTable, VueContentLoading  } from 'vue-content-loading';

    export default {
        props: {
            hideCircle: Boolean,
            radius: Number,
            rows: Number,
            columns: Number,
        },
        components: { VclTable, VueContentLoading },
        data () {
            return {  
            }
        },
        computed: {
        },
        created() { 
        }, 
        methods: { 
        }
    }
</script>