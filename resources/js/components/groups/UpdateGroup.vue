<template>
    <div class="flex flex-col space-y-4">
        <template v-if="!group.id || group.canEdit">
            <input
                v-model.trim="name"
                type="text"
                placeholder="Заголовок..."
                class="py-3 px-4 rounded-xl shadow-md border-gray-300 hover:border-gray-400 focus:border-gray-100"
            />
            <textarea
                v-model.trim="description"
                placeholder="Описание..."
                class="py-3 px-4 rounded-xl shadow-md border-gray-300 hover:border-gray-400 focus:border-gray-100"
            />
        </template>
        <template v-else>
            <input
                :value="name"
                disabled
                type="text"
                placeholder="Заголовок..."
                class="py-3 px-4 rounded-xl shadow-md border-gray-300 bg-gray-200 text-gray-500"
            />
            <textarea
                :value="description"
                disabled
                placeholder="Описание..."
                class="py-3 px-4 rounded-xl shadow-md border-gray-300 bg-gray-200 text-gray-500"
            />
        </template>
        <div class="flex space-x-4 text-xs text-white tracking-wider">
            <button
                v-if="group.id"
                class="w-24 py-2 uppercase rounded-xl focus:outline-none focus:ring transition ease-in-out duration-150 disabled:opacity-50 bg-purple-500 hover:bg-purple-700 active:bg-purple-900 focus:border-purple-900 ring-purple-300"
                :disabled="isNotValidData || !group.canEdit"
                @click="onClickUpdateButton"
            >
                Обновить
            </button>
            <button
                v-else
                class="w-24 py-2 uppercase rounded-xl focus:outline-none focus:ring transition ease-in-out duration-150 disabled:opacity-50 bg-green-500 hover:bg-green-700 active:bg-green-900 focus:border-green-900 ring-green-300"
                :disabled="isNotValidData"
                @click="onClickCreateButton"
            >
                Создать
            </button>
            <button
                class="w-24 py-2 uppercase bg-yellow-500 rounded-xl hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none disabled:opacity-50 focus:border-yellow-900 focus:ring ring-yellow-300 transition ease-in-out duration-150"
                :disabled="
                    !(name || description) || (group.id && !group.canEdit)
                "
                @click="onClickCleanButton"
            >
                Очистить
            </button>
            <button
                v-if="group.id"
                class="w-24 py-2 uppercase bg-gray-500 rounded-xl hover:bg-gray-600 active:bg-gray-700 focus:outline-none disabled:opacity-50 focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150"
                :disabled="!group.canEdit"
                @click="onClickCancelButton"
            >
                Отменить
            </button>
        </div>
    </div>
</template>

<script>
import { computed, ref, watchEffect } from "vue";

export default {
    props: {
        group: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },
    emits: ["create", "update", "unselectGroup"],
    setup(props, { emit }) {
        const name = ref("");
        const description = ref("");

        const clean = () => {
            name.value = "";
            description.value = "";
        };

        watchEffect(() => {
            if (props.group.id) {
                name.value = props.group.name;
                description.value = props.group.description;
            } else {
                clean();
            }
        });

        const isNotValidData = computed(() => {
            return !name.value || !description.value;
        });

        const onClickCleanButton = () => {
            clean();
        };

        const onClickCreateButton = () => {
            if (!name.value) {
                return;
            }
            emit("create", {
                name: name.value,
                description: description.value,
            });
            clean();
        };

        const onClickUpdateButton = () => {
            if (!name.value) {
                return;
            }
            emit("update", {
                name: name.value,
                description: description.value,
            });
            clean();
        };

        const onClickCancelButton = () => {
            emit("unselectGroup");
        };

        return {
            name,
            description,
            isNotValidData,
            onClickCleanButton,
            onClickCreateButton,
            onClickCancelButton,
            onClickUpdateButton,
        };
    },
};
</script>

<style scoped>
.h-max {
    height: max-content;
}
</style>
