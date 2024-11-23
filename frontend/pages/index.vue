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

<script setup>
import { ref, onMounted } from "vue";
import { useRuntimeConfig } from "#app";

import Layout from "../components/Layout.vue";
import ThemeCard from "../components/ThemeCard.vue";
import StickerCard from "../components/StickerCard.vue";
import EmojiCard from "../components/EmojiCard.vue";

const config = useRuntimeConfig();

const stickers = ref([]); // ข้อมูลสำหรับสติกเกอร์
const themes = ref([]); // ข้อมูลสำหรับธีม
const emojis = ref([]); // ข้อมูลสำหรับอิโมจิ

// ดึงข้อมูลเมื่อ mounted
const fetchStickerUpdate = async () => {
    try {
        const response = await fetch(`${config.public.apiBase}/sticker-update`);
        stickers.value = await response.json();
    } catch (error) {
        console.error("Error fetching sticker update:", error);
    }
};

const fetchThemeUpdate = async () => {
    try {
        const response = await fetch(`${config.public.apiBase}/theme-update`);
        themes.value = await response.json();
    } catch (error) {
        console.error("Error fetching theme update:", error);
    }
};

const fetchEmojiUpdate = async () => {
    try {
        const response = await fetch(`${config.public.apiBase}/emoji-update`);
        emojis.value = await response.json();
    } catch (error) {
        console.error("Error fetching emoji update:", error);
    }
};

onMounted(() => {
    fetchStickerUpdate();
    fetchThemeUpdate();
    fetchEmojiUpdate();
});
</script>

<style scoped>
/* เพิ่มสไตล์เพิ่มเติมถ้าจำเป็น */
</style>
