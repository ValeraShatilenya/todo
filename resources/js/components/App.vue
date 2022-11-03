<template>
    <custom-navbar />
    <div
        class="max-height-calc overflow-y-auto max-w-7xl mx-auto sm:p-3 lg:p-6"
    >
        <div
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-3 pb-0 sm:p-6 sm:pb-3 bg-white border border-gray-200"
        >
            <router-view />
        </div>
    </div>
</template>

<script lang="ts">
import CustomNavbar from "./CustomNavbar.vue";

export default {
    components: {
        CustomNavbar,
    },
};
</script>

<style scoped>
.max-height-calc {
    max-height: calc(100vh - 64px);
}

@media screen and (max-width: 768px) {
    .max-height-calc {
        max-height: calc(100vh - 32px);
    }
}
</style>
