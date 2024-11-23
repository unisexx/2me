<template>
    <Layout>
        <div class="container mx-auto">
            <div v-if="!emoji" class="text-center text-gray-500">
                Loading...
            </div>

            <div v-else class="flex flex-wrap">
                <!-- Left Column -->
                <div class="w-full lg:w-9/12 xl:w-8/12 mx-auto">
                    <!-- Breadcrumb -->
                    <nav
                        v-if="emoji"
                        aria-label="breadcrumb"
                        class="mb-4 bg-gray-100 rounded-lg p-4 flex items-center"
                    >
                        <ol
                            class="breadcrumb flex flex-wrap items-center text-sm text-gray-500"
                        >
                            <!-- Section 1: หน้าแรก -->
                            <li class="breadcrumb-item flex items-center">
                                <NuxtLink
                                    to="/"
                                    class="flex items-center text-gray-500 hover:text-blue-600"
                                >
                                    <span class="material-icons mr-1"
                                        >home</span
                                    >
                                    หน้าแรก
                                </NuxtLink>
                                <span class="mx-2">/</span>
                            </li>

                            <!-- Section 2: หมวดหมู่ -->
                            <li class="breadcrumb-item flex items-center">
                                <NuxtLink
                                    :to="getCategoryLink()"
                                    class="text-gray-500 hover:text-blue-600"
                                >
                                    {{ getCategoryText() }}
                                </NuxtLink>
                                <span class="mx-2">/</span>
                            </li>

                            <!-- Section 3: ประเทศ -->
                            <li class="breadcrumb-item flex items-center">
                                <NuxtLink
                                    :to="getCountryLink()"
                                    class="text-gray-500 hover:text-blue-600"
                                >
                                    {{ getCountryText() }}
                                </NuxtLink>
                                <span class="mx-2">/</span>
                            </li>

                            <!-- Section 4: ชื่ออิโมจิ -->
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
                                class="w-32 sm:w-48 lg:w-56 xl:w-64 h-auto"
                            />
                            <span
                                v-if="emoji.is_new"
                                class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-1 rounded custom-font-size"
                            >
                                NEW
                            </span>
                        </div>

                        <!-- Right Content Column -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-4">
                                {{ emoji.title }}
                            </h1>
                            <p class="hidden md:block text-gray-600 mb-2">
                                {{ emoji.detail }}
                            </p>
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
                            <a
                                :href="`https://line.me/ti/p/~ratasak1234`"
                                target="_blank"
                                class="inline-block bg-blue-700 text-white text-center px-6 py-3 rounded-full hover:bg-blue-600 w-full hidden md:block"
                            >
                                สั่งซื้อชุดนี้แอดไลน์ไอดี ratasak1234
                            </a>
                        </div>
                    </div>

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
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div
                    class="w-full lg:w-3/12 xl:w-4/12 border-l border-gray-200"
                >
                    <!-- เนื้อหาสำหรับคอลัมน์ขวา -->
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRuntimeConfig, useRoute, useHead } from "#app";

export default {
    setup() {
        const emoji = ref(null);
        const route = useRoute();
        const runtimeConfig = useRuntimeConfig();

        const fetchEmojiData = async () => {
            const emojiId = route.params.id;
            try {
                emoji.value = await $fetch(
                    `${runtimeConfig.public.apiBase}/emoji-view/${emojiId}`
                );
            } catch (error) {
                console.error("Error loading emoji:", error);
            }
        };

        const fetchEmojiSEO = async () => {
            const emojiId = route.params.id;
            try {
                const response = await $fetch(
                    `${runtimeConfig.public.apiBase}/emoji-seo/${emojiId}`
                );

                // ตั้งค่า Meta Tags
                useHead({
                    title: response.title,
                    meta: [
                        { name: "description", content: response.description },
                        { name: "keywords", content: response.keywords },
                        { property: "og:title", content: response.title },
                        {
                            property: "og:description",
                            content: response.description,
                        },
                        { property: "og:image", content: response.image },
                        { property: "og:url", content: response.url },
                    ],
                });
            } catch (error) {
                console.error("Error fetching emoji SEO:", error);
            }
        };

        const generateEmojiImageUrl = (emojiCode, imgOrder) => {
            const baseUrl =
                "https://stickershop.line-scdn.net/sticonshop/v1/sticon";
            return `${baseUrl}/${emojiCode}/iphone/${String(imgOrder).padStart(
                3,
                "0"
            )}.png`;
        };

        const hideImage = (event) => {
            event.target.style.display = "none";
        };

        const getCategoryText = () => {
            if (!emoji.value) return "";
            return emoji.value.category === "official"
                ? "อิโมจิทางการ"
                : "ครีเอเตอร์อิโมจิ";
        };

        const getCategoryLink = () => {
            if (!emoji.value) return "#";
            return `/emojis?category=${emoji.value.category}`;
        };

        const getCountryText = () => {
            if (!emoji.value) return "";
            const countryMap = {
                th: "ประเทศไทย",
                jp: "ประเทศญี่ปุ่น",
                tw: "ประเทศไต้หวัน",
                id: "ประเทศอินโดนีเซีย",
            };
            return countryMap[emoji.value.country] || "ประเทศไม่ทราบ";
        };

        const getCountryLink = () => {
            if (!emoji.value) return "#";
            return `/emojis?country=${emoji.value.country}`;
        };

        onMounted(async () => {
            await fetchEmojiData(); // ดึงข้อมูลอิโมจิ
            await fetchEmojiSEO(); // ดึงข้อมูล SEO และตั้งค่า Meta Tags
        });

        return {
            emoji,
            route,
            generateEmojiImageUrl,
            hideImage,
            getCategoryText,
            getCategoryLink,
            getCountryText,
            getCountryLink,
        };
    },
};
</script>

<style scoped>
h1 {
    word-break: break-word;
    overflow-wrap: anywhere;
}
</style>
