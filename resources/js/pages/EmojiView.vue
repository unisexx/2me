<template>
    <Layout>
        <div class="container mx-auto">
            <div v-if="loading" class="text-center text-gray-500">
                Loading...
            </div>

            <div v-else class="flex flex-wrap">
                <!-- Left Column -->
                <div class="w-full lg:w-8/12 mx-auto pe-5">
                    <!-- Breadcrumb -->
                    <nav
                        v-if="!loading && emoji"
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
                                {{ emoji.title }}
                            </li>
                        </ol>
                    </nav>

                    <!-- Section: ข้อมูลอิโมจิ -->
                    <div class="flex items-start">
                        <!-- Left Image Column -->
                        <div class="relative mr-3">
                            <img
                                :src="`https://stickershop.line-scdn.net/sticonshop/v1/product/${emoji.emoji_code}/iphone/main.png`"
                                :alt="emoji.title"
                                class="w-32 sm:w-48 lg:w-60 h-auto"
                            />
                        </div>

                        <!-- Right Content Column -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-4">
                                {{ emoji.title }}
                            </h1>
                            <div class="block mb-2">
                                <p class="text-gray-700">
                                    {{ emoji.detail }}
                                </p>
                            </div>
                            <p class="mb-2">
                                <span class="font-bold">รหัสสินค้า:</span>
                                {{ emoji.id }}
                            </p>
                            <p class="mb-2">
                                <span class="font-bold">ประเทศ:</span>
                                <span
                                    class="fflag ff-sm ml-1 mb-1"
                                    :class="
                                        'fflag-' + emoji.country.toUpperCase()
                                    "
                                    :title="emoji.country"
                                ></span>
                                {{ emoji.country }}
                            </p>
                            <p class="text-lg mb-2 font-bold">
                                <span>Price: </span>
                                <span class="text-red-500">{{
                                    emoji.price
                                }}</span>
                                <span>THB</span>
                            </p>
                            <p class="text-gray-600 mb-2">
                                {{ emoji.detail }}
                            </p>
                            <a
                                :href="`https://line.me/ti/p/~ratasak1234`"
                                target="_blank"
                                class="inline-block bg-blue-700 text-white text-center px-6 py-3 rounded-full hover:bg-blue-600 w-full"
                            >
                                สั่งซื้อชุดนี้แอดไลน์ไอดี ratasak1234
                            </a>
                        </div>
                    </div>

                    <hr class="my-4 border-t border-gray-300" />

                    <!-- Section: ตัวอย่างอิโมจิ -->
                    <div class="mt-8">
                        <h2 class="text-2xl font-semibold mb-4">
                            ตัวอย่างอิโมจิ
                        </h2>
                        <div class="grid grid-cols-8 gap-4">
                            <div
                                v-for="x in 50"
                                :key="x"
                                class="text-center overflow-hidden"
                            >
                                <a
                                    class="venobox"
                                    data-gall="gallery"
                                    :href="
                                        generateEmojiImageUrl(
                                            emoji.emoji_code,
                                            x
                                        )
                                    "
                                >
                                    <img
                                        class="w-full h-auto rounded"
                                        :src="
                                            generateEmojiImageUrl(
                                                emoji.emoji_code,
                                                x
                                            )
                                        "
                                        :alt="`อิโมจิไลน์ ${emoji.title}`"
                                        @error="hideImage($event)"
                                    />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="w-full lg:w-4/12 border-l border-gray-200">
                    <!-- เนื้อหาสำหรับคอลัมน์ขวา -->
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
            emoji: null,
            loading: true,
        };
    },
    methods: {
        async fetchemojiData() {
            const emojiId = this.$route.params.id;
            try {
                const response = await axios.get(`/api/emoji-view/${emojiId}`);
                this.emoji = response.data;
            } catch (error) {
                console.error("Error loading emoji:", error);
            } finally {
                this.loading = false;
            }
        },
        generateEmojiImageUrl(emojiCode, imgOrder) {
            const baseUrl =
                "https://stickershop.line-scdn.net/sticonshop/v1/sticon";
            return `${baseUrl}/${emojiCode}/iphone/${String(imgOrder).padStart(
                3,
                "0"
            )}.png`;
        },
        hideImage(event) {
            event.target.style.display = "none"; // ซ่อนรูปภาพเมื่อเกิดข้อผิดพลาดในการโหลด
        },
        getCategoryText() {
            if (!this.emoji) return "";
            if (this.emoji.category === "official") {
                return "อิโมจิทางการ";
            } else if (this.emoji.category === "creator") {
                return "ครีเอเตอร์อิโมจิ";
            }
            return "หมวดหมู่ไม่ทราบ";
        },
        getCategoryLink() {
            if (!this.emoji) return "#";
            return `/emojis?category=${this.emoji.category}`;
        },
        getCountryText() {
            if (!this.emoji) return "";
            const countryMap = {
                th: "ประเทศไทย",
                jp: "ประเทศญี่ปุ่น",
                tw: "ประเทศไต้หวัน",
                id: "ประเทศอินโดนีเซีย",
            };
            return countryMap[this.emoji.country] || "ประเทศไม่ทราบ";
        },
        getCountryLink() {
            if (!this.emoji) return "#";
            return `/emojis?country=${this.emoji.country}`;
        },
    },
    async mounted() {
        await this.fetchemojiData();
    },
};
</script>

<style scoped>
/* คุณสามารถเพิ่มสไตล์เพิ่มเติมได้ที่นี่ */
</style>
