<template>
    <div class="relative">
        <button
            ref="buttonElement"
            class="focus:outline-none font-medium rounded-xl text-base text-center inline-flex items-center"
            @click="showDropdown = !showDropdown"
        >
            <span class="underline text-sm">
                Сортировка по: {{ selectedItem?.title }}
            </span>
            <font-awesome-icon
                icon="fa-solid fa-sort-up"
                :rotation="showDropdown ? null : 180"
                size="lg"
                class="ml-2"
                :class="showDropdown ? '-mb-2' : '-mt-2'"
            />
        </button>
        <Transition>
            <div
                ref="dropdownElement"
                class="absolute top-full w-32 mt-2 z-10 bg-white rounded divide-y divide-gray-100 shadow"
                v-if="showDropdown"
            >
                <ul class="p-3 space-y-1 text-sm text-gray-700">
                    <li v-for="(item, index) in data" :key="index">
                        <div
                            class="flex p-2 rounded hover:bg-gray-200"
                            :class="
                                item === selectedItem
                                    ? 'bg-gray-100 cursor-not-allowed'
                                    : 'cursor-pointer'
                            "
                            @click="changeValue(item.value)"
                        >
                            <div class="text-sm font-medium text-gray-900">
                                {{ item.title }}
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </Transition>
    </div>
</template>

<script lang="ts">
import { computed, ComputedRef, onMounted, onUnmounted, Ref, ref } from "vue";

interface IDataItem {
    value: any;
    title: string;
    [key: string]: any;
}

export default {
    props: {
        data: {
            required: true,
            type: Array as () => IDataItem[],
        },
        modelValue: {
            required: true,
        },
    },
    setup(props: any, { emit }: any) {
        const showDropdown = ref(false);

        interface IDataItem {
            value: any;
            [key: string]: any;
        }

        const selectedItem: ComputedRef<any> = computed(() => {
            const data = props.data as IDataItem[];
            return data.find(({ value }) => value === props.modelValue);
        });

        const buttonElement: Ref<HTMLElement | null> = ref(null);
        const dropdownElement: Ref<HTMLElement | null> = ref(null);

        const clickedOutside = (event: Event): void => {
            const el = event.target as HTMLElement;
            if (
                !el ||
                buttonElement.value?.contains(el) ||
                dropdownElement.value?.contains(el)
            )
                return;
            showDropdown.value = false;
        };

        onMounted(() => {
            document?.addEventListener("click", clickedOutside);
        });
        onUnmounted(() => {
            document?.removeEventListener("click", clickedOutside);
        });

        const changeValue = (value: any) => {
            emit("update:modelValue", value);
            showDropdown.value = false;
        };

        return {
            showDropdown,
            selectedItem,
            changeValue,
            buttonElement,
            dropdownElement,
        };
    },
};
</script>

<style scoped>
.v-enter-active {
    transition: all 0.2s ease-out;
}
.v-leave-active {
    transition: all 0.1s ease-in;
}
.v-enter-from,
.v-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>
