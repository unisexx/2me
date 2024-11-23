<script>
import Layout from "~/components/Layout.vue";
import StickerCard from "~/components/StickerCard.vue";
import { ref, watch, computed } from "vue";
import { useRuntimeConfig, useRoute, useRouter, useHead } from "#app";

export default {
    components: {
        Layout,
        StickerCard,
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const runtimeConfig = useRuntimeConfig();

        // ตั้งค่าเริ่มต้นของ stickers
        const stickers = ref({
            data: [],
            current_page: 1,
            next_page_url: null,
            prev_page_url: null,
        });

        const category = ref("");
        const countries = ref({
            "": "ทั้งหมด",
            th: "ไทย",
            jp: "ญี่ปุ่น",
            tw: "ไต้หวัน",
            id: "อินโดนีเซีย",
        });

        const fetchStickers = async (page, category, country, order) => {
            try {
                const url = `${runtimeConfig.public.apiBase}/sticker-more?page=${page}&category=${category}&country=${country}&order=${order}`;
                const response = await $fetch(url);
                stickers.value = response;

                // เช็คว่าอยู่ใน Browser ก่อนเลื่อนหน้า
                if (typeof window !== "undefined") {
                    window.scrollTo({ top: 0 });
                }
            } catch (error) {
                console.error("Error fetching stickers:", error);
                stickers.value.data = []; // เคลียร์ข้อมูลเมื่อ API ล้มเหลว
            }
        };

        const headerTitle = computed(() => {
            const currentCategory = category.value;
            const currentCountry = route.query.country || "";
            const countryLabel = countries.value[currentCountry] || "";

            let baseTitle = "";
            if (currentCategory === "official") {
                baseTitle = "สติกเกอร์ไลน์ทางการ";
            } else if (currentCategory === "creator") {
                baseTitle = "สติกเกอร์ไลน์ครีเอเตอร์";
            } else {
                baseTitle = "สติกเกอร์ทั้งหมด";
            }

            return countryLabel ? `${baseTitle}${countryLabel}` : baseTitle;
        });

        const filteredCountries = computed(() => {
            if (category.value === "creator") {
                return {
                    "": "ทั้งหมด",
                    th: "ไทย",
                    jp: "ญี่ปุ่น",
                    tw: "ไต้หวัน",
                };
            }
            return countries.value;
        });

        const changePage = (page) => {
            router.push({
                query: {
                    ...route.query,
                    page,
                },
            });
        };

        const changeCountry = (country) => {
            router.push({
                query: {
                    ...route.query,
                    country,
                    page: 1,
                },
            });
        };

        // ตั้งค่า SEO โดยใช้ useHead
        useHead(() => {
            const title = `${headerTitle.value} | Line2Me`;
            const description = `สำรวจ ${headerTitle.value} ที่ Line2Me มีสติกเกอร์หลากหลายหมวดหมู่ พร้อมรายละเอียดและราคา`;
            const keywords = `สติกเกอร์ไลน์, ${headerTitle.value}, ซื้อสติกเกอร์, Line2Me`;

            return {
                title,
                meta: [
                    { name: "description", content: description },
                    { name: "keywords", content: keywords },
                    { property: "og:title", content: title },
                    { property: "og:description", content: description },
                    { property: "og:type", content: "website" },
                    {
                        property: "og:url",
                        content: window?.location?.href || "",
                    },
                    {
                        property: "og:image",
                        content:
                            "https://example.com/default-sticker-image.jpg", // เปลี่ยน URL รูปภาพตามจริง
                    },
                ],
            };
        });

        watch(
            () => route.query,
            (newQuery) => {
                const page = newQuery.page || 1;
                const country = newQuery.country || "";
                const order = newQuery.order || "new";

                category.value = newQuery.category || "";

                fetchStickers(page, category.value, country, order);
            },
            { immediate: true }
        );

        return {
            route,
            stickers,
            countries,
            headerTitle,
            filteredCountries,
            changePage,
            changeCountry,
        };
    },
};
</script>

<template>
    <Layout>
        <div class="container mx-auto">
            <h2 class="text-xl font-semibold mb-4">{{ headerTitle }}</h2>

            <!-- ปุ่มเปลี่ยนประเทศ -->
            <div class="flex flex-wrap gap-2 mb-4">
                <button
                    v-for="(label, country) in filteredCountries"
                    :key="country"
                    class="px-2 py-1 sm:px-4 sm:py-2 rounded"
                    :class="{
                        'bg-blue-500 text-white':
                            route.query.country === country,
                        'bg-gray-200 text-black':
                            route.query.country !== country,
                    }"
                    @click="changeCountry(country)"
                >
                    {{ label }}
                </button>
            </div>

            <!-- ใช้ StickerCard -->
            <StickerCard
                v-if="stickers.data.length"
                :stickers="stickers.data"
            />

            <!-- Pagination Controls -->
            <div class="flex justify-between items-center mt-6">
                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="!stickers.prev_page_url"
                    @click="changePage(stickers.current_page - 1)"
                >
                    Previous
                </button>
                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="!stickers.next_page_url"
                    @click="changePage(stickers.current_page + 1)"
                >
                    Next
                </button>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}
</style>
