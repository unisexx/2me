export default defineNuxtConfig({
    css: ['~/assets/css/main.css'],

    postcss: {
      plugins: {
        tailwindcss: {},
        autoprefixer: {},
      },
    },

    // ปิดเส้นทางอัตโนมัติ (ตัวเลือก)
    pages: true,

    // โหลดคอมโพเนนต์อัตโนมัติ
    components: true,

    modules: [
      '@vueuse/nuxt', // เพิ่มโมดูล @vueuse/nuxt ตรงนี้
    ],

    runtimeConfig: {
      public: {
        apiBase: 'http://dev-line2me.test/api', // เปลี่ยนจาก 127.0.0.1 เป็น dev-line2me.test
      },
    },

    compatibilityDate: '2024-11-23',
  });
