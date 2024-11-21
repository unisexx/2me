<template>
    <div>
        <!-- <nav>
            <router-link to="/">Home</router-link> |
            <router-link to="/about">About</router-link>
        </nav> -->
        <router-view></router-view> <!-- แสดงเนื้อหาของหน้าที่เลือก -->
    </div>
</template>

<script>
export default {
    name: 'App',
};
</script>
