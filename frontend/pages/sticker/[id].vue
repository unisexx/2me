<template>
    <Layout>
        <div class="container mx-auto">
            <!-- Loading -->
            <div v-if="loading" class="text-center text-gray-500">
                Loading...
            </div>

            <!-- Main Content -->
            <div v-else>
                <div class="flex flex-wrap">
                    <!-- Left Column: Sticker Info -->
                    <div class="w-full lg:w-9/12 xl:w-8/12 mx-auto">
                        <!-- Breadcrumb -->
                        <nav
                            v-if="sticker"
                            class="mb-4 bg-gray-100 rounded-lg p-4"
                        >
                            <ol
                                class="breadcrumb flex flex-wrap items-center text-sm text-gray-500"
                            >
                                <li class="breadcrumb-item flex items-center">
                                    <NuxtLink
                                        to="/"
                                        class="text-gray-500 hover:text-blue-600"
                                    >
                                        หน้าแรก
                                    </NuxtLink>
                                    <span class="mx-2">/</span>
                                </li>
                                <li class="breadcrumb-item flex items-center">
                                    <NuxtLink
                                        :to="getCategoryLink()"
                                        class="text-gray-500 hover:text-blue-600"
                                    >
                                        {{ getCategoryText() }}
                                    </NuxtLink>
                                    <span class="mx-2">/</span>
                                </li>
                                <li class="breadcrumb-item flex items-center">
                                    <NuxtLink
                                        :to="getCountryLink()"
                                        class="text-gray-500 hover:text-blue-600"
                                    >
                                        {{ getCountryText() }}
                                    </NuxtLink>
                                    <span class="mx-2">/</span>
                                </li>
                                <li
                                    class="breadcrumb-item active text-gray-700"
                                >
                                    {{ sticker.title_th }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Sticker Details -->
                        <div class="flex items-start">
                            <!-- รูปภาพหลัก -->
                            <div class="relative mr-3">
                                <img
                                    :src="sticker.img_url"
                                    :alt="sticker.title"
                                    class="w-32 sm:w-48 lg:w-56 xl:w-64 h-auto autoLoopSticker"
                                    :data-animation="sticker.img_url"
                                />
                                <!-- เงื่อนไขแสดงเสียง -->
                                <audio
                                    v-if="
                                        [
                                            'SOUND',
                                            'POPUP_SOUND',
                                            'ANIMATION_SOUND',
                                        ].includes(sticker.stickerresourcetype)
                                    "
                                    id="mainAudio"
                                    autoplay
                                    preload="metadata"
                                    style="display: none"
                                >
                                    <source
                                        :src="`https://sdl-stickershop.line.naver.jp/stickershop/v1/product/${sticker.sticker_code}/IOS/main_sound.m4a`"
                                        type="audio/mpeg"
                                    />
                                </audio>

                                <span
                                    v-if="sticker.is_new"
                                    class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-1 rounded custom-font-size"
                                >
                                    NEW
                                </span>
                            </div>

                            <!-- ข้อมูลข้อความ -->
                            <div class="flex-1">
                                <h1
                                    class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 flex flex-wrap items-center gap-2"
                                >
                                    {{ sticker.title_th }}
                                    <span
                                        v-if="
                                            getStickerResourceTypeText(
                                                sticker.stickerresourcetype
                                            )
                                        "
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400 flex-shrink-0"
                                    >
                                        {{
                                            getStickerResourceTypeText(
                                                sticker.stickerresourcetype
                                            )
                                        }}
                                    </span>
                                </h1>

                                <p class="hidden md:block text-gray-600 mb-2">
                                    {{ sticker.detail }}
                                </p>
                                <p class="mb-2">
                                    <span class="font-bold">รหัสสินค้า:</span>
                                    {{ sticker.sticker_code }}
                                </p>
                                <p
                                    class="mb-2"
                                    v-if="
                                        getStickerResourceTypeText(
                                            sticker.stickerresourcetype
                                        )
                                    "
                                >
                                    ประเภท:
                                    {{
                                        getStickerResourceTypeText(
                                            sticker.stickerresourcetype
                                        )
                                    }}
                                </p>
                                <p class="mb-2">
                                    <span class="font-bold">ประเทศ:</span>
                                    <span
                                        class="fflag ff-sm ml-1 mb-1"
                                        :class="`fflag-${sticker.country.toUpperCase()}`"
                                        :title="sticker.country"
                                    ></span>
                                    {{ getCountryText() }}
                                </p>
                                <p class="text-lg mb-2 font-bold">
                                    <span>Price: </span>
                                    <span class="text-red-500">{{
                                        sticker.price
                                    }}</span>
                                    <span>THB</span>
                                </p>
                                <NuxtLink
                                    :to="`https://line.me/ti/p/~ratasak1234`"
                                    target="_blank"
                                    class="inline-block bg-blue-700 text-white text-center px-6 py-3 rounded-full hover:bg-blue-600 w-full hidden md:block"
                                >
                                    สั่งซื้อชุดนี้แอดไลน์ไอดี ratasak1234
                                </NuxtLink>
                            </div>
                        </div>

                        <div class="mt-2">
                            <NuxtLink
                                :to="`https://line.me/ti/p/~ratasak1234`"
                                target="_blank"
                                class="inline-block bg-blue-700 text-white text-center px-6 py-3 rounded-full hover:bg-blue-600 w-full block md:hidden"
                            >
                                สั่งซื้อชุดนี้แอดไลน์ไอดี ratasak1234
                            </NuxtLink>
                        </div>

                        <!-- ตัวอย่างสติกเกอร์ในชุด -->
                        <div class="mt-4">
                            <h2 class="text-2xl font-semibold mb-4">
                                ตัวอย่างสติกเกอร์ในชุด
                            </h2>

                            <!-- หากไม่มีข้อมูล -->
                            <div
                                v-if="
                                    !sticker.stamp || sticker.stamp.length === 0
                                "
                                class="text-center text-gray-500"
                            >
                                ไม่มีตัวอย่างสติกเกอร์ในชุด
                            </div>

                            <!-- หากมีข้อมูล -->
                            <div v-else class="grid grid-cols-4 gap-2">
                                <div
                                    v-for="stamp in sticker.stamp"
                                    :key="stamp"
                                    class="flex justify-center items-center"
                                >
                                    <img
                                        :src="getStickerUrl(stamp)"
                                        :alt="`สติกเกอร์ ${stamp}`"
                                        class="stampImg w-auto object-contain"
                                        :data-animation="getAnimationUrl(stamp)"
                                        @click="changeToAnimation"
                                    />
                                    <audio
                                        v-if="
                                            [
                                                'SOUND',
                                                'POPUP_SOUND',
                                                'ANIMATION_SOUND',
                                            ].includes(
                                                sticker.stickerresourcetype
                                            )
                                        "
                                        preload="metadata"
                                        style="display: none"
                                    >
                                        <source
                                            :src="`https://sdl-stickershop.line.naver.jp/products/0/0/${sticker.version}/${sticker.sticker_code}/android/sound/${stamp}.m4a`"
                                            type="audio/mpeg"
                                        />
                                    </audio>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ขวา: สามารถใส่ข้อมูลเพิ่มเติมได้ -->
                    <div
                        class="w-full lg:w-3/12 xl:w-4/12 border-l border-gray-200"
                    >
                        <!-- เนื้อหาคอลัมน์ขวา -->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from "~/components/Layout.vue";
import { ref, onMounted } from "vue";
import { useRuntimeConfig, useRoute, useHead } from "#app";

export default {
    components: { Layout },
    setup() {
        const sticker = ref(null);
        const seoData = ref(null);
        const loading = ref(true);
        const runtimeConfig = useRuntimeConfig();
        const route = useRoute();

        // ดึงข้อมูล Sticker
        const fetchStickerData = async (stickerCode) => {
            try {
                const response = await $fetch(
                    `${runtimeConfig.public.apiBase}/sticker-view/${stickerCode}`
                );
                sticker.value = response;
            } catch (error) {
                console.error("Error fetching sticker:", error);
            } finally {
                loading.value = false;
            }
        };

        // ดึงข้อมูล SEO
        const fetchStickerSEO = async (stickerCode) => {
            try {
                const response = await $fetch(
                    `${runtimeConfig.public.apiBase}/sticker-seo/${stickerCode}`
                );
                seoData.value = response;

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
                console.error("Error fetching SEO data:", error);
            }
        };

        onMounted(() => {
            const stickerCode = route.params.id;
            fetchStickerData(stickerCode);
            fetchStickerSEO(stickerCode);
        });

        // ฟังก์ชันเพิ่มเติม (คงไว้จากโค้ดเดิม)
        const getCategoryText = () => {
            if (!sticker.value) return "";
            if (sticker.value.category === "official") return "สติกเกอร์ทางการ";
            if (sticker.value.category === "creator")
                return "ครีเอเตอร์สติกเกอร์";
            return "หมวดหมู่ไม่ทราบ";
        };

        const getCategoryLink = () => {
            if (!sticker.value) return "#";
            return `/stickers?category=${sticker.value.category}`;
        };

        const getCountryText = () => {
            if (!sticker.value) return "";
            const countryMap = {
                th: "ประเทศไทย",
                jp: "ประเทศญี่ปุ่น",
                tw: "ประเทศไต้หวัน",
                id: "ประเทศอินโดนีเซีย",
            };
            return countryMap[sticker.value.country] || "ประเทศไม่ทราบ";
        };

        const getCountryLink = () => {
            if (!sticker.value) return "#";
            return `/stickers?country=${sticker.value.country}`;
        };

        const getStickerResourceTypeText = (type) => {
            const resourceTypeMap = {
                ANIMATION: "สติกเกอร์เคลื่อนไหว",
                ANIMATION_SOUND: "สติกเกอร์เคลื่อนไหว+มีเสียง",
                SOUND: "สติกเกอร์มีเสียง",
                POPUP: "สติกเกอร์ป๊อปอัพ",
                POPUP_SOUND: "สติกเกอร์ป๊อปอัพ + มีเสียง",
                NAME_TEXT: "สติกเกอร์เติมคำ",
                PER_STICKER_TEXT: "สติกเกอร์ข้อความ",
            };
            return resourceTypeMap[type] || "";
        };

        const getStickerUrl = (stamp) => {
            return `https://stickershop.line-scdn.net/stickershop/v1/sticker/${stamp}/android/sticker.png;compress=true`;
        };

        const getAnimationUrl = (stamp) => {
            const type = sticker.value?.stickerresourcetype || "";

            if (
                ["SOUND", "STATIC", "NAME_TEXT", "PER_STICKER_TEXT"].includes(
                    type
                )
            ) {
                return `https://stickershop.line-scdn.net/stickershop/v1/sticker/${stamp}/android/sticker.png;compress=true`;
            } else if (["POPUP", "POPUP_SOUND"].includes(type)) {
                return `https://stickershop.line-scdn.net/stickershop/v1/sticker/${stamp}/IOS/sticker_popup.png;compress=true`;
            } else {
                return `https://stickershop.line-scdn.net/stickershop/v1/sticker/${stamp}/IOS/sticker_animation@2x.png;compress=true`;
            }
        };

        const changeToAnimation = (event) => {
            const target = event.target;
            const animationSrc = target.getAttribute("data-animation");
            if (animationSrc) {
                target.src = animationSrc;
            }

            const parentDiv = target.closest("div");
            const audioElement = parentDiv.querySelector("audio");

            if (audioElement) {
                audioElement.play();
            }
        };

        return {
            sticker,
            seoData,
            loading,
            getCategoryText,
            getCategoryLink,
            getCountryText,
            getCountryLink,
            getStickerResourceTypeText,
            getStickerUrl,
            getAnimationUrl,
            changeToAnimation,
        };
    },
};
</script>

<style scoped>
h1 {
    word-break: break-word;
    overflow-wrap: anywhere;
}
.stampImg {
    cursor: pointer;
}
</style>
