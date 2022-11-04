<template>
    <Transition>
        <div
            v-if="isActive"
            class="fixed inset-0 z-40 flex items-center justify-center"
        >
            <div
                class="absolute inset-0 bg-black opacity-50 z-40"
                @click="close"
            />
            <div class="z-50 flex flex-col rounded-xl bg-zinc-50 max-w-full">
                <header
                    class="px-4 py-3 bg-zinc-200 rounded-t-xl text-2xl font-semibold"
                    v-if="data.title"
                >
                    {{ data.title }}
                </header>

                <section
                    class="px-4 py-2 text-lg"
                    :class="{ 'rounded-t-xl': !data.title }"
                >
                    <div v-html="data.message" />
                </section>

                <footer
                    class="px-4 py-3 bg-zinc-200 rounded-b-xl flex justify-end gap-2"
                >
                    <button
                        v-if="data.cancelText"
                        class="py-1 px-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-900 transition ease-in-out duration-150"
                        @click="close"
                    >
                        {{ data.cancelText }}
                    </button>
                    <button
                        class="py-1 px-4 text-white rounded-lg focus:outline-none transition ease-in-out duration-150"
                        :class="data.confirmColorClass"
                        @click="confirm"
                    >
                        {{ data.confirmText }}
                    </button>
                </footer>
            </div>
        </div>
    </Transition>
</template>

<script lang="ts">
import { ref } from "vue";
import type { Ref } from "vue";

import { confirmData } from "./interfaces";

const defaultData: confirmData = {
    title: "",
    message: "",
    width: 450,
    confirmText: "OK",
    confirmColorClass:
        "bg-green-600 hover:bg-green-700 active:bg-green-800 focus:border-green-900",
    cancelText: "Отмена",
    onConfirm: () => {},
};

export default {
    setup() {
        const isActive: Ref<boolean> = ref(false);

        const data: Ref<confirmData> = ref({ ...defaultData });

        const open = (params: confirmData): void => {
            data.value = { ...defaultData, ...params };
            isActive.value = true;
        };

        const close = (): void => {
            isActive.value = false;
        };

        const confirm = async (): Promise<any> => {
            await data.value.onConfirm?.();
            close();
        };

        return {
            isActive,
            data,
            open,
            close,
            confirm,
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
