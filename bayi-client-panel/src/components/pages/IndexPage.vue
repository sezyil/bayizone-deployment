<script setup lang="ts">
import { useApi } from '/@src/composables/useApi';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';


const api = useApi();

</script>

<template>
    <div class="dashboard-wrapper">
        <CardArea />
        <LastFiveOfferOrder />
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Sipariş Özeti</h6>
                    </div>
                    <div class="card-body">
                        <MonthlyOrderChart />
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Teklif Özeti</h6>
                    </div>
                    <div class="card-body">
                        <MonthlyOfferChart />
                    </div>
                </div>
            </div>
        </div>
        <PieChart />
        <!-- Monthly Transaction Chart -->

    </div>
</template>

<style scoped></style>