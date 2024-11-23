<template>
    <Layout>
        <div class="container mx-auto">
            <h2 class="text-xl font-semibold mb-4">{{ headerTitle }}</h2>

            <!-- ปุ่มเปลี่ยนประเทศ -->
            <div class="flex gap-2 mb-4">
                <button
                    v-for="(label, country) in filteredCountries"
                    :key="country"
                    class="px-4 py-2 rounded"
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

            <!-- ใช้ EmojiCard -->
            <EmojiCard :emojis="emojis.data" />

            <!-- Pagination Controls -->
            <div class="flex justify-between items-center mt-6">
                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="!emojis.prev_page_url"
                    @click="changePage(emojis.current_page - 1)"
                >
                    Previous
                </button>
                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="!emojis.next_page_url"
                    @click="changePage(emojis.current_page + 1)"
                >
                    Next
                </button>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from "~/components/Layout.vue";
import EmojiCard from "~/components/EmojiCard.vue";
import { ref, computed, watch } from "vue";
import { useRuntimeConfig, useRoute, useRouter } from "#app";

export default {
    components: {
        Layout,
        EmojiCard,
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const runtimeConfig = useRuntimeConfig();

        const emojis = ref({}); // ใช้สำหรับเก็บข้อมูล Pagination
        const category = ref(""); // เก็บค่า category จาก URL
        const countries = ref({
            "": "ทั้งหมด",
            th: "ไทย",
            jp: "ญี่ปุ่น",
            tw: "ไต้หวัน",
            id: "อินโดนีเซีย",
        });

        const fetchEmojis = async (page, category, country, order) => {
            try {
                const url = `${runtimeConfig.public.apiBase}/emoji-more?page=${page}&category=${category}&country=${country}&order=${order}`;
                const response = await $fetch(url);
                emojis.value = response;

                // เลื่อนหน้าจอไปด้านบนสุดหลังจากโหลดเสร็จ
                window.scrollTo({ top: 0 });
            } catch (error) {
                console.error("Error fetching emojis:", error);
            }
        };

        const headerTitle = computed(() => {
            const currentCategory = category.value;
            const currentCountry = route.query.country || "";
            const countryLabel = countries.value[currentCountry] || "";

            let baseTitle = "";
            if (currentCategory === "official") {
                baseTitle = "อิโมจิไลน์ทางการ";
            } else if (currentCategory === "creator") {
                baseTitle = "อิโมจิไลน์ครีเอเตอร์";
            } else {
                baseTitle = "อิโมจิทั้งหมด";
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
                fetchEmojis(page, category.value, country, order);
            },
            { immediate: true }
        );

        return {
            route,
            emojis,
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
