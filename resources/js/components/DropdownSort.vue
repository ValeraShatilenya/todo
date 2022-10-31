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

<script>
import { computed, onMounted, onUnmounted, ref } from "vue";

export default {
    props: {
        data: {
            required: true,
            type: Array,
        },
        modelValue: {
            required: true,
        },
    },
    setup(props, { emit }) {
        const showDropdown = ref(false);

        const selectedItem = computed(() => {
            return props.data.find(({ value }) => value === props.modelValue);
        });

        const buttonElement = ref(null);
        const dropdownElement = ref(null);

        const clickedOutside = (event) => {
            if (
                buttonElement.value?.contains(event.target) ||
                dropdownElement.value?.contains(event.target)
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

        const changeValue = (value) => {
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
