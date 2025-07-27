<script setup lang="ts">
import Chart from 'primevue/chart';
import { IPieChartBestSellingCategory } from '../PieChart.vue';
const props = defineProps({
    data: {
        type: Array as PropType<IPieChartBestSellingCategory[]>,
        required: true
    }
})

onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});

const chartData = ref();
const chartOptions = ref();



const setChartData = () => {
    if (!props.data) return;
    const documentStyle = getComputedStyle(document.body);

    const createBgClassFor5 = () => {
        //generate class for 5 
        const classes = ['--cyan-500', '--orange-500', '--pink-500', '--purple-500', '--indigo-500', '--green-500', '--yellow-500', '--red-500', '--cyan-500', '--orange-500', '--gray-500', '--pink-500', '--purple-500', '--indigo-500', '--green-500', '--yellow-500', '--red-500'];
        return classes.slice(0, props.data.length).map((item, index) => {
            return documentStyle.getPropertyValue(item);
        });
    };

    const createHoverBgClassFor5 = () => {
        //generate class for 5
        const classes = ['--cyan-400', '--orange-400', '--pink-400', '--purple-400', '--indigo-400', '--green-400', '--yellow-400', '--red-400', '--cyan-400', '--orange-400', '--gray-400', '--pink-400', '--purple-400', '--indigo-400', '--green-400', '--yellow-400', '--red-400'];
        return classes.slice(0, props.data.length).map((item, index) => {
            return documentStyle.getPropertyValue(item);
        });
    };

    return {
        labels: props.data.map(item => item.category_name),

        datasets: [
            {
                data: props.data.map(item => item.total),
                backgroundColor: createBgClassFor5(),
                hoverBackgroundColor: createHoverBgClassFor5()
            }
        ]

    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');

    return {
        plugins: {
            legend: {
                labels: {
                    cutout: '60%',
                    color: textColor
                }
            }
        }
    };
};
</script>
<template>
    <div class="card flex justify-content-center">
        <Chart type="doughnut" :data="chartData" :options="chartOptions" class="w-full md:w-30rem" />
    </div>
</template>


<style scoped></style>