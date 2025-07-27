<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.BANK_ACCOUNTS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import CustomerBankAccountApi from '/@src/request/request-customer-bank-accounts';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const apiClass = new CustomerBankAccountApi();
const swal = inject('$swal') as SwalInstance;
const router = useRouter();
const { t } = useI18n();
const cardTitle = ref(t('components.bank_accounts.index.title'));



const refreshTable = ref(false);
const tableData = ref([]);

interface IResponse {
    id: string
    company_customer_id: string
    company_customer_name: string
    iban: string
    bank_name: string
    branch_name: string
    account_name: string
    status: boolean
}

const dataContent = ref<IDataTableColumn[]>([
    { data: "bank_name", headText: t('components.bank_accounts.index.bankName') },
    { data: "branch_name", headText: t('components.bank_accounts.index.branchName') },
    { data: "account_name", headText: t('components.bank_accounts.index.accountName') },
    { data: "iban", headText: t('components.bank_accounts.index.iban') },
    { data: "status", headText: t('common.status'), render: (row: any, data: boolean) => data ? t('common.active') : t('common.passive') },
    {
        data: "id",
        headText: t('common.actions'),
        render: (row: IResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-primary mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`, title: t('actions.edit')
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-danger",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`, title: t('actions.delete')
                    }
                ]
            }
        }
    }

]);

const deleteItem = async (id: string) => {
    swal.fire({
        title: t('actions.deleteConfirm'),
        text: t('actions.deleteNotRecoverable'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('actions.yes'),
        cancelButtonText: t('actions.no')
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.remove(id);
                swal.fire(t('common.success'), t('actions.deleteSuccess'), "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire(t('common.error'), t('actions.deleteError'), "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const _redir = (id: any) => {
    router.push(`/app/bank_accounts/${id}/edit`);
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};


</script>