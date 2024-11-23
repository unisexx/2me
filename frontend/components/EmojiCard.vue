<template>
    <div
        class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2 md:gap-4 mt-6"
    >
        <a
            v-for="emoji in emojis"
            :key="emoji.id"
            :href="'/emoji/' + emoji.id"
            class="overflow-hidden block"
        >
            <div class="relative">
                <!-- รูปภาพเต็มขนาด -->
                <img
                    :src="`https://stickershop.line-scdn.net/sticonshop/v1/product/${emoji.emoji_code}/iphone/main.png`"
                    :alt="emoji.title"
                    class="w-full"
                />
                <span
                    v-if="emoji.is_new"
                    class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-1 rounded custom-font-size"
                >
                    NEW
                </span>
            </div>
            <div class="p-4 text-center">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">
                    <span
                        v-if="emoji.country !== 'th'"
                        class="fflag ff-sm mb-1"
                        :class="'fflag-' + emoji.country.toUpperCase()"
                        :title="emoji.country"
                    ></span>
                    {{ emoji.title }}
                </h3>
                <p class="text-sm font-bold text-green-600">
                    {{ emoji.price }} THB
                </p>
            </div>
        </a>
    </div>
</template>

<script>
export default {
    props: {
        emojis: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped>
/* คุณสามารถเพิ่มสไตล์เฉพาะสำหรับ component นี้ได้ */
.custom-font-size {
    font-size: 8px; /* Default สำหรับมือถือ */
}
</style>
