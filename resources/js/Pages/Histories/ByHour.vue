<template>
    <Head title="Báo cáo theo giờ" />
    <AuthenticatedLayout>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <el-button type="primary" circle @click="prevDay">
                &#10094;
            </el-button>
            <el-date-picker
                class="mx-2"
                v-model="dateFilter"
                type="date"
                placeholder="Pick a day"
                format="DD/MM/YYYY"
                value-format="YYYY-MM-DD"
                @change="dateChange"
            />
            <el-button type="primary" circle @click="nextDay">
                &#10095;
            </el-button>
        </div>

        <div v-if="isLoading" class="mt-5">
            <el-text size="large" type="primary">Đang tải dữ liệu...</el-text>
        </div>
        <div v-else style="width: 100%">
            <div class="overflow-auto">
                <div
                    class="bg-white p-4 rounded-lg shadow-md mt-4"
                    style="height: calc(100vh - 460px)"
                >
                    <apexchart
                        :options="chartOptions"
                        :series="statistics"
                        type="bar"
                        height="97%"
                    />
                </div>
            </div>
            <div
                v-if="statistics.length"
                class="bg-white p-4 rounded-lg shadow-md mt-4 overflow-auto"
            >
                <table
                    class="table-auto border-collapse border border-gray-300"
                >
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">
                                Xưởng
                            </th>
                            <th
                                v-for="(time, index) in chartOptions.xaxis
                                    .categories"
                                :key="'time-' + index"
                                class="border border-gray-300 px-4 py-2"
                            >
                                {{ time }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(dept, index) in statistics"
                            :key="'data-' + index"
                        >
                            <td
                                class="border border-gray-300 px-4 py-2 font-semibold"
                            >
                                {{ dept.name }}
                            </td>
                            <td
                                v-for="(data, i) in dept.data"
                                :key="'data-' + i"
                                class="border border-gray-300 px-4 py-2 text-center"
                            >
                                {{ data }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { isLoading } from "@/Plugins/loadingState";
import moment from "moment";

const chartWidth = () => {
    if (!props.statistics.length) return 1900;

    return Math.max(props.statistics[0].data.length * 80, 1000);
};
const dateFilter = ref(null);
const props = defineProps({
    statistics: {
        type: Array,
        required: true,
        default() {
            return [];
        },
    },
    date: {
        type: String,
        required: true,
    },
});
const chartOptions = reactive({
    chart: {
        type: "bar",
        toolbar: { show: true },
    },
    legend: {
        fontSize: "26px",
        labels: {
            fontSize: "26px",
        },
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: "70%",
            dataLabels: {
                position: "top",
            },
        },
    },
    dataLabels: {
        enabled: props.statistics.length && props.statistics[0].data.length < 5,
        offsetY: -30,
        style: {
            fontSize: "16px",
            colors: ["#304758"], // Màu chữ
        },
    },
    stroke: {
        show: true,
        width: 2,
        colors: ["transparent"],
    },
    xaxis: {
        categories: [],
        labels: {
            style: {
                fontSize: "24px",
            },
        },
    },
    yaxis: {
        title: {
            text: "kW",
            style: {
                fontSize: "18px",
            },
        },
        labels: {
            style: {
                fontSize: "18px",
            },
        },
    },
    fill: {
        opacity: 1,
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return (val ? val.toLocaleString("en-US") : 0) + " kW";
            },
        },
    },
});
// Hàm tăng ngày
const nextDay = () => {
    dateFilter.value = moment(dateFilter.value)
        .add(1, "day")
        .format("YYYY-MM-DD");
    dateChange();
};

// Hàm giảm ngày
const prevDay = () => {
    dateFilter.value = moment(dateFilter.value)
        .subtract(1, "day")
        .format("YYYY-MM-DD");
    dateChange();
};
const dateChange = () => {
    router.get(route("histories.by-hour"), { date: dateFilter.value });
};
onMounted(() => {
    dateFilter.value = props.date;

    if (props.statistics.length) {
        chartOptions.xaxis.categories = props.statistics[0].hours;
    }
});
</script>
