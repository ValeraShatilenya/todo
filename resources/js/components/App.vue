<template>
    <custom-navbar />
    <div class="max-w-7xl mx-auto sm:p-3 lg:p-6">
        <div
            class="scroller bg-white max-height-calc overflow-y-auto shadow-sm sm:rounded-lg p-3 sm:px-6 sm:pt-6 bg-white border border-gray-200"
        >
            <router-view v-slot="{ Component }">
                <transition mode="out-in" name="slide">
                    <component :is="Component" />
                </transition>
            </router-view>
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
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-50%);
}

/* Works on Firefox */
.scroller {
    scrollbar-width: thin;
    scrollbar-color: #0000004f #d1d1d1;
}

/* Works on Chrome, Edge, and Safari */
.scroller::-webkit-scrollbar {
    width: 10px;
}

.scroller::-webkit-scrollbar-track {
    background: #d1d1d1;
    border-radius: 8px;
}

.scroller::-webkit-scrollbar-thumb {
    background-color: #0000004f;
    border-radius: 8px;
}

.max-height-calc {
    max-height: calc(100vh - 112px);
}

@media screen and (max-width: 1024px) {
    .max-height-calc {
        max-height: calc(100vh - 90px);
    }
}

@media screen and (max-width: 768px) {
    .max-height-calc {
        max-height: calc(100vh - 56px);
    }
}

@media screen and (max-width: 640px) {
    .max-height-calc {
        max-height: calc(100vh - 32px);
    }
    .scroller::-webkit-scrollbar-track {
        border-radius: 0px;
    }

    .scroller::-webkit-scrollbar-thumb {
        border-radius: 0px;
    }
}
</style>
