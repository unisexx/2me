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
                            $route.query.country === country,
                        'bg-gray-200 text-black':
                            $route.query.country !== country,
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
import Layout from "../components/Layout.vue";
import EmojiCard from "../components/EmojiCard.vue"; // Import EmojiCard component
import axios from "axios";

export default {
    components: {
        Layout,
        EmojiCard,
    },
    data() {
        return {
            emojis: {}, // ใช้สำหรับเก็บข้อมูล Pagination
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
            // หาก category = creator ให้แสดงเฉพาะประเทศที่ระบุ
            if (this.category === "creator") {
                return {
                    "": "ทั้งหมด",
                    th: "ไทย",
                    jp: "ญี่ปุ่น",
                    tw: "ไต้หวัน",
                };
            }
            // สำหรับ category อื่น ให้แสดงทั้งหมด
            return this.countries;
        },
    },
    watch: {
        // ตรวจจับการเปลี่ยนแปลง query parameter
        "$route.query": {
            immediate: true,
            handler(newQuery) {
                const page = newQuery.page || 1; // Default page
                const category = newQuery.category || ""; // Default category
                const country = newQuery.country || ""; // Default country
                const order = newQuery.order || "new"; // Default order

                // อัปเดต category จาก query parameter
                this.category = newQuery.category || "";

                // เรียก API เพื่อโหลดข้อมูล
                this.fetchEmojis(page, this.category, country, order);
            },
        },
    },
    methods: {
        async fetchEmojis(page, category, country, order) {
            try {
                // สร้าง URL จากพารามิเตอร์
                const url = `/api/emoji-more?page=${page}&category=${category}&country=${country}&order=${order}`;
                const response = await axios.get(url);
                this.emojis = response.data; // ดึงข้อมูล Pagination จาก API

                // เลื่อนหน้าจอไปด้านบนสุดหลังจากโหลดเสร็จ
                window.scrollTo({
                    top: 0,
                });
            } catch (error) {
                console.error("Error fetching emojis:", error);
            }
        },
        changePage(page) {
            // อัปเดต query parameter ของ URL โดยรักษาค่าพารามิเตอร์อื่นๆ ไว้
            this.$router.push({
                query: {
                    ...this.$route.query, // รักษาค่า query อื่นไว้
                    page, // อัปเดต page
                },
            });
        },
        changeCountry(country) {
            // อัปเดต query parameter ของ URL
            this.$router.push({
                query: {
                    ...this.$route.query, // รักษาค่า query อื่นไว้
                    country, // อัปเดต country
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
