<template>
    <div class="flex flex-col space-y-4">
        <div class="flex gap-2">
            <input
                v-model.trim="title"
                type="text"
                placeholder="Заголовок..."
                class="py-3 px-4 w-full rounded-xl shadow-md border border-gray-300 hover:border-gray-400 focus:border focus:border-gray-500 focus:outline-none"
            />
            <dropdown-radio
                v-model="status"
                position="left"
                title="Статус"
                name="status"
                :data="statuses"
            />
        </div>
        <textarea
            v-model.trim="description"
            placeholder="Описание..."
            class="py-3 px-4 rounded-xl shadow-md border border-gray-300 hover:border-gray-400 focus:border focus:border-gray-500 focus:outline-none"
        />
        <div class="relative">
            <input
                @change="onFileChange"
                type="file"
                id="input-file"
                ref="inputFile"
                class="invisible opacity-0 absolute"
            />
            <label
                for="input-file"
                class="py-2 px-3 max-w-max text-xs uppercase rounded-xl border border-gray-300 hover:border-gray-400 focus:border-gray-10 cursor-pointer transition ease-in-out duration-150"
            >
                Нажмите для выбора файла
            </label>
        </div>
        <div v-if="files.length" class="flex flex-wrap gap-1">
            <template v-for="(file, index) in files" :key="index">
                <div
                    class="flex items-center gap-2 py-1 px-2 rounded-xl border border-gray-300"
                >
                    <span class="text-xs">
                        {{ file.name }}
                    </span>
                    <button class="flex" @click="onDeleteFile(index)">
                        <font-awesome-icon icon="fa-solid fa-times" size="sm" />
                    </button>
                </div>
            </template>
        </div>
        <div class="flex space-x-4 text-xs text-white tracking-wider">
            <button
                v-if="task"
                class="w-24 py-2 uppercase rounded-xl focus:outline-none focus:ring transition ease-in-out duration-150 bg-purple-500 hover:bg-purple-700 active:bg-purple-900 focus:border-purple-900 ring-purple-300 disabled:opacity-50"
                :disabled="isNotValidData"
                @click="onClickUpdateButton"
            >
                Обновить
            </button>
            <button
                v-else
                class="w-24 py-2 uppercase rounded-xl focus:outline-none focus:ring transition ease-in-out duration-150 bg-green-500 hover:bg-green-700 active:bg-green-900 focus:border-green-900 ring-green-300 disabled:opacity-50"
                :disabled="isNotValidData"
                @click="onClickCreateButton"
            >
                Создать
            </button>
            <button
                class="w-24 py-2 uppercase bg-yellow-500 rounded-xl hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 transition ease-in-out duration-150 disabled:opacity-50"
                :disabled="!(title || description)"
                @click="onClickCleanButton"
            >
                Очистить
            </button>
            <button
                v-if="task"
                class="w-24 py-2 uppercase bg-gray-500 rounded-xl hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150"
                @click="onClickCancelButton"
            >
                Отменить
            </button>
        </div>
    </div>
</template>

<script lang="ts">
import { computed, Ref, ref, watchEffect } from "vue";
import DropdownRadio from "../DropdownRadio.vue";

import { STATUSES } from "../../constants";
import { IFile } from "../../composables/fileInterfaces";

export default {
    components: {
        DropdownRadio,
    },
    props: {
        task: {
            required: true,
            type: [Object, null],
            default: null,
        },
    },
    emits: ["create", "update", "unselectTask"],
    setup(props: any, { emit }: any) {
        const title = ref("");
        const status = ref(STATUSES.at(-1)?.value);
        const description = ref("");

        const files: Ref<Array<IFile | File>> = ref([]);
        const inputFile: Ref<HTMLInputElement | null> = ref(null);

        const clean = () => {
            title.value = "";
            status.value = STATUSES.at(-1)?.value;
            description.value = "";
            files.value = [];
        };

        watchEffect(() => {
            clean();
            if (props.task) {
                title.value = props.task.title;
                status.value = props.task.status;
                description.value = props.task.description;
                files.value = [...props.task.files];
            }
        });

        const isNotValidData = computed(() => {
            return !title.value || !description.value;
        });

        const onClickCleanButton = () => {
            clean();
        };

        const onClickCreateButton = () => {
            if (isNotValidData.value) {
                return;
            }
            emit("create", {
                title: title.value,
                status: status.value,
                description: description.value,
                files: files.value,
            });
            clean();
        };

        const onClickUpdateButton = () => {
            if (isNotValidData.value) {
                return;
            }
            const newFiles: File[] = [];
            const oldFiles: number[] = [];
            files.value.forEach((file) => {
                "id" in file ? oldFiles.push(file.id) : newFiles.push(file);
            });
            emit("update", {
                title: title.value,
                status: status.value,
                description: description.value,
                files: newFiles,
                oldFiles,
            });
            clean();
        };

        const onClickCancelButton = () => {
            clean();
            emit("unselectTask");
        };

        type HTMLInputFileEvent = Event & {
            target: HTMLInputElement;
            dataTransfer: DataTransfer;
        };

        const onFileChange = (event: HTMLInputFileEvent) => {
            const file =
                event.target.files?.[0] || event.dataTransfer?.files[0];
            files.value.push(file);
            if (inputFile.value) {
                inputFile.value.value = "";
            }
        };

        const onDeleteFile = (index: number) => {
            files.value.splice(index, 1);
        };

        return {
            title,
            statuses: STATUSES,
            status,
            description,
            files,
            inputFile,
            isNotValidData,
            onClickCleanButton,
            onClickCreateButton,
            onClickCancelButton,
            onClickUpdateButton,
            onFileChange,
            onDeleteFile,
        };
    },
};
</script>
