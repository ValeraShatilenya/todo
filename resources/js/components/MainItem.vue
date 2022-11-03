<template>
    <div class="rounded-xl">
        <div class="py-2 flex px-3">
            <div class="flex-1 pr-1 max-w-100-40">
                <div class="flex relative">
                    <div
                        v-if="currentStatus"
                        :class="`tooltip w-3 h-3 rounded-full self-center mr-2 ${currentStatus.backgroundClass}`"
                    />
                    <div
                        v-if="currentStatus"
                        :class="`tooltip-content opacity-0 py-1 px-2 rounded-lg -translate-x-2.5 absolute bottom-full left-0 text-sm leading-4 text-white ${currentStatus.backgroundClass} transition ease-in-out duration-150`"
                    >
                        {{ currentStatus.description }}
                    </div>
                    <h3 class="text-lg font-semibold">
                        <slot name="title" />
                    </h3>
                </div>
                <p class="text-gray-500">
                    <slot name="description" />
                </p>
                <slot />
            </div>
            <div class="flex flex-wrap gap-2">
                <slot name="buttons" />
            </div>
        </div>
        <p class="text-gray-400 text-xs text-right pr-1">
            <slot name="footer" />
        </p>
    </div>
</template>

<script lang="ts">
import { computed, ComputedRef } from "vue";
import { IStatuse } from "../constants";

interface IStatuseObject {
    [key: number]: IStatuse;
}

export default {
    props: {
        status: {
            required: false,
            type: Number,
        },
        statuses: {
            required: false,
            type: Object as () => IStatuseObject,
            default: () => ({}),
        },
    },
    setup(props: any) {
        const currentStatus: ComputedRef<IStatuse | null> = computed(() => {
            if (!props.status) return null;
            return props.statuses?.[props.status] ?? null;
        });
        return {
            currentStatus,
        };
    },
};
</script>

<style scoped>
.max-w-100-40 {
    max-width: calc(100% - 40px);
}
.tooltip:hover ~ .tooltip-content {
    opacity: 1;
}
</style>
