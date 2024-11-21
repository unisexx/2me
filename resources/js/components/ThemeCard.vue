<template>
    <div
        class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2 md:gap-4 mt-6"
    >
        <a
            v-for="theme in themes"
            :key="theme.id"
            :href="'/theme/' + theme.id"
            class="overflow-hidden block"
        >
            <div class="relative">
                <!-- รูปภาพเต็มขนาด -->
                <img :src="theme.img_url" :alt="theme.title" class="w-full" />
                <span
                    v-if="theme.is_new"
                    class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded"
                >
                    NEW
                </span>
            </div>
            <div class="p-4 text-center">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">
                    <span
                        v-if="theme.country !== 'th'"
                        class="fflag ff-sm mb-1"
                        :class="'fflag-' + theme.country.toUpperCase()"
                        :title="theme.country"
                    ></span>
                    {{ theme.title }}
                </h3>
                <p class="text-sm font-bold text-green-600">
                    {{ theme.price }} THB
                </p>
            </div>
        </a>
    </div>
</template>

<script>
export default {
    props: {
        themes: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped>
/* คุณสามารถเพิ่มสไตล์เฉพาะสำหรับ component นี้ได้ */
</style>
