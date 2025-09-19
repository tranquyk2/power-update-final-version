<template>
    <Head title="Dữ liệu" />
    <authenticated-layout>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <span class="mr-2">Xưởng: </span>
            <el-select
                v-model="slaveId"
                multiple
                placeholder="Chọn xưởng"
                style="width: 150px"
                clearable
                collapse-tags
            >
                <el-option
                    v-for="item in departments"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                    :disabled="!departmentAvailable.includes(item.id)"
                />
            </el-select>

            <span class="mx-2">Từ ngày: </span>
            <el-date-picker
                class="mx-2"
                v-model="dateFilter.from"
                type="date"
                placeholder="Từ ngày"
                format="DD/MM/YYYY"
                value-format="YYYY-MM-DD"
            />

            <span class="mx-2">Đến ngày: </span>
            <el-date-picker
                class="mx-2"
                v-model="dateFilter.to"
                type="date"
                placeholder="Đến ngày"
                format="DD/MM/YYYY"
                value-format="YYYY-MM-DD"
            />
            <el-button class="ml-3" type="primary" @click="filterData">
                Lọc
            </el-button>
        </div>

        <div v-if="isLoading" class="mt-5">
            <el-text size="large" type="primary">Đang tải dữ liệu...</el-text>
        </div>

        <div v-else class="bg-white p-4 rounded-lg shadow-md my-4">
            <div
                class="relative overflow-y-auto overscroll-none rounded-lg"
                style="height: calc(100vh - 240px)"
            >
                <table
                    class="w-full text-sm text-left rtl:text-right text-blue-100"
                >
                    <thead
                        class="text-xs text-white uppercase bg-blue-600 dark:text-white sticky top-0"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3">Xưởng</th>
                            <th scope="col" class="px-6 py-3">KW</th>
                            <th scope="col" class="px-6 py-3">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(history, index) in histories.data"
                            :key="'data-' + index"
                            class="border-b border-blue-400 text-gray-900"
                        >
                            <th
                                scope="row"
                                class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap"
                            >
                                {{ getDepartmentName(history.slave_id) }}
                            </th>
                            <td class="px-6 py-2">{{ history.kw }}</td>
                            <td class="px-6 py-2">
                                {{
                                    moment(history.datetime).format(
                                        "HH:mm DD-MM-YYYY"
                                    )
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex">
                <el-button
                    type="primary"
                    plain
                    class="mr-4"
                    @click="exportData"
                >
                    Xuất Excel
                </el-button>
                <el-pagination
                    background
                    layout="prev, pager, next, sizes"
                    :total="histories.total"
                    v-model:page-size="pageSize"
                    v-model:current-page="currentPage"
                    :page-sizes="[30, 50, 100, 200, 300, 500]"
                    @change="pageChange"
                />
            </div>
        </div>
    </authenticated-layout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { isLoading } from "@/Plugins/loadingState";
import moment from "moment";
import { router, Head } from "@inertiajs/vue3";
import { computed, onMounted, reactive, ref } from "vue";

const dateFilter = reactive({
    from: null,
    to: null,
});
const departmentAvailable = computed(() => {
    return props.departments
        .filter((dept) => dept.enabled)
        .map((dept) => dept.id);
});
const slaveId = ref([]);
const pageSize = ref(30);
const currentPage = ref(1);
const props = defineProps({
    histories: {
        type: Object,
        required: true,
        default() {
            return {};
        },
    },
    filter: {
        type: Object,
        default() {
            return {};
        },
    },
    dateFrom: {
        type: String,
    },
    dateTo: {
        type: String,
    },
    departments: {
        type: Array,
        required: true,
        default() {
            return [];
        },
    },
});
const getDepartmentName = (id) => {
    const department = props.departments.find((dept) => dept.id === id);
    return department ? department.name : "";
};
const pageChange = () => {
    filterData();
};
const getParams = () => {
    const params = {
        filter: {},
        dateFrom: dateFilter.from,
        dateTo: dateFilter.to,
        page: currentPage.value,
        size: pageSize.value,
    };

    if (dateFilter.from && dateFilter.to) {
        params.filter.datetime = {
            field: "datetime",
            type: "date",
            operator: "between",
            from: dateFilter.from,
            to: dateFilter.to,
        };
    }

    if (slaveId.value) {
        params.filter.slave_id = {
            field: "slave_id",
            type: "in",
            value: slaveId.value,
        };
    }

    return params;
};
const filterData = () => {
    router.get(route("histories.index"), getParams());
};
const exportData = () => {
    window.open(route("histories.export", getParams()));
};

onMounted(() => {
    pageSize.value = props.histories.per_page;
    currentPage.value = props.histories.current_page;

    if (props.filter && props.filter.slave_id && props.filter.slave_id.value) {
        slaveId.value = props.filter.slave_id.value.map((id) => parseInt(id));
    }

    if (
        props.filter &&
        props.filter.datetime &&
        props.filter.datetime.from &&
        props.filter.datetime.to
    ) {
        dateFilter.from = props.filter.datetime.from;
        dateFilter.to = props.filter.datetime.to;
    }
});
</script>
