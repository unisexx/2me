<template>
    <Layout>
        <div class="container mx-auto">
            <div v-if="loading" class="text-center text-gray-500">
                Loading...
            </div>

            <div v-else class="flex flex-wrap">
                <!-- Left Column -->
                <div class="w-full lg:w-9/12 xl:w-8/12 mx-auto">
                    <!-- Breadcrumb -->
                    <nav
                        v-if="!loading && theme"
                        aria-label="breadcrumb"
                        class="mb-4 bg-gray-100 rounded-lg p-4 flex items-center"
                    >
                        <ol
                            class="breadcrumb flex flex-wrap items-center text-sm text-gray-500"
                        >
                            <!-- Section 1: หน้าแรก -->
                            <li class="breadcrumb-item flex items-center">
                                <a
                                    href="/"
                                    class="flex items-center text-gray-500 hover:text-blue-600"
                                >
                                    <span class="material-icons mr-1"
                                        >home</span
                                    >
                                    <!-- Icon -->
                                    หน้าแรก
                                </a>
                                <span class="mx-2">/</span>
                                <!-- Separator -->
                            </li>

                            <!-- Section 2: หมวดหมู่ -->
                            <li class="breadcrumb-item flex items-center">
                                <a
                                    :href="getCategoryLink()"
                                    class="text-gray-500 hover:text-blue-600"
                                >
                                    {{ getCategoryText() }}
                                </a>
                                <span class="mx-2">/</span>
                                <!-- Separator -->
                            </li>

                            <!-- Section 3: ประเทศ -->
                            <li class="breadcrumb-item flex items-center">
                                <a
                                    :href="getCountryLink()"
                                    class="text-gray-500 hover:text-blue-600"
                                >
                                    {{ getCountryText() }}
                                </a>
                                <span class="mx-2">/</span>
                                <!-- Separator -->
                            </li>

                            <!-- Section 4: ชื่อสติกเกอร์ -->
                            <li class="breadcrumb-item active text-gray-700">
                                {{ theme.title }}
                            </li>
                        </ol>
                    </nav>

                    <!-- Section: ข้อมูลธีม -->
                    <div class="flex items-start">
                        <!-- Left Image Column -->
                        <div class="relative mr-3">
                            <img
                                :src="theme.img_url"
                                :alt="theme.title"
                                class="w-32 sm:w-48 lg:w-56 xl:w-64 h-auto"
                            />
                            <span
                                v-if="theme.is_new"
                                class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-1 rounded custom-font-size"
                            >
                                NEW
                            </span>
                        </div>

                        <!-- Right Content Column -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-4">
                                {{ theme.title }}
                            </h1>
                            <p class="hidden md:block text-gray-600 mb-2">
                                {{ theme.detail }}
                            </p>
                            <p class="mb-2">
                                <span class="font-bold">รหัสสินค้า:</span>
                                {{ theme.id }}
                            </p>
                            <p class="mb-2">
                                <span class="font-bold">ประเทศ:</span>
                                <span
                                    class="fflag ff-sm ml-1 mb-1"
                                    :class="
                                        'fflag-' + theme.country.toUpperCase()
                                    "
                                    :title="theme.country"
                                ></span>
                                {{ theme.country }}
                            </p>
                            <p class="text-lg mb-2 font-bold">
                                <span>Price: </span>
                                <span class="text-red-500">{{
                                    theme.price
                                }}</span>
                                <span>THB</span>
                            </p>
                            <a
                                :href="`https://line.me/ti/p/~ratasak1234`"
                                target="_blank"
                                class="inline-block bg-blue-700 text-white text-center px-6 py-3 rounded-full hover:bg-blue-600 w-full hidden md:block"
                            >
                                สั่งซื้อชุดนี้แอดไลน์ไอดี ratasak1234
                            </a>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a
                            :href="`https://line.me/ti/p/~ratasak1234`"
                            target="_blank"
                            class="inline-block bg-blue-700 text-white text-center px-6 py-3 rounded-full hover:bg-blue-600 w-full block md:hidden"
                        >
                            สั่งซื้อชุดนี้แอดไลน์ไอดี ratasak1234
                        </a>
                    </div>

                    <hr class="my-4 border-t border-gray-300" />

                    <!-- Section: ตัวอย่างธีม -->
                    <div class="mt-8">
                        <h2 class="text-2xl font-semibold mb-4">ตัวอย่างธีม</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                v-for="x in [2, 3, 4, 5]"
                                :key="x"
                                class="text-center overflow-hidden"
                            >
                                <a
                                    class="venobox"
                                    data-gall="gallery"
                                    :href="
                                        generateThemeUrlDetail(
                                            theme.theme_code,
                                            x,
                                            theme.section
                                        )
                                    "
                                >
                                    <img
                                        class="w-full h-auto rounded"
                                        :src="
                                            generateThemeUrlDetail(
                                                theme.theme_code,
                                                x,
                                                theme.section
                                            )
                                        "
                                        :alt="`ธีมไลน์ ${theme.title}`"
                                    />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div
                    class="w-full lg:w-3/12 xl:w-4/12 border-l border-gray-200"
                >
                    <!-- เนื้อหาคอลัมน์ขวา -->
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from "../components/Layout.vue";
import axios from "axios";

export default {
    components: {
        Layout,
    },
    data() {
        return {
            theme: null,
            loading: true,
        };
    },
    methods: {
        async fetchThemeData() {
            const themeId = this.$route.params.id;
            try {
                const response = await axios.get(`/api/theme-view/${themeId}`);
                this.theme = response.data;
            } catch (error) {
                console.error("Error loading theme:", error);
            } finally {
                this.loading = false;
            }
        },
        generateThemeUrlDetail(theme_code, imgOrder, section = 1) {
            const baseUrl = "https://shop.line-scdn.net/themeshop/v1/products/";
            const formattedUrl =
                baseUrl +
                theme_code.substring(0, 2) +
                "/" +
                theme_code.substring(2, 4) +
                "/" +
                theme_code.substring(4, 6) +
                "/" +
                theme_code +
                "/" +
                section +
                "/ANDROID/th/preview_00" +
                imgOrder +
                "_720x1232.png";

            return formattedUrl;
        },
        getCategoryText() {
            if (!this.theme) return "";
            if (this.theme.category === "official") {
                return "ธีมทางการ";
            } else if (this.theme.category === "creator") {
                return "ครีเอเตอร์ธีม";
            }
            return "หมวดหมู่ไม่ทราบ";
        },
        getCategoryLink() {
            if (!this.theme) return "#";
            return `/themes?category=${this.theme.category}`;
        },
        getCountryText() {
            if (!this.theme) return "";
            const countryMap = {
                th: "ประเทศไทย",
                jp: "ประเทศญี่ปุ่น",
                tw: "ประเทศไต้หวัน",
                id: "ประเทศอินโดนีเซีย",
            };
            return countryMap[this.theme.country] || "ประเทศไม่ทราบ";
        },
        getCountryLink() {
            if (!this.theme) return "#";
            return `/themes?country=${this.theme.country}`;
        },
    },
    async mounted() {
        await this.fetchThemeData();
    },
};
</script>

<style scoped>
/* คุณสามารถเพิ่มสไตล์เพิ่มเติมได้ที่นี่ */
h1 {
    word-break: break-word; /* จัดการคำที่ยาวติดกัน */
    overflow-wrap: anywhere; /* ขึ้นบรรทัดใหม่ได้ทุกที่ */
}
</style>
