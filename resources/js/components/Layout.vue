<template>
    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <nav class="bg-white border-b fixed top-0 left-0 w-full z-50">
            <div class="mx-auto px-4 flex items-center justify-between py-4">
                <router-link to="/" class="text-2xl font-bold text-gray-800">
                    Line2me
                </router-link>
            </div>
        </nav>

        <!-- Main Layout -->
        <div class="flex flex-1 mt-[4rem] relative">
            <!-- Mobile Layout -->
            <div class="lg:hidden">
                <!-- Floating Button -->
                <button
                    class="fixed top-3 right-4 bg-blue-700 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 focus:outline-none flex items-center justify-center z-50"
                    @click="toggleAside"
                >
                    <span v-if="isCollapsed" class="material-icons text-2xl">
                        menu
                        <!-- ไอคอนเปิดเมนู -->
                    </span>
                    <span v-else class="material-icons text-2xl">
                        close
                        <!-- ไอคอนปิดเมนู -->
                    </span>
                </button>

                <!-- Aside Menu -->
                <aside
                    :class="{
                        '-translate-x-full': isCollapsed,
                        'translate-x-0': !isCollapsed,
                    }"
                    class="border-r h-screen fixed top-[4rem] transition-transform duration-300 w-64 z-40 bg-white"
                >
                    <!-- เมนูรายการ -->
                    <ul class="p-4">
                        <li v-for="menu in menus" :key="menu.to">
                            <router-link
                                v-if="menu.to"
                                :to="menu.to"
                                class="hover:bg-gray-500 hover:bg-opacity-10 hover:text-blue-600 flex items-center text-gray-700 py-1.5 px-4 rounded space-x-2 cursor-pointer"
                            >
                                <span v-if="menu.icon" class="material-icons">{{
                                    menu.icon
                                }}</span>
                                <span class="ml-2">
                                    <template v-if="menu.label === 'หน้าแรก'">
                                        {{ menu.label }}
                                    </template>
                                    <template v-else>
                                        - {{ menu.label }}
                                    </template>
                                </span>
                            </router-link>
                            <hr v-else-if="menu.separator" />
                        </li>
                    </ul>
                </aside>
                <!-- Main Content -->
                <main class="flex-1 p-3 transition-all duration-300">
                    <slot />
                </main>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden lg:flex">
                <!-- Aside Menu -->
                <aside
                    class="border-r h-screen w-64 bg-white fixed top-[4rem] left-0 z-40"
                >
                    <ul class="p-4">
                        <li v-for="menu in menus" :key="menu.to">
                            <router-link
                                v-if="menu.to"
                                :to="menu.to"
                                class="hover:bg-gray-500 hover:bg-opacity-10 hover:text-blue-600 flex items-center text-gray-700 py-1.5 px-4 rounded space-x-2 cursor-pointer"
                            >
                                <span v-if="menu.icon" class="material-icons">
                                    {{ menu.icon }}
                                </span>
                                <span class="ml-2">
                                    <template v-if="menu.label === 'หน้าแรก'">
                                        {{ menu.label }}
                                    </template>
                                    <template v-else>
                                        - {{ menu.label }}
                                    </template>
                                </span>
                            </router-link>
                            <hr v-else-if="menu.separator" />
                        </li>
                    </ul>
                </aside>
                <!-- Main Content -->
                <main class="flex-1 p-3 ml-64">
                    <slot />
                </main>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-4">
            <div class="container mx-auto text-center">
                <p>&copy; 2024 Line2me Stickers Shop. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: "Layout",
    data() {
        return {
            isCollapsed: true, // ค่าเริ่มต้น: ซ่อนเมนูใน Mobile Layout
            menus: [
                { to: "/", label: "หน้าแรก", icon: "home" },
                { separator: true },
                {
                    to: "/stickers?category=official",
                    label: "สติกเกอร์ไลน์ทางการ",
                },
                {
                    to: "/stickers?category=creator",
                    label: "สติกเกอร์ไลน์ครีเอเตอร์",
                },
                { separator: true },
                {
                    to: "/themes?category=official",
                    label: "ธีมไลน์ทางการ",
                },
                {
                    to: "/themes?category=creator",
                    label: "ธีมไลน์ครีเอเตอร์",
                },
                { separator: true },
                {
                    to: "/emojis?category=official",
                    label: "อิโมจิไลน์ทางการ",
                },
                {
                    to: "/emojis?category=creator",
                    label: "อิโมจิไลน์ครีเอเตอร์",
                },
                { separator: true },
                {
                    to: "/series",
                    label: "แนะนำจากทางร้าน",
                },
            ],
            screenIsLarge: window.innerWidth >= 1024,
        };
    },
    mounted() {
        this.setDefaultAsideState();
        window.addEventListener("resize", this.setDefaultAsideState);
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.setDefaultAsideState);
    },
    methods: {
        toggleAside() {
            this.isCollapsed = !this.isCollapsed;
        },
        setDefaultAsideState() {
            const screenWidth = window.innerWidth;
            this.screenIsLarge = screenWidth >= 1024;
            this.isCollapsed = !this.screenIsLarge; // ซ่อนเมนูหากเป็น Mobile Layout
        },
    },
};
</script>

<style>
/* ใช้ Material Icons */
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
</style>

<style>
/** ธงชาติ */
@import "@/css/freakflags/freakflags.css";
</style>
