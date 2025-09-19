<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import { ElMessage } from "element-plus";

const showingNavigationDropdown = ref(false);
const isActive = ref(true);
let reloadTimer = null;
let activityTimer = null;
let intervalId = null;

// Hàm bắt đầu reload sau 15s nếu không có thao tác
const startAutoReload = () => {
    clearInterval(reloadTimer); // Xóa timer cũ (nếu có)
    reloadTimer = setInterval(() => {
        if (!isActive.value) {
            if (route().current() !== "histories.by-hour") {
                router.get(route("histories.by-hour"));
            } else {
                router.get(route("histories.by-day"));
            }
        }
    }, 15000);
};

// Khi người dùng thao tác, reset lại timer
const resetActivity = () => {
    isActive.value = true;
    clearTimeout(activityTimer);
    clearInterval(reloadTimer); // Xóa timer reload để tránh bị lặp

    // Nếu người dùng không thao tác trong 1 phút -> đánh dấu là không hoạt động và khởi động lại vòng lặp reload
    activityTimer = setTimeout(() => {
        isActive.value = false;
        startAutoReload(); // Bắt đầu quá trình reload sau 1 phút
    }, 60000); // 1 phút
};

onMounted(() => {
    // Gắn sự kiện để phát hiện thao tác của người dùng
    window.addEventListener("mousemove", resetActivity);
    window.addEventListener("keydown", resetActivity);
    window.addEventListener("scroll", resetActivity);
    window.addEventListener("click", resetActivity);

    // Bắt đầu quá trình tự động reload
    startAutoReload();

    // intervalId = setInterval(() => {
    //     if (route().current() !== "histories.by-hour") {
    //         router.get(route("histories.by-hour"));
    //     } else {
    //         router.get(route("histories.by-day"));
    //     }
    // }, 15000);

    if (Object.keys(usePage().props.errors).length > 0) {
        Object.keys(usePage().props.errors).forEach((key) => {
            ElMessage({
                message: usePage().props.errors[key],
                type: "error",
            });
        });
    }

    // let lastChangPage = localStorage.getItem("last_change_page");
    // if (!lastChangPage) {
    //     localStorage.setItem("last_change_page", Date.now());
    // } else {
    //     if (Date.now() - lastChangPage > 15000) {
    //         localStorage.setItem("last_change_page", Date.now());
    //         if (route().current() !== "histories.by-hour") {
    //             router.get(route("histories.by-hour"));
    //         } else {
    //             router.get(route("histories.by-day"));
    //         }
    //     }
    // }
});

// onUnmounted(() => {
//     clearInterval(intervalId);
// });
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('histories.by-hour')">
                                    <ApplicationLogo
                                        :height="50"
                                        :width="150"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('histories.by-hour')"
                                    :active="
                                        route().current('histories.by-hour')
                                    "
                                >
                                    <span
                                        :class="
                                            route().current('histories.by-hour')
                                                ? 'bg-blue-600 text-white border px-3 py-2 rounded-lg'
                                                : ''
                                        "
                                    >
                                        Hourly Report
                                    </span>
                                </NavLink>
                                <NavLink
                                    :href="route('histories.by-day')"
                                    :active="
                                        route().current('histories.by-day')
                                    "
                                >
                                    <span
                                        :class="
                                            route().current('histories.by-day')
                                                ? 'bg-blue-600 text-white border px-3 py-2 rounded-lg'
                                                : ''
                                        "
                                    >
                                        Daily Report
                                    </span>
                                </NavLink>
                                <NavLink
                                    :href="route('histories.index')"
                                    :active="route().current('histories.index')"
                                >
                                    <span
                                        :class="
                                            route().current('histories.index')
                                                ? 'bg-blue-600 text-white border px-3 py-2 rounded-lg'
                                                : ''
                                        "
                                    >
                                        Data
                                    </span>
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Đăng xuất
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('histories.by-hour')"
                            :active="route().current('histories.by-hour')"
                        >
                            Báo cáo theo giờ
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('histories.by-day')"
                            :active="route().current('histories.by-day')"
                        >
                            Báo cáo theo ngày
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('histories.index')"
                            :active="route().current('histories.index')"
                        >
                            Dữ liệu
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="mx-auto px-4 sm:px-6 lg:px-8 mt-3">
                <slot />
            </main>
        </div>
    </div>
</template>
