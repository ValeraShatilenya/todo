<template>
    <div class="flex flex-col space-y-4">
        <input
            v-model.trim="name"
            :disabled="notAccess"
            type="text"
            placeholder="Заголовок..."
            class="py-3 px-4 rounded-xl shadow-md border border-gray-300 disabled:opacity-50 hover:border-gray-400 focus:border-gray-500 focus:outline-none"
        />
        <textarea
            v-model.trim="description"
            :disabled="notAccess"
            placeholder="Описание..."
            class="py-3 px-4 rounded-xl shadow-md border border-gray-300 disabled:opacity-50 hover:border-gray-400 focus:border-gray-500 focus:outline-none"
        />

        <div class="flex space-x-4 text-xs text-white tracking-wider">
            <button
                v-if="group"
                class="w-24 py-2 uppercase rounded-xl focus:outline-none focus:ring transition ease-in-out duration-150 disabled:opacity-50 bg-purple-500 hover:bg-purple-700 active:bg-purple-900 focus:border-purple-900 ring-purple-300"
                :disabled="isNotValidData || notAccess"
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
                :disabled="(!name && !description) || notAccess"
                @click="onClickCleanButton"
            >
                Очистить
            </button>
            <button
                v-if="group"
                class="w-24 py-2 uppercase bg-gray-500 rounded-xl hover:bg-gray-600 active:bg-gray-700 focus:outline-none disabled:opacity-50 focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150"
                @click="onClickCancelButton"
            >
                Отменить
            </button>
        </div>
    </div>
</template>

<script lang="ts">
import {
    computed,
    ComputedRef,
    defineComponent,
    Ref,
    ref,
    watchEffect,
} from "vue";

export default {
    props: {
        group: {
            required: false,
            type: [Object, null],
            default: null,
        },
    },
    emits: ["create", "update", "unselectGroup"],
    setup(props: any, { emit }: any) {
        const name: Ref<string> = ref("");
        const description: Ref<string> = ref("");

        const clean = () => {
            name.value = "";
            description.value = "";
        };

        watchEffect(() => {
            if (props.group) {
                name.value = props.group.name;
                description.value = props.group.description;
            } else {
                clean();
            }
        });

        const notAccess: ComputedRef<boolean> = computed(() => {
            return props.group && !props.group.canEdit;
        });

        const isNotValidData: ComputedRef<boolean> = computed(() => {
            return !name.value || !description.value;
        });

        const onClickCleanButton = () => {
            clean();
        };

        const onClickCreateButton = (): void => {
            if (!name.value) {
                return;
            }
            emit("create", {
                name: name.value,
                description: description.value,
            });
            clean();
        };

        const onClickUpdateButton = (): void => {
            if (isNotValidData.value || notAccess.value) {
                return;
            }
            emit("update", {
                name: name.value,
                description: description.value,
            });
            clean();
        };

        const onClickCancelButton = (): void => {
            emit("unselectGroup");
        };

        return {
            name,
            description,
            isNotValidData,
            notAccess,
            onClickCleanButton,
            onClickCreateButton,
            onClickCancelButton,
            onClickUpdateButton,
        };
    },
};
</script>
