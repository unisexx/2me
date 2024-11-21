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
            <!-- Floating Button -->
            <button
                class="fixed top-3 right-4 bg-blue-700 text-white p-2 rounded-md shadow-lg hover:bg-blue-600 focus:outline-none z-50"
                @click="toggleAside"
            >
                <span v-if="isCollapsed">เปิดเมนู</span>
                <span v-else>ปิดเมนู</span>
            </button>

            <!-- Aside Menu -->
            <aside
                :class="{
                    '-translate-x-full': isCollapsed,
                    'translate-x-0': !isCollapsed,
                }"
                class="border-r h-screen fixed top-[4rem] transition-transform duration-300 w-64 z-40"
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
                                <template v-else> - {{ menu.label }} </template>
                            </span>
                        </router-link>
                        <hr v-else-if="menu.separator" />
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main
                :class="{
                    'ml-64': !isCollapsed,
                    'ml-0': isCollapsed,
                }"
                class="flex-1 p-6 transition-all duration-300"
            >
                <slot />
            </main>
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
            isCollapsed: false, // ค่าเริ่มต้น: ขยายสำหรับ Desktop
            menus: [
                { to: "/", label: "หน้าแรก", icon: "home" },
                { separator: true },
                {
                    to: "/stickers?category=official",
                    label: "สติกเกอร์ไลน์ทางการ",
                    // icon: "collections",
                },
                {
                    to: "/stickers?category=creator",
                    label: "สติกเกอร์ไลน์ครีเอเตอร์",
                    // icon: "collections",
                },
                { separator: true },
                {
                    to: "/themes?category=official",
                    label: "ธีมไลน์ทางการ",
                    // icon: "brush",
                },
                {
                    to: "/themes?category=creator",
                    label: "ธีมไลน์ครีเอเตอร์",
                    // icon: "brush",
                },
                { separator: true },
                {
                    to: "/emojis?category=official",
                    label: "อิโมจิไลน์ทางการ",
                    // icon: "insert_emoticon",
                },
                {
                    to: "/emojis?category=creator",
                    label: "อิโมจิไลน์ครีเอเตอร์",
                    // icon: "insert_emoticon",
                },
                { separator: true },
                {
                    to: "/series",
                    label: "แนะนำจากทางร้าน",
                    // icon: "star",
                },
            ],
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
            // ปรับสถานะตามขนาดหน้าจอ
            this.isCollapsed = screenWidth < 1280;
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
