import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import StickerView from "../pages/StickerView.vue";
import StickerMore from "../pages/StickerMore.vue";
import ThemeMore from "../pages/ThemeMore.vue";
import ThemeView from "../pages/ThemeView.vue";
import EmojiMore from "../pages/EmojiMore.vue";
import EmojiView from "../pages/EmojiView.vue";

const routes = [
    { path: "/", name: "Home", component: Home },
    { path: "/sticker/:id", name: "StickerView", component: StickerView },
    { path: "/stickers", name: "Stickers", component: StickerMore },
    { path: "/themes", name: "Themes", component: ThemeMore },
    { path: "/theme/:id", name: "ThemeView", component: ThemeView },
    { path: "/emojis", name: "Emojis", component: EmojiMore },
    { path: "/emoji/:id", name: "EmojiView", component: EmojiView },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
