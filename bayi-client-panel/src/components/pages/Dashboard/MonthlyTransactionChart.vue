<script setup lang="ts">
import Chart from 'primevue/chart';
import DashboardRequestApi from '/@src/request/request-dashboard';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { generateMonths } from '/@src/utils/chart/chart_utils';
const api = new DashboardRequestApi();
const isLoading = ref(false);

interface IResponse {
    label: string
    dataset: any[]
    backgroundColor: string
    borderColor: string
}

const getData = async () => {
    isLoading.value = true;
    try {
        const { data } = await api.get('last_transactions');
        chartData.value = setChartData(data.data as IResponse[]);
        chartOptions.value = setChartOptions();
    } catch (error) {
        catchFieldError(error);
    } finally {
        isLoading.value = false;
    }
}


onMounted(async () => {
    await getData();

});

const chartData = ref();
const chartOptions = ref();

const setChartData = (data: IResponse[]) => {
    const documentStyle = getComputedStyle(document.documentElement);
    return {
        labels: generateMonths(true),
        /* datasets: [
            {
                label: 'My First dataset',
                backgroundColor: documentStyle.getPropertyValue('--cyan-500'),
                borderColor: documentStyle.getPropertyValue('--cyan-500'),
                data: [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56, 55]
            },
            {
                label: 'My Second dataset',
                backgroundColor: documentStyle.getPropertyValue('--gray-500'),
                borderColor: documentStyle.getPropertyValue('--gray-500'),
                data: [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56, 55]
            }
        ] */
        datasets: data.map((item) => {
            return {
                label: item.label,
                backgroundColor: documentStyle.getPropertyValue(item.backgroundColor),
                borderColor: documentStyle.getPropertyValue(item.borderColor),
                data: item.dataset
            }
        })
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
    const surfaceBorder = documentStyle.getPropertyValue('--surface-border');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.8,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary,
                    font: {
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder,
                    drawBorder: false
                }
            }
        }
    };
}
</script>
<template>
    <div>
        <VLoader :active="isLoading" />
        <Chart type="bar" :data="chartData" :options="chartOptions" style="height:20rem;" />
    </div>
</template>



<style scoped></style>