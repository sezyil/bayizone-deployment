<script setup lang="ts">
import { ICustomerFilter } from './CustomerFilter.vue';
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useApi } from '/@src/composables/useApi';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { ECompanyCustomerGroup, getCompanyCustomerGroupDescription } from '/@src/shared/form/interface-company-customer';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const api = useApi();
const viewWrapper = useViewWrapper();
const props = defineProps({
    canViewWarehouse: {
        type: Boolean,
        default: false,
    },
    canViewBankAccount: {
        type: Boolean,
        default: false,
    },
    canDelete: {
        type: Boolean,
        default: false,
    },
    canUpdate: {
        type: Boolean,
        default: false,
    },
});
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Müşteriler");
const filter = ref<ICustomerFilter>({});

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "code", headText: "Müşteri Kodu" },
    { data: "name", headText: "Müşteri Adı" },
    { data: "phone", headText: "Telefon", render: (row: any, data: any) => data ?? "---" },
    { data: "email", headText: "Email" },
    { data: "group", headText: "Grup", render: (row: any, data: ECompanyCustomerGroup) => getCompanyCustomerGroupDescription(data) },
    { data: "country_name", headText: "Ülke" },
    {
        data: "id",
        headText: "İşlemler",
        render: (row, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    //create user if has_user
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: !row.has_user,
                        trigger: {
                            type: "click",
                            emitName: "createUser",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-user-plus"></i>`,
                        title: "Kullanıcı Oluştur"
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: props.canUpdate,
                        trigger: {
                            type: "click",
                            emitName: "showItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-eye"></i>`,
                        title: "Görüntüle"
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark",
                        nodeActive: props.canDelete,
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        title: "Sil"
                    }
                ]
            }
        }
    }
]);

const createUser = async (id: string) => {

    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu müşteri için kullanıcı oluşturmak istediğinize emin misiniz? Böylece müşteri giriş yapabilecek ve işlemlerini yapabilecektir.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, oluştur!',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {

            viewWrapper.setLoading(true);
            try {
                await api.post(API_URIS.CUSTOMERS + '/' + id + '/createCustomerUser');
                swal.fire("Başarılı", "Kullanıcı oluşturuldu ve giriş bilgileri mail olarak gönderildi", "success");
                refreshTable.value = true;
            } catch (err: any) {
                let _err = err?.response?.data?.msg ?? "Kullanıcı oluşturulamadı";
                swal.fire("Hata", _err, "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            } finally {
                viewWrapper.setLoading(false);
            }
        }
    })
}

const deleteItem = async (id: string) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await remove(id);
                swal.fire("Başarılı", "Silindi", "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire("Hata", "Silinemedi", "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const _redir = (str: any) => {
    router.push('/app/customers/' + str);
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "showItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
    //for bank accounts
    else if (e.name == "bankAccounts") router.push('/app/customers/' + e.firstParam + '/bank_accounts');
    else if (e.name == "offers") router.push('/app/offers?customer_id=' + e.firstParam);
    else if (e.name == "warehouses") router.push('/app/customers/:id/warehouses'.replace(':id', e.firstParam))
    else if (e.name == "createUser") createUser(e.firstParam);
};

const handleSearch = (e: any) => {
    filter.value = e;
    refreshTable.value = true;
}

</script>
<template>
    <CustomerFilter @search-triggered="handleSearch" />
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.CUSTOMERS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" :filter="filter"
        @event-triggered="eventWatcher" />
</template>