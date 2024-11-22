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
                            $route.query.country === country,
                        'bg-gray-200 text-black':
                            $route.query.country !== country,
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
import Layout from "../components/Layout.vue";
import StickerCard from "../components/StickerCard.vue";
import axios from "axios";

export default {
    components: {
        Layout,
        StickerCard,
    },
    data() {
        return {
            stickers: {}, // ใช้สำหรับเก็บข้อมูล Pagination
            countries: {
                "": "ทั้งหมด",
                th: "ไทย",
                jp: "ญี่ปุ่น",
                tw: "ไต้หวัน",
                id: "อินโดนีเซีย",
            },
            category: "", // เก็บค่า category จาก URL
        };
    },
    computed: {
        headerTitle() {
            const category = this.category;
            const country = this.$route.query.country || ""; // ดึง country จาก query parameter
            const countryLabel = this.countries[country] || ""; // ดึงชื่อประเทศจาก countries

            let baseTitle = "";
            if (category === "official") {
                baseTitle = "สติกเกอร์ไลน์ทางการ";
            } else if (category === "creator") {
                baseTitle = "สติกเกอร์ไลน์ครีเอเตอร์";
            } else {
                baseTitle = "สติกเกอร์ทั้งหมด";
            }

            // เพิ่มชื่อประเทศต่อท้าย หาก countryLabel ไม่ว่าง
            return countryLabel ? `${baseTitle}${countryLabel}` : baseTitle;
        },
        // ฟิลเตอร์ประเทศตาม category
        filteredCountries() {
            if (this.category === "creator") {
                return {
                    "": "ทั้งหมด",
                    th: "ไทย",
                    jp: "ญี่ปุ่น",
                    tw: "ไต้หวัน",
                };
            }
            return this.countries;
        },
    },
    watch: {
        // ตรวจจับการเปลี่ยนแปลง query parameter
        "$route.query": {
            immediate: true,
            handler(newQuery) {
                const page = newQuery.page || 1;
                const country = newQuery.country || "";
                const order = newQuery.order || "new";

                // อัปเดต category จาก query parameter
                this.category = newQuery.category || "";

                // เรียก API เพื่อโหลดข้อมูล
                this.fetchStickers(page, this.category, country, order);
            },
        },
    },
    methods: {
        async fetchStickers(page, category, country, order) {
            try {
                const url = `/api/sticker-more?page=${page}&category=${category}&country=${country}&order=${order}`;
                const response = await axios.get(url);
                this.stickers = response.data;

                // เลื่อนหน้าจอไปด้านบนสุดหลังจากโหลดเสร็จ
                window.scrollTo({
                    top: 0,
                });
            } catch (error) {
                console.error("Error fetching stickers:", error);
            }
        },
        changePage(page) {
            this.$router.push({
                query: {
                    ...this.$route.query,
                    page, // อัปเดต page
                },
            });

            // Debugging page change
            console.log("Changing page to:", page);
        },
        changeCountry(country) {
            this.$router.push({
                query: {
                    ...this.$route.query,
                    country,
                    page: 1, // รีเซ็ตไปหน้าแรก
                },
            });
        },
    },
};
</script>

<style scoped>
button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}
</style>
