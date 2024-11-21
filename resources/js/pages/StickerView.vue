<template>
    <Layout>
        <div class="container mx-auto">
            <!-- เนื้อหา -->
            <div v-if="loading" class="text-center text-gray-500">
                Loading...
            </div>

            <div v-else>
                <!-- เนื้อหาหลัก -->
                <div class="flex flex-wrap">
                    <!-- ซ้าย: ข้อมูลสติกเกอร์ -->
                    <div class="w-full lg:w-8/12 mx-auto pe-5">
                        <!-- Breadcrumb -->
                        <nav
                            v-if="!loading && sticker"
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
                                <li
                                    class="breadcrumb-item active text-gray-700"
                                >
                                    {{ sticker.title_th }}
                                </li>
                            </ol>
                        </nav>

                        <div class="flex items-start">
                            <!-- รูปภาพหลัก -->
                            <div class="relative mr-3">
                                <img
                                    :src="sticker.img_url"
                                    :alt="sticker.title"
                                    class="w-32 sm:w-48 lg:w-60 h-auto autoLoopSticker"
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
                            </div>

                            <!-- ข้อมูลข้อความ -->
                            <div class="flex-1">
                                <h1
                                    class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4"
                                >
                                    {{ sticker.title_th }}
                                </h1>
                                <p class="mb-2">
                                    <span class="font-bold">รหัสสินค้า:</span>
                                    {{ sticker.sticker_code }}
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
                                <p class="text-gray-600 mb-2">
                                    {{ sticker.description }}
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

                        <!-- ตัวอย่างสติกเกอร์ในชุด -->
                        <div class="mt-8">
                            <h2 class="text-2xl font-semibold mb-4">
                                ตัวอย่างสติกเกอร์ในชุด
                            </h2>
                            <div
                                class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-4 gap-4"
                            >
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

                                    <!-- เงื่อนไขแสดงเสียง -->
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
                    <div class="w-full lg:w-4/12 border-l border-gray-200">
                        <!-- เนื้อหาคอลัมน์ขวา -->
                    </div>
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
            sticker: null, // ข้อมูลสติกเกอร์
            loading: true, // สถานะการโหลด
        };
    },
    methods: {
        async fetchStickerData() {
            const stickerCode = this.$route.params.id;
            try {
                const response = await axios.get(
                    `/api/sticker-view/${stickerCode}`
                );
                this.sticker = response.data;
            } catch (error) {
                console.error("Error loading sticker:", error);
            } finally {
                this.loading = false;
            }
        },
        getCategoryText() {
            if (!this.sticker) return "";
            if (this.sticker.category === "official") {
                return "สติกเกอร์ทางการ";
            } else if (this.sticker.category === "creator") {
                return "ครีเอเตอร์สติกเกอร์";
            }
            return "หมวดหมู่ไม่ทราบ";
        },
        getCategoryLink() {
            if (!this.sticker) return "#";
            return `/stickers?category=${this.sticker.category}`;
        },
        getCountryText() {
            if (!this.sticker) return "";
            const countryMap = {
                th: "ประเทศไทย",
                jp: "ประเทศญี่ปุ่น",
                tw: "ประเทศไต้หวัน",
                id: "ประเทศอินโดนีเซีย",
            };
            return countryMap[this.sticker.country] || "ประเทศไม่ทราบ";
        },
        getCountryLink() {
            if (!this.sticker) return "#";
            return `/stickers?country=${this.sticker.country}`;
        },
        getStickerUrl(stamp) {
            return `https://stickershop.line-scdn.net/stickershop/v1/sticker/${stamp}/android/sticker.png;compress=true`;
        },
        getAnimationUrl(stamp) {
            const type = this.sticker.stickerresourcetype;

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
        },
        changeToAnimation(event) {
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
        },
    },
    async mounted() {
        await this.fetchStickerData();
    },
};
</script>

<style scoped>
.breadcrumb {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 0.5rem;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item a {
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #6b7280;
}
.stampImg {
    cursor: pointer;
}
</style>
