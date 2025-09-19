<template>
    <Head title="Báo cáo theo ngày" />
    <AuthenticatedLayout>
        <div class="bg-white p-4 rounded-lg shadow-md md:flex">
            <!-- <el-button type="primary" circle @click="prevDay">
                &#10094;
            </el-button> -->
            <div>
                <span class="mr-2">Từ ngày: </span>
                <el-date-picker
                    class="mx-2"
                    v-model="dateFilter.from"
                    type="date"
                    placeholder="Từ ngày"
                    format="DD/MM/YYYY"
                    value-format="YYYY-MM-DD"
                />
            </div>

            <div class="mt-2 md:mt-0 md:ml-2">
                <span class="mr-2">Đến ngày: </span>
                <el-date-picker
                    class="mx-2"
                    v-model="dateFilter.to"
                    type="date"
                    placeholder="Đến ngày"
                    format="DD/MM/YYYY"
                    value-format="YYYY-MM-DD"
                />
                <el-button class="ml-3" type="primary" @click="dateChange">
                    Lọc
                </el-button>
            </div>
        </div>

        <div v-if="isLoading" class="mt-5">
            <el-text size="large" type="primary">Đang tải dữ liệu...</el-text>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div
                class="md:col-span-8 bg-white p-4 rounded-lg shadow-md mt-4 overflow-auto min-h-96"
            >
                <div class="h-full">
                    <apexchart
                        :options="chartOptions"
                        :series="statistics"
                        type="bar"
                        height="93%"
                    />
                </div>
            </div>
            <div
                class="bg-white p-4 rounded-lg shadow-md mt-4 md:col-span-4 min-h-96"
            >
                <div class="flex items-center justify-center">
                    <apexchart
                        type="pie"
                        :options="pieChartOptions"
                        :series="pieChart"
                        width="100%"
                    />
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
            <div
                v-if="statistics.length"
                class="bg-white p-4 rounded-lg shadow-md overflow-auto md:col-span-8 h-full"
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
                        <!-- Thêm hàng Total -->
                        <tr class="bg-blue-100 font-bold">
                            <td class="border border-gray-300 px-4 py-2">
                                Total
                            </td>
                            <td
                                v-for="(total, i) in totalRow"
                                :key="'total-' + i"
                                class="border border-gray-300 px-4 py-2 text-center"
                            >
                                {{ total.toFixed(2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-lg md:col-span-4 h-full">
                <el-text tag="ins" size="large">
                    <strong>STATISTICS:</strong>
                </el-text>
                <div class="ml-4 mt-4" style="font-size: 18px">
                    <div
                        v-for="number in pieChart.length"
                        :key="number"
                        :class="[
                            'flex',
                            'items-center',
                            'py-1 px-2',
                            { 'bg-slate-100': number % 2 === 0 },
                        ]"
                    >
                        <span class="font-bold block" style="width: 100px">
                            {{ pieChartOptions.labels[number - 1] }}
                        </span>
                        <span style="width: 130px"
                            >{{ pieChart[number - 1] }} <small>kW</small></span
                        >
                        <span
                            >{{ getCost(pieChartOptions.labels[number - 1]) }}
                            <small>VNĐ</small></span
                        >
                    </div>
                    <div class="text-right mt-2 text-xl">
                        <span
                            style="background-color: #ccf9ff"
                            class="py-1 px-2 text-red-500 font-semibold"
                        >
                            <strong>Total: </strong>
                            {{
                                pieChart
                                    .reduce((acc, curr) => acc + curr, 0)
                                    .toFixed(2)
                            }}
                            <small>kW</small> =
                            {{
                                costs
                                    .reduce((acc, curr) => acc + curr.cost, 0)
                                    .toLocaleString("en-US")
                            }}
                            <small>VNĐ</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, onMounted, reactive, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { isLoading } from "@/Plugins/loadingState";
import moment from "moment";

const dateFilter = reactive({
    from: null,
    to: null,
});
const props = defineProps({
    statistics: {
        type: Array,
        required: true,
        default() {
            return [];
        },
    },
    dateFrom: {
        type: String,
        required: true,
    },
    dateTo: {
        type: String,
        required: true,
    },
    costMonth: {
        type: String,
        required: true,
    },
    days: {
        type: Array,
        required: true,
        default() {
            return [];
        },
    },
    costs: {
        type: Array,
        required: true,
        default() {
            return [];
        },
    },
});
const getCost = (name) => {
    const cost = props.costs.find((cost) => cost.name === name);
    return cost ? cost.cost.toLocaleString("en-US") : 0;
};
const chartWidth = () => {
    if (!props.statistics.length) return 1900;

    if (props.statistics.length === 2) {
        return Math.max(props.statistics[0].data.length * 130, 1000);
    }

    if (props.statistics.length === 3) {
        return Math.max(props.statistics[0].data.length * 200, 1000);
    }

    if (props.statistics.length === 4) {
        return Math.max(props.statistics[0].data.length * 250, 1000);
    }

    return Math.max(props.statistics[0].data.length * 350, 1000);
};
const totalRow = computed(() => {
    if (!props.statistics.length) return [];

    // Tìm mảng data dài nhất trong statistics
    const maxLength = Math.max(
        ...props.statistics.map((dept) => dept.data.length),
        0
    );

    // Tạo mảng tổng theo độ dài lớn nhất
    return Array.from({ length: maxLength }, (_, colIndex) =>
        props.statistics.reduce(
            (sum, dept) => sum + (dept.data[colIndex] || 0),
            0
        )
    );
});
const chartOptions = reactive({
    chart: {
        type: "bar",
        height: 500,
    },
    legend: {
        fontSize: "18px",
        labels: {
            fontSize: "26px",
        },
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: "80%",
            // borderRadius: 5,
            // borderRadiusApplication: "end",
            dataLabels: {
                position: "top",
            },
        },
    },
    dataLabels: {
        enabled: true,
        offsetY: -20,
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
                fontSize: "18px",
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
const pieChart = ref([]);
const pieChartOptions = reactive({
    chart: {
        width: 480,
        type: "pie",
    },
    labels: [],
    plotOptions: {
        pie: {
            dataLabels: {
                offset: -35,
            },
        },
    },
    dataLabels: {
        enabled: true,
        formatter: (val, opts) => {
            let value = opts.w.config.series[opts.seriesIndex];
            return [`${value.toFixed(2)} kW`, `(${val.toFixed(2)}%)`];
        },
        style: {
            fontSize: "16px",
            fontWeight: "bold",
            colors: ["#fff"], // Màu chữ
            textAnchor: "middle",
        },
    },
    responsive: [
        {
            breakpoint: 960,
            options: {
                chart: {
                    width: 380,
                },
                legend: {
                    position: "bottom",
                    style: {
                        fontSize: "16px",
                    },
                },
            },
        },
        {
            breakpoint: 1920,
            options: {
                chart: {
                    width: 520,
                },
                legend: {
                    position: "bottom",
                    style: {
                        fontSize: "16px",
                    },
                },
            },
        },
        {
            breakpoint: 2500,
            options: {
                chart: {
                    width: 600,
                },
                legend: {
                    position: "bottom",
                    fontSize: "18px",
                },
            },
        },
    ],
});
const dateChange = () => {
    router.get(route("histories.by-day"), {
        date_from: dateFilter.from,
        date_to: dateFilter.to,
    });
};
onMounted(() => {
    dateFilter.from = props.dateFrom;
    dateFilter.to = props.dateTo;
    pieChartOptions.labels = props.statistics.map((stat) => stat.name);
    if (props.statistics.length) {
        chartOptions.xaxis.categories = props.statistics[0].days;
    }

    pieChart.value = props.statistics.map((stat) =>
        stat.data
            .reduce((acc, curr) => parseFloat(acc) + parseFloat(curr), 0)
            .toFixed(2)
    );
    pieChart.value = pieChart.value.map((value) => parseFloat(value));
});
</script>
