<template>
    <div class="relative">
        <button
            ref="buttonElement"
            class="text-white h-full focus:ring-4 focus:outline-none font-medium rounded-xl text-base px-4 py-2.5 text-center inline-flex items-center"
            :class="buttonColorClass"
            @click="showDropdown = !showDropdown"
        >
            {{ title }}
            <font-awesome-icon
                :icon="`fa-solid fa-chevron-${showDropdown ? 'up' : 'down'}`"
                size="sm"
                class="ml-2"
            />
        </button>
        <Transition>
            <div
                ref="dropdownElement"
                class="absolute mt-2 z-10 w-60 bg-white rounded divide-y divide-gray-100 shadow"
                :class="{
                    'right-0': position === 'left',
                    'left-0': position === 'right',
                }"
                v-if="showDropdown"
            >
                <ul class="p-3 space-y-1 text-sm text-gray-700">
                    <li
                        v-for="(
                            { title, description, value, backgroundClass },
                            index
                        ) in data"
                        :key="index"
                    >
                        <div
                            class="flex p-2 rounded cursor-pointer hover:bg-gray-100"
                            @click="changeValue(value)"
                        >
                            <div class="flex items-center h-5">
                                <input
                                    :id="`helper-radio-${index}`"
                                    :name="name"
                                    type="radio"
                                    :value="value"
                                    v-model="modelValue"
                                    class="w-4 h-4 bg-gray-100 border-gray-300 cursor-pointer focus:ring-2"
                                    :class="backgroundClass"
                                />
                            </div>
                            <div class="ml-2 text-sm">
                                <label
                                    :for="`helper-radio-${index}`"
                                    class="font-medium cursor-pointer text-gray-900"
                                >
                                    <div>{{ title }}</div>
                                    <p
                                        :id="`helper-radio-${index}`"
                                        class="text-xs font-normal text-gray-500"
                                    >
                                        {{ description }}
                                    </p>
                                </label>
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
        title: {
            required: true,
            type: String,
        },
        name: {
            required: true,
            type: String,
        },
        modelValue: {
            required: true,
        },
        position: {
            required: false,
            type: String,
            default: "right",
        },
    },
    setup(props, { emit }) {
        const showDropdown = ref(false);

        const selectedItem = computed(() => {
            return props.data.find(({ value }) => value === props.modelValue);
        });

        const buttonColorClass = computed(() => {
            return `${selectedItem.value?.backgroundClass ?? "bg-grey-600"}`;
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
            changeValue,
            buttonElement,
            dropdownElement,
            buttonColorClass,
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
