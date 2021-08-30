<template>
    <div class="col-xl-6">
        <section class="card card-featured-left card-featured-secondary">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">Establecimiento</label>
                        <el-select v-model="form.establishment_id" @change="loadAll">
                            <el-option
                            v-for="option in establishments"
                            :key="option.id"
                            :value="option.id"
                            :label="option.name"
                            ></el-option>
                        </el-select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">Periodo</label>
                        <el-select v-model="form.period" @change="changePeriod">
                        <el-option key="all" value="all" label="Todos"></el-option>
                        <el-option key="month" value="month" label="Por mes"></el-option>
                        <el-option key="between_months" value="between_months" label="Entre meses"></el-option>
                        <el-option key="date" value="date" label="Por fecha"></el-option>
                        <el-option key="between_dates" value="between_dates" label="Entre fechas"></el-option>
                        </el-select>
                    </div>
                    <template v-if="form.period === 'month' || form.period === 'between_months'">
                        <div class="col-md-6">
                        <label class="control-label">Mes de</label>
                        <el-date-picker
                            v-model="form.month_start"
                            type="month"
                            @change="changeDisabledMonths"
                            value-format="yyyy-MM"
                            format="MM/yyyy"
                            :clearable="false"
                        ></el-date-picker>
                        </div>
                    </template>
                    <template v-if="form.period === 'between_months'">
                        <div class="col-md-6">
                        <label class="control-label">Mes al</label>
                        <el-date-picker
                            v-model="form.month_end"
                            type="month"
                            :picker-options="pickerOptionsMonths"
                            @change="loadAll"
                            value-format="yyyy-MM"
                            format="MM/yyyy"
                            :clearable="false"
                        ></el-date-picker>
                        </div>
                    </template>
                    <template v-if="form.period === 'date' || form.period === 'between_dates'">
                        <div class="col-md-6">
                        <label class="control-label">Fecha del</label>
                        <el-date-picker
                            v-model="form.date_start"
                            type="date"
                            @change="changeDisabledDates"
                            value-format="yyyy-MM-dd"
                            format="dd/MM/yyyy"
                            :clearable="false"
                        ></el-date-picker>
                        </div>
                    </template>
                    <template v-if="form.period === 'between_dates'">
                        <div class="col-md-6">
                        <label class="control-label">Fecha al</label>
                        <el-date-picker
                            v-model="form.date_end"
                            type="date"
                            :picker-options="pickerOptionsDates"
                            @change="loadAll"
                            value-format="yyyy-MM-dd"
                            format="dd/MM/yyyy"
                            :clearable="false"
                        ></el-date-picker>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {

}
</script>

<style>

</style>
