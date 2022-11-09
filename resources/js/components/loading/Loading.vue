<template>
    <Transition>
        <div
            v-if="isActive"
            class="fixed inset-0 z-40 flex items-center justify-center"
        >
            <div class="absolute inset-0 bg-white opacity-50 z-40" />
            <div class="z-50 flex flex-col max-w-full">
                <font-awesome-icon
                    class="text-gray-600"
                    icon="fa-solid fa-spinner"
                    pulse
                    spin
                    size="3x"
                />
            </div>
        </div>
    </Transition>
</template>

<script lang="ts">
import { ref } from "vue";
import type { Ref } from "vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

export default {
    components: {
        FontAwesomeIcon,
    },
    setup() {
        const isActive: Ref<boolean> = ref(false);

        const open = (): void => {
            isActive.value = true;
        };

        const close = (): void => {
            isActive.value = false;
        };

        return {
            isActive,
            open,
            close,
        };
    },
};
</script>

<style scoped>
header {
    box-shadow: 0px 1px 2px #00000059;
}
footer {
    box-shadow: 0px -1px 2px #00000059;
}

.v-enter-active,
.v-leave-active {
    transition: all 0.1s ease-in-out;
}
.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
