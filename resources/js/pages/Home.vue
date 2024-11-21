<template>
    <Layout>
        <div class="container mx-auto">
            <h2 class="text-xl font-semibold mb-4">
                สติกเกอร์ไลน์อัพเดทประจำสัปดาห์
            </h2>
            <StickerCard :stickers="stickers" />

            <hr />

            <h2 class="text-xl font-semibold mt-8 mb-4">
                ธีมไลน์อัพเดทประจำสัปดาห์
            </h2>
            <ThemeCard :themes="themes" />

            <hr />

            <h2 class="text-xl font-semibold mt-8 mb-4">
                อิโมจิไลน์อัพเดทประจำสัปดาห์
            </h2>
            <EmojiCard :emojis="emojis" />
        </div>
    </Layout>
</template>

<script>
import Layout from "../components/Layout.vue";
import ThemeCard from "../components/ThemeCard.vue";
import StickerCard from "../components/StickerCard.vue";
import EmojiCard from "../components/EmojiCard.vue";
import axios from "axios";

export default {
    components: {
        Layout,
        StickerCard,
        ThemeCard,
        EmojiCard,
    },
    data() {
        return {
            stickers: [], // ข้อมูลสำหรับสติกเกอร์
            themes: [], // ข้อมูลสำหรับธีม
            emojis: [], // ข้อมูลสำหรับธีม
        };
    },
    mounted() {
        this.fetchStickerUpdate();
        this.fetchThemeUpdate();
        this.fetchEmojiUpdate();
    },
    methods: {
        async fetchStickerUpdate() {
            try {
                const response = await axios.get("/api/sticker-update");
                this.stickers = response.data; // เก็บข้อมูลสติกเกอร์
            } catch (error) {
                console.error("Error fetching sticker update:", error);
            }
        },
        async fetchThemeUpdate() {
            try {
                const response = await axios.get("/api/theme-update");
                this.themes = response.data; // เก็บข้อมูลธีม
            } catch (error) {
                console.error("Error fetching theme update:", error);
            }
        },
        async fetchEmojiUpdate() {
            try {
                const response = await axios.get("/api/emoji-update");
                this.emojis = response.data; // เก็บข้อมูลอิโมจิ
            } catch (error) {
                console.error("Error fetching emoji update:", error);
            }
        },
    },
};
</script>
