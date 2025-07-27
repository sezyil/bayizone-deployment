<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.PRODUCT" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { _delete, duplicateProduct, syncWithAi } from '/@src/request/request-product';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { get_product_image_url } from '/@src/utils/GLOB_URIS';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const props = defineProps({
    canDelete: {
        type: Boolean,
        default: false
    },
    canUpdate: {
        type: Boolean,
        default: false
    },
    canCreate: {
        type: Boolean,
        default: false
    }
})
const viewWrapper = useViewWrapper();
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Ürünler");

const refreshTable = ref(false);
const tableData = ref([]);
const userSession = useUserSession();
const dataContent = ref<IDataTableColumn[]>([
    /* image */
    {
        data: "image", headText: "RESİM", render: (row: any, data: any) => {
            let imgLink = get_product_image_url(data);
            let img = `<img src="${imgLink}" alt="image" style="width: 60px; height: 60px; border-radius: 50%;">`;
            let aTag = `<a href="${imgLink}" target="_blank">${img}</a>`;
            return aTag;
        }
    },
    {
        data: "name", headText: "ÜRÜN ADI", render: (row: any, data: any) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "a",
                        isRaw: false,
                        class: "text-primary cursor-pointer",
                        innerHTML: data,
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: row.id
                        }
                    }
                ]
            }
        }
    },
    { data: "model", headText: "Model Kodu", render: (row: any, data: any) => data ?? "---" },
    { data: "sku", headText: "Stok Kodu", render: (row: any, data: any) => data ?? "---" },
    { data: "default_price", headText: "FİYAT" },
    { data: "quantity", headText: "MİKTAR" },
    {
        data: "id",
        headText: "İşlemler",
        render: (row, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "syncAi",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-sync-alt"></i>`,
                        title: 'Yapay Zeka Eşitle',
                        nodeActive: userSession.user?.ai_support
                            ? (row.ai_sync ? false : true)
                            : false
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        nodeActive: props.canUpdate
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        nodeActive: props.canDelete
                    },
                    /* duplicate */
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark",
                        trigger: {
                            type: "click",
                            emitName: "duplicateItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-clone"></i>`,
                        nodeActive: props.canUpdate
                    }
                ]
            }
        }
    }
]);

const deleteItem = async (id: number) => {
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
                await _delete(id);
                swal.fire("Başarılı", "Ürün başarıyla silindi", "success"), refreshTable.value = true;
            } catch (err) {
                catchFieldError(err, undefined, undefined, () => router.push('/app'));
            }
        }
    })
}

const _redir = (str: any) => {
    router.push('/app/catalog/product/' + str + '/edit');
}

const syncItem = async (id: number) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Ürün yapay zeka ile eşitlenmeye başlayacaktır. Sonraki ürün bilgi değişiklikleri otomatik eşitlenecektir.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {
            viewWrapper.setLoading(true);
            try {
                await syncWithAi(id);
                swal.fire("Başarılı", "Ürün başarıyla eşitlendi", "success"), refreshTable.value = true;
            } catch (err) {
                viewWrapper.setLoading(false);
                catchFieldError(err, undefined, undefined, () => router.push('/app'));
            }
            viewWrapper.setLoading(false);
        }
    })
}

const duplicateItem = async (id: number) => {
    swal.fire({
        title: 'Bu ürünü kopyalamak istediğinize emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, kopyala!',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await duplicateProduct(id);
                swal.fire("Başarılı", "Ürün başarıyla kopyalandı", "success"), refreshTable.value = true;
            } catch (err) {
                catchFieldError(err, undefined, undefined, () => router.push('/app'));
            }
        }
    })

}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
    else if (e.name == "duplicateItem") duplicateItem(e.firstParam);
    else if (e.name == "syncAi") syncItem(e.firstParam);
};


</script>