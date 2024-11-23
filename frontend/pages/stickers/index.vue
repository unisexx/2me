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
            <StickerCard :stickers="stickers.data" />

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

<script>
import Layout from "~/components/Layout.vue";
import StickerCard from "~/components/StickerCard.vue";
import { ref, watch, computed } from "vue";
import { useRuntimeConfig, useRoute, useRouter } from "#app";

export default {
    components: {
        Layout,
        StickerCard,
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const runtimeConfig = useRuntimeConfig();

        const stickers = ref({}); // ใช้สำหรับเก็บข้อมูล Pagination
        const category = ref(""); // เก็บค่า category จาก URL
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

                // เลื่อนหน้าจอไปด้านบนสุดหลังจากโหลดเสร็จ
                window.scrollTo({ top: 0 });
            } catch (error) {
                console.error("Error fetching stickers:", error);
            }
        };

        const headerTitle = computed(() => {
            const currentCategory = category.value;
            const currentCountry = route.query.country || ""; // ดึง country จาก query parameter
            const countryLabel = countries.value[currentCountry] || ""; // ดึงชื่อประเทศจาก countries

            let baseTitle = "";
            if (currentCategory === "official") {
                baseTitle = "สติกเกอร์ไลน์ทางการ";
            } else if (currentCategory === "creator") {
                baseTitle = "สติกเกอร์ไลน์ครีเอเตอร์";
            } else {
                baseTitle = "สติกเกอร์ทั้งหมด";
            }

            // เพิ่มชื่อประเทศต่อท้าย หาก countryLabel ไม่ว่าง
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
                    page, // อัปเดต page
                },
            });
        };

        const changeCountry = (country) => {
            router.push({
                query: {
                    ...route.query,
                    country,
                    page: 1, // รีเซ็ตไปหน้าแรก
                },
            });
        };

        watch(
            () => route.query,
            (newQuery) => {
                const page = newQuery.page || 1;
                const country = newQuery.country || "";
                const order = newQuery.order || "new";

                // อัปเดต category จาก query parameter
                category.value = newQuery.category || "";

                // เรียก API เพื่อโหลดข้อมูล
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

<style scoped>
button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}
</style>
