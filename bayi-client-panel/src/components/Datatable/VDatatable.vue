<template>
    <div :class="wrapperClass" :style="wrapperStyle" :id="`vueTableContent`">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0 float-left">
                            {{ cardTitle }}
                            <i class="fa fa-sync cursor-pointer d-none" :class="{ spinner: loading }"
                                @click="inComponentRefresh"></i>
                        </h3>
                        <div class="float-right">
                            <label class="mr-1">Veri limiti:</label>
                            <select @change="changeLimit($event)" id="vTable-length-changer">
                                <option v-for="(item, index) in avaibleLimits" :key="index" :value="item">{{ item }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <VTable :current-data="currentData" :table-heads="tableHeads" @triggeredevent="emitTriggered" />
                    </div>
                    <div class="card-footer py-4">
                        <div class="row">
                            <div class="col">
                                <span>{{ showingDataText }}</span>
                            </div>
                            <div class="col">
                                <VPagination :total-pages="totalPage" :total="totalData" :per-page="limit"
                                    :current-page="currentPage" @pagechanged="onPageChange" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="loading" class="card-overlay">
            <i style="font-size: 3rem; color: #18a4d1" class="icon-spinner9 spinner mr-2"></i>
        </div>
    </div>
</template>

<script setup lang="ts">
//@ts-nocheck

//todo get method pagination not sending with get query
import { useApi } from "/@src/composables/useApi";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
import { IDataTableColumn } from '/@src/components/Datatable/IDatatable'
import { AxiosRequestConfig } from "axios";
import { uuid } from "vue-uuid";
import { useSwal } from "/@src/composables/useSwal";

const api = useApi();

const swal = useSwal();
type MethodTypes = "get" | "post" | "put" | "delete";
const emit = defineEmits(["tableRendered", "datachanged", "eventTriggered", "extraDataChanged"]);
const props = defineProps({
    filter: Object,
    requestURL: String,
    method: {
        default: 'get',
        type: String as PropType<MethodTypes>
    },
    refresh: Boolean,
    dataContent: {
        type: Array as PropType<IDataTableColumn[]>,
        default: () => []
    },
    headClass: String,
    headStyle: String,
    wrapperClass: String,
    wrapperStyle: String,
    cardTitle: String,
    itemId: {
        type: String,
        default: "id"
    },
});

const currentData = ref([]);
const serializedExtract = ref<{ [key: string]: any }>({});
const extraData = ref();
const currentPage = ref(1);
const limit = ref(10);
const totalPage = ref(0);
const renderedData = computed(() => currentData.value.length);
const avaibleLimits = ref([10, 20, 50, 100]);
const totalData = ref(0);
const showingDataText = ref("");
const loading = ref(false);

//methods

const fireDataChanged = () => {
    emit("datachanged", currentData.value);
    emit("extraDataChanged", extraData.value);

};
const changeLimit = (event: any) => {
    limit.value = Number(event.target.value);
    currentPage.value = 1;
    getData();
};
const onPageChange = (page: number) => {
    currentPage.value = page;
    getData();
};
const serializeExtractedData = () => {
    if (props.dataContent.length > 0) {
        props.dataContent.forEach((element, index) => {
            serializedExtract.value[element.data] = index + 1;
        });
    }
};
const getData = async () => {
    loading.value = true;
    let dt_axios_params: AxiosRequestConfig = {
        method: props.method,
        url: props.requestURL,
    };
    let dt_limit_data = { limit: limit.value, current_page: currentPage.value, };

    if (props.method == "get") {
        dt_axios_params.params = dt_limit_data;
        if (props.filter) dt_axios_params.params = { ...dt_limit_data, ...props.filter };
    }
    else {
        dt_axios_params.data = { ...dt_limit_data, external_filter: props.filter };
    }
    await api(dt_axios_params).then((result) => {
        let response = result.data;
        var tempRow: any = [];
        serializeExtractedData();
        response.data.forEach((ax: { [x: string]: any; }) => {
            var tempColumn: { id: string, name: string; value: any; displayData: any; }[] = [];
            Object.keys(ax).forEach((key) => {
                if (serializedExtract.value[key]) {
                    let tmpColumnData = {
                        id: uuid.v4(),
                        name: key,
                        value: ax[key],
                        displayData: ax[key]
                    };
                    try {
                        if (typeof props.dataContent[serializedExtract.value[key] - 1].render !== "undefined") {
                            let tmpRenderedCellValue = props.dataContent[serializedExtract.value[key] - 1].render(ax, ax[key]);
                            if (typeof tmpRenderedCellValue === "string") tmpColumnData.displayData = tmpRenderedCellValue;
                            else tmpColumnData = { ...tmpColumnData, ...props.dataContent[serializedExtract.value[key] - 1].render(ax, ax[key], tempRow.length) };
                        }
                    } catch (error) {
                        console.log(error);
                    }

                    tempColumn[serializedExtract.value[key] - 1] = tmpColumnData;


                }
                if (props.itemId && key == props.itemId) tempColumn[props.itemId] = ax[props.itemId];
            });
            tempRow.push(tempColumn);
        });
        currentData.value = tempRow;
        extraData.value = response?.extra || undefined;
        totalData.value = Number(response.totalData);
        totalPage.value = Math.ceil(totalData.value / limit.value);
        showingDataText.value = getFilteredDataText.value;
        fireDataChanged();
        loading.value = false;
    }).catch((err) => {
        catchFieldError(err);
        currentData.value = [];
        totalData.value = 0;
        totalPage.value = 0;
        showingDataText.value = getFilteredDataText.value;
        loading.value = false;
        fireDataChanged();
    });
};

const emitTriggered = (args: any) => {
    emit("eventTriggered", args)
}

const tableHeads = computed(() => props.dataContent.map((e) => e.headText));

const getFilteredDataText = computed(() => {
    let startingVal = !renderedData.value ? 0 : (currentPage.value - 1) * limit.value + 1;
    let endingVal = !renderedData.value ? 0 : Math.min(currentPage.value * limit.value, totalData.value);
    return `Toplam ${totalData.value} veriden ${startingVal} - ${endingVal} arası gösteriliyor.`;
});
watch(() => props.refresh, (newValue, oldValue) => {
    if (newValue === true) {
        currentPage.value = 1;
        getData();
    }
});

const inComponentRefresh = () => {
    currentPage.value = 1, getData();
}

onMounted(() => {
    if (props.requestURL) getData();
});
</script>
<style scoped>
@import "./datatable.css";
</style>
