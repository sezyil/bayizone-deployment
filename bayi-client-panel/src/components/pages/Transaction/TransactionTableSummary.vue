<script setup lang="ts">
export interface ITransactionTableSummary {
    total_credit: number
    formatted_total_credit: string
    total_debt: number
    formatted_total_debt: string
    balance: number
    formatted_balance: string
    total_paid_debt: number
    formatted_total_paid_debt: string
    total_paid_credit: number
    formatted_total_paid_credit: string
    total_unpaid_debt: number
    formatted_total_unpaid_debt: string
    total_unpaid_credit: number
    formatted_total_unpaid_credit: string
    total_overdue_debt: number
    formatted_total_overdue_debt: string
    total_overdue_credit: number
    formatted_total_overdue_credit: string
}
const props = defineProps<{
    data: ITransactionTableSummary
}>()
const debtClass = 'bg-primary text-danger';
const creditClass = 'bg-primary text-success';
</script>
<template>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Genel Özet</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <StatisticsCard :number-area="data.formatted_balance" class="cursor-pointer font-bold"
                                :class="data.balance < 0 ? debtClass : creditClass" icon-position="left"
                                text-content="Bakiye" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 ">
                            <StatisticsCard :number-area="data.formatted_total_overdue_debt"
                                :class="data.total_overdue_debt < 0 ? debtClass : creditClass"
                                class="cursor-pointer font-bold" icon-position="left"
                                text-content="Vadesi Geçen Borç" />

                        </div>
                        <div class="col-12 col-md-6 ">
                            <StatisticsCard :number-area="data.formatted_total_overdue_credit"
                                :class="data.total_overdue_credit < 0 ? debtClass : creditClass"
                                class="cursor-pointer font-bold" icon-position="left"
                                text-content="Vadesi Geçen Alacak" />
                        </div>

                        <div class="col-12 col-sm-6">
                            <StatisticsCard :number-area="data.formatted_total_unpaid_debt"
                                :class="data.total_unpaid_debt < 0 ? debtClass : creditClass"
                                class="cursor-pointer font-bold" icon-position="left" text-content="Ödenmeyen Borç" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <StatisticsCard :number-area="data.formatted_total_unpaid_credit"
                                :class="data.total_unpaid_credit < 0 ? debtClass : creditClass"
                                class="cursor-pointer font-bold" icon-position="left" text-content="Ödenmeyen Alacak" />
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Toplam İşlem Hacmi</h4>
                    <!-- summary all -->
                    <small class="text-muted">Bu tablo, şu ana kadar yapılan tüm işlemleri içerir.</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <StatisticsCard :number-area="data.formatted_total_paid_debt" :class="debtClass"
                                class="cursor-pointer font-bold" icon-position="left"
                                text-content="Toplam Ödenen Borç" />

                        </div>
                        <div class="col-12 col-sm-6">
                            <StatisticsCard :number-area="data.formatted_total_paid_credit" :class="creditClass"
                                class="cursor-pointer font-bold" icon-position="left"
                                text-content="Toplam Ödenen Alacak" />
                        </div>
                        <div class="col-12 col-md-6 ">
                            <StatisticsCard :number-area="data.formatted_total_debt" :class="debtClass"
                                class="cursor-pointer font-bold" icon-position="left" text-content="Toplam Borç" />

                        </div>

                        <div class="col-12 col-md-6 ">
                            <StatisticsCard :number-area="data.formatted_total_credit"
                                :class="data.total_credit < 0 ? debtClass : creditClass"
                                class="cursor-pointer font-bold" icon-position="left" text-content="Toplam Alacak" />

                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<style scoped></style>