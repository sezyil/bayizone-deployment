<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import ShipmentsApi from '/@src/request/request-shipments';
import { ShipmentAvailableOrdersResponse, ShipmentEditDataResponse } from '/@src/shared/response/interface-shipment-response';
import { CurrencyTypes } from '/@src/shared/response/interface-utils-response';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';

const props = defineProps<{
    shipmentId?: string
}>();
const api = new ShipmentsApi();
const router = useRouter();
const swal = useSwal();
interface ISelectedList {
    order_id: string;
    order_no: string;
    selectedItems: {
        lineData: ShipmentAvailableOrdersResponse.IResponse["items"][0];
        line_id: number;
        quantity: number;
    }[]
}

const formData = ref<{
    customer_id: string;
    currency: CurrencyTypes | "0";
    container_no: string;
    carrier: string
    note: string
}>({
    customer_id: "",
    currency: "0",
    container_no: "",
    carrier: "",
    note: "",
})
const selectedList = ref<ISelectedList[]>([]);

const loadingOrderData = ref<boolean>(false);
const availableOrderData = ref<ShipmentAvailableOrdersResponse.IResponse[]>([]);
const computedAvailableOrderData = computed(() => {
    //if added to selected list filter line_id
    return availableOrderData.value.map((order) => {
        let selectedOrder = selectedList.value.find((selectedOrder) => selectedOrder.order_id == order.order_id);
        if (!selectedOrder) {
            return order;
        }
        return {
            ...order,
            items: order.items.filter((line) => {
                return !selectedOrder.selectedItems.find((selectedLine) => selectedLine.line_id == line.line_id);
            })
        }
    })
})


const getAvailableOrders = async () => {
    if (!formData.value.customer_id || formData.value.currency == "0") {
        return;
    }
    loadingOrderData.value = true;
    try {
        const { data } = await api.getAvailableOrders(formData.value.customer_id, formData.value.currency);
        availableOrderData.value = data.data as ShipmentAvailableOrdersResponse.IResponse[];
    } catch (error) {
        console.error(error);
    } finally {
        loadingOrderData.value = false;
    }
}

const getExistingData = async () => {
    if (!props.shipmentId) {
        return;
    }
    try {
        const { data } = await api.editData(props.shipmentId);
        let __data = data.data as ShipmentEditDataResponse.IResponse;
        formData.value = {
            customer_id: __data.company_customer_id,
            currency: __data.currency as CurrencyTypes,
            container_no: __data.container_no,
            carrier: __data.carrier,
            note: __data.note
        }
        await getAvailableOrders();
        if (!availableOrderData.value) {
            return;
        }

        let _deleted = __data.selectedList.filter((order) => {
            return !availableOrderData.value.find((availableOrder) => availableOrder.order_id == order.order_id);
        })

        let filtered = __data.selectedList.filter((order) => {
            return availableOrderData.value.find((availableOrder) => availableOrder.order_id == order.order_id);
        })
        if (_deleted.length > 0) {
            swal.fire("Dikkat", "Bazı siparişler silinmiş olabilir. Silinen siparişler otomatik olarak çıkarıldı.", "warning");
        }

        if (filtered.length > 0) {
            selectedList.value = filtered.map((order) => {
                let findedOrder = availableOrderData.value.find((availableOrder) => availableOrder.order_id == order.order_id) as ShipmentAvailableOrdersResponse.IResponse;
                return {
                    order_id: order.order_id,
                    order_no: findedOrder ? findedOrder.order_no : "",
                    selectedItems: order.items.map((line) => {
                        return {
                            lineData: findedOrder.items.find((item) => item.line_id == line.line_id) as ShipmentAvailableOrdersResponse.IResponse["items"][0],
                            line_id: line.line_id,
                            quantity: line.quantity
                        }
                    })
                }
            })
        } else {
            selectedList.value = [];
            await swal.fire("Dikkat", `Seçtiğiniz siparişler bulunamadı. Gönderilmiş veya silinmiş olabilirler. Karışıklık olmaması için lütfen sevkiyat kaydını silin.`, "warning")
            router.push("/app/shipments");
        }


    } catch (error) {
        console.error(error);
    }
}

const addProduct = (order: ShipmentAvailableOrdersResponse.IResponse, line: ShipmentAvailableOrdersResponse.IResponse["items"][0]) => {
    let selectedOrder = selectedList.value.find((selectedOrder) => selectedOrder.order_id == order.order_id);
    if (!selectedOrder) {
        selectedOrder = {
            order_id: order.order_id,
            order_no: order.order_no,
            selectedItems: []
        }
        selectedList.value.push(selectedOrder);
    }
    let selectedLine = selectedOrder.selectedItems.find((selectedLine) => selectedLine.line_id == line.line_id);
    if (!selectedLine) {
        selectedOrder.selectedItems.push({
            lineData: line,
            line_id: line.line_id,
            quantity: 1
        })
    } else {
        selectedLine.quantity++;
    }
}

const removeProduct = (order: ShipmentAvailableOrdersResponse.IResponse, line: ShipmentAvailableOrdersResponse.IResponse["items"][0]) => {
    let selectedOrder = selectedList.value.find((selectedOrder) => selectedOrder.order_id == order.order_id);
    if (!selectedOrder) {
        return;
    }
    let selectedLine = selectedOrder.selectedItems.find((selectedLine) => selectedLine.line_id == line.line_id);
    if (!selectedLine) {
        return;
    }
    if (selectedLine.quantity > 1) {
        selectedLine.quantity--;
    } else {
        selectedOrder.selectedItems = selectedOrder.selectedItems.filter((selectedLine) => selectedLine.line_id != line.line_id);
    }
    if (selectedOrder.selectedItems.length == 0) {
        selectedList.value = selectedList.value.filter((selectedOrder) => selectedOrder.order_id != order.order_id);
    }
}

const removeLine = (order: ISelectedList, line: ISelectedList["selectedItems"][0]) => {
    let selectedOrder = selectedList.value.find((selectedOrder) => selectedOrder.order_id == order.order_id);
    if (!selectedOrder) {
        return;
    }
    selectedOrder.selectedItems = selectedOrder.selectedItems.filter((selectedLine) => selectedLine.line_id != line.line_id);
    if (selectedOrder.selectedItems.length == 0) {
        selectedList.value = selectedList.value.filter((selectedOrder) => selectedOrder.order_id != order.order_id);
    }
}

const save = async (e: Event) => {
    e.preventDefault();
    if (!formData.value.customer_id || formData.value.currency == "0") {
        swal.fire("Müşteri ve para birimi seçiniz.");
        return;
    }
    //check if selectedList is empty
    if (selectedList.value.length == 0) {
        swal.fire("Lütfen en az bir kalemi seçiniz.");
        return;
    }

    let selectedData = selectedList.value.map((order) => {
        return {
            order_id: order.order_id,
            items: order.selectedItems.map((line) => {
                return {
                    line_id: line.line_id,
                    quantity: line.quantity
                }
            })
        }
    })
    let data = {
        ...formData.value,
        selectedList: selectedData
    }

    try {
        if (props.shipmentId) {
            let response = await api.update(props.shipmentId, data);
        } else {
            let response = await api.add(data);
        }
        swal.fire("Kayıt Başarılı", "Sevkiyat başarılı bir şekilde kaydedildi.", "success").then(() => {
            if (props.shipmentId) {
                return location.reload();
            }
            router.push("/app/shipments");
        })
    } catch (error: any) {
        catchFieldError(error);
    }


}


//if currency and customer_id valid, get available orders
watch([() => formData.value.customer_id, () => formData.value.currency], async ([customer_id, currency]) => {
    if (customer_id && currency != "0" && !props.shipmentId) {
        await getAvailableOrders();
    }
})

onMounted(async () => {
    await getExistingData();
})
</script>
<template>
    <div>
        <!-- Bu ekranda sadece sipariş durumu sevke hazır olan ve sistem tarafından yönetilen siparişleri görebileceksiniz. dismissable -->
        <div class="alert alert-info alert-styled-left alert-dismissible">
            <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
            <span class="font-weight-semibold">Bilgi!</span> Bu ekranda sadece sipariş durumu sevke hazır olan ve sistem
            tarafından yönetilen siparişleri görebileceksiniz.
        </div>
        <form action="" @submit.prevent="save">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sevkiyat Formu</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <VInputCustomers v-model="formData.customer_id" />
                        </div>
                        <div class="col-md-6">
                            <VInputCurrencies v-model="formData.currency" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <InputWithError>
                                <label>Konteyner No</label>
                                <input type="text" class="form-control" v-model="formData.container_no" />
                            </InputWithError>
                        </div>
                        <div class="col-md-6">
                            <InputWithError>
                                <label>Nakliye/Sevkiyat Firması</label>
                                <input type="text" class="form-control" v-model="formData.carrier" />
                            </InputWithError>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <InputWithError>
                                <label>Not</label>
                                <textarea class="form-control" v-model="formData.note"></textarea>
                            </InputWithError>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card" v-if="availableOrderData.length > 0">
                <div class="card-header">
                    <h4 class="card-title">Dahil Edilebilir Siparişler</h4>
                </div>
                <div class="card-body">
                    <VLoader :active="loadingOrderData" />
                    <div>
                        <div class="row" v-if="availableOrderData.length > 0">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sipariş No</th>
                                                <th>Kalemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in computedAvailableOrderData" :key="order.order_id">
                                                <td style="text-align: center; vertical-align: middle;width: 20%;">
                                                    <RouterLink :to="'/app/customer_orders/' + order.order_id + '/show'"
                                                        target="_blank">
                                                        {{ order.order_no }}
                                                    </RouterLink>
                                                </td>
                                                <td>
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Ürün Adı</th>
                                                                <!-- miktar -->
                                                                <th class="text-center">Miktar</th>
                                                                <!-- gönderilen -->
                                                                <th class="text-center">Gönderilen</th>
                                                                <!-- kalan -->
                                                                <th class="text-center">Kalan</th>
                                                                <th class="text-center"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody v-if="order.items.length > 0">
                                                            <tr v-for="line in order.items" :key="line.line_id">
                                                                <td>
                                                                    {{ line.product_name }}
                                                                    <br />
                                                                    <div class="d-flex gap-1 flex-row"
                                                                        :class="{ 'mb-1': line.dimension }"
                                                                        v-if="line.color">
                                                                        <span v-for="color in line.color"
                                                                            class="badge bg-primary">
                                                                            {{ color.variant.name }}
                                                                            : {{ color.variant_value.name }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="d-flex gap-1 flex-row"
                                                                        v-if="line.dimension">
                                                                        <span v-for="dimension in line.dimension"
                                                                            class="badge bg-primary">
                                                                            {{ dimension.variant.name }}
                                                                            :
                                                                            Y: {{ dimension.variant_value.value.height
                                                                            }}x
                                                                            G: {{ dimension.variant_value.value.width
                                                                            }}x
                                                                            U: {{ dimension.variant_value.value.length
                                                                            }}
                                                                        </span>
                                                                    </div>

                                                                </td>
                                                                <td>
                                                                    {{ line.quantity }}
                                                                </td>
                                                                <td>
                                                                    {{ line.sent_quantity }}
                                                                </td>
                                                                <td>
                                                                    {{ line.remaining_quantity }}
                                                                </td>
                                                                <!-- ekle çıkar -->
                                                                <td>
                                                                    <div class="d-flex gap-1 flex-row">
                                                                        <button type="button"
                                                                            class="btn btn-primary btn-sm"
                                                                            @click="addProduct(order, line)">
                                                                            Ekle
                                                                        </button>
                                                                    </div>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tr v-else>
                                                            <td colspan="5">
                                                                <table class="table table-bordered table-striped">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="5" class="text-center">Kalem
                                                                                Bulunamadı</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="alert alert-info">
                    <p class="text-center m-0">Dahil edilebilir sipariş bulunamadı. Lütfen müşteri ve para birimi
                        seçiniz.
                        Eğer Sipariş
                        bulunamazsa müşteri siparişlerini kontrol ediniz.</p>
                </div>
            </div>

            <div class="card" v-if="selectedList.length > 0">
                <div class="card-header">
                    <h4 class="card-title">Seçilen Siparişler</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div class="row" v-if="selectedList.length > 0">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sipariş No</th>
                                                <th>Kalemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in selectedList" :key="order.order_id">
                                                <td style="text-align: center; vertical-align: middle;width: 20%;">
                                                    <RouterLink :to="'/app/customer_orders/' + order.order_id + '/show'"
                                                        target="_blank">
                                                        {{ order.order_no }}
                                                    </RouterLink>
                                                </td>
                                                <td>
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Ürün Adı</th>
                                                                <!-- miktar -->
                                                                <th class="text-center">Miktar</th>
                                                                <!-- kalan -->
                                                                <th class="text-center">Kalan</th>
                                                                <!-- gönderilecek -->
                                                                <th class="text-center">Gönderilecek</th>
                                                                <th class="text-center"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="line in order.selectedItems" :key="line.line_id">
                                                                <td>
                                                                    {{ line.lineData.product_name }}x
                                                                    <br />
                                                                    <div class="d-flex gap-1 flex-row"
                                                                        :class="{ 'mb-1': line.lineData.dimension }"
                                                                        v-if="line.lineData.color">
                                                                        <span v-for="color in line.lineData.color"
                                                                            class="badge bg-primary">
                                                                            {{ color.variant.name }}
                                                                            : {{ color.variant_value.name }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="d-flex gap-1 flex-row"
                                                                        v-if="line.lineData.dimension">
                                                                        <span
                                                                            v-for="dimension in line.lineData.dimension"
                                                                            class="badge bg-primary">
                                                                            {{ dimension.variant.name }}
                                                                            :
                                                                            Y: {{ dimension.variant_value.value.height
                                                                            }}x
                                                                            G: {{ dimension.variant_value.value.width
                                                                            }}x
                                                                            U: {{ dimension.variant_value.value.length
                                                                            }}
                                                                        </span>
                                                                    </div>

                                                                </td>
                                                                <td>
                                                                    {{ line.lineData.quantity }}
                                                                </td>
                                                                <td>
                                                                    {{ line.lineData.remaining_quantity }}
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control" required
                                                                        :max="line.lineData.remaining_quantity" min="1"
                                                                        v-model="line.quantity" />
                                                                </td>
                                                                <!-- ekle çıkar -->
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        @click="removeLine(order, line)">
                                                                        Çıkar
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- hidden submit -->

                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2 mt-2">
                                    <button type="submit" class="btn btn-bayi-red">
                                        <i class="fa fa-save"></i> Kaydet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="alert alert-info">
                    <p class="text-center m-0">Seçilen sipariş bulunamadı. Lütfen dahil edilebilir siparişlerden seçim
                        yapınız.</p>
                </div>
            </div>
        </form>
    </div>
</template>


<style scoped></style>