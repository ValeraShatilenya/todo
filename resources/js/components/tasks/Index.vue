<template>
    <div class="mb-3 flex gap-3">
        <legend-label name="Группа" class="bg-gray-50 rounded-lg w-full">
            <select
                v-model="selectedGroup"
                class="rounded-lg shadow-md border border-gray-300 text-gray-900 text-sm focus:border-blue-100 block w-full"
            >
                <option v-for="group in groups" :key="group.id" :value="group">
                    {{ group.name }}
                </option>
            </select>
        </legend-label>
        <button
            title="Отправить по почте"
            class="px-2 w-48 text-sm bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-400 transition ease-in-out duration-150"
            @click="onSendPdfToMail"
        >
            Отправить по почте
        </button>
    </div>
    <div class="mb-6">
        <update-task
            :task="selectedTask"
            @create="onCreate"
            @update="onUpdate"
            @unselectTask="selectedTask = {}"
        />
    </div>

    <div
        class="overflow-hidden overflow-x-auto break-words min-w-full grid md:grid-cols-2 gap-6 align-middle sm:rounded-md"
    >
        <div>
            <h2 class="text-2xl font-medium tracking-wider text-center">
                Не выполненные
            </h2>
            <hr class="my-2" />
            <pagination
                v-model:page="tasks.data.notCompleted.page"
                :total="tasks.data.notCompleted.total"
                :perPage="perPage"
                :totalCurrent="tasks.data.notCompleted.data.length"
                class="flex justify-center"
            />
            <hr class="my-2" />
            <template
                v-for="task in tasks.data.notCompleted.data"
                :key="task.id"
            >
                <div
                    class="rounded-xl"
                    :class="
                        selectedTask === task ? 'bg-gray-100' : 'bg-gray-50'
                    "
                >
                    <div class="py-2 px-3 flex">
                        <div class="flex-1 pr-1 max-w-100-40">
                            <h3 class="text-lg font-semibold">
                                {{ task.title }}
                            </h3>
                            <p class="text-gray-500">
                                {{ task.description }}
                            </p>
                            <p
                                v-if="task.files.length"
                                class="flex flex-wrap gap-1 mt-1"
                            >
                                <template
                                    v-for="file in task.files"
                                    :key="file.id"
                                >
                                    <button
                                        title="Скачать"
                                        class="px-2 bg-blue-600 border border-transparent h-max rounded text-white hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-400 transition ease-in-out duration-150"
                                        @click="downloadTaskFile(file)"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-download"
                                            size="sm"
                                        />
                                        <span class="text-xs ml-1">
                                            {{ file.name }}
                                        </span>
                                    </button>
                                </template>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                title="Выполнено"
                                class="px-3 py-2 bg-green-600 border border-transparent h-max rounded-xl text-white hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-400 transition ease-in-out duration-150"
                                @click="
                                    onChangeCompleted(task.id, 'notCompleted')
                                "
                            >
                                <font-awesome-icon
                                    icon="fa-solid fa-hand-point-right"
                                    size="sm"
                                />
                            </button>
                            <template v-if="canEdit(task)">
                                <button
                                    title="Выбрать"
                                    class="px-3 py-2 bg-purple-600 border border-transparent h-max rounded-xl text-white hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-400 transition ease-in-out duration-150"
                                    @click="selectedTask = task"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-pen-to-square"
                                        size="sm"
                                    />
                                </button>
                                <button
                                    title="Удалить"
                                    @click="onDelete(task.id, 'notCompleted')"
                                    class="px-3 py-2 bg-red-500 border border-transparent h-max rounded-xl text-white hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 transition ease-in-out duration-150"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-trash"
                                        size="sm"
                                    />
                                </button>
                            </template>
                        </div>
                    </div>
                    <p class="text-gray-400 text-xs text-right pr-1">
                        <template v-if="task.user">
                            <template v-if="task.user.id === userId">
                                Создал(-а):
                                <strong class="text-purple-500">Вы</strong>
                            </template>
                            <template v-else>
                                Создал(-а): {{ task.user.name }}
                            </template>
                        </template>
                        <template v-else> Создано: </template>
                        {{ new Date(task.dateTime).toLocaleDateString() }}
                    </p>
                </div>
                <hr class="my-2" />
            </template>
        </div>
        <div>
            <h2 class="text-2xl font-medium tracking-wider text-center">
                Выполненные
            </h2>
            <hr class="my-2" />
            <pagination
                v-model:page="tasks.data.completed.page"
                :total="tasks.data.completed.total"
                :perPage="perPage"
                :totalCurrent="tasks.data.completed.data.length"
                class="flex justify-center"
            />
            <hr class="my-2" />
            <template v-for="task in tasks.data.completed.data" :key="task.id">
                <div class="rounded-xl bg-gray-50">
                    <div class="py-2 flex px-3">
                        <div class="flex-1 pr-1 max-w-100-40">
                            <h3 class="text-lg font-semibold">
                                {{ task.title }}
                            </h3>
                            <p class="text-gray-500">
                                {{ task.description }}
                            </p>
                            <p
                                v-if="task.files.length"
                                class="flex flex-wrap gap-1 mt-1"
                            >
                                <template
                                    v-for="file in task.files"
                                    :key="file.id"
                                >
                                    <button
                                        title="Скачать"
                                        class="px-2 bg-blue-600 border border-transparent h-max rounded text-white hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-400 transition ease-in-out duration-150"
                                        @click="downloadTaskFile(file)"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-download"
                                            size="sm"
                                        />
                                        <span class="text-xs ml-1">
                                            {{ file.name }}
                                        </span>
                                    </button>
                                </template>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                class="px-3 py-2 bg-green-600 border border-transparent h-max rounded-xl text-white hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-400 transition ease-in-out duration-150"
                                @click="onChangeCompleted(task.id, 'completed')"
                            >
                                <font-awesome-icon
                                    icon="fa-solid fa-hand-point-left"
                                    size="sm"
                                />
                            </button>
                            <template v-if="canEdit(task)">
                                <button
                                    @click="onDelete(task.id, 'completed')"
                                    class="px-3 py-2 bg-red-500 border border-transparent h-max rounded-xl text-white hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 transition ease-in-out duration-150"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-trash"
                                        size="sm"
                                    />
                                </button>
                            </template>
                        </div>
                    </div>
                    <p class="text-gray-400 text-xs text-right pr-1">
                        <template v-if="task.completed_user">
                            <template v-if="task.completed_user.id === userId">
                                Выполнил(-а):
                                <strong class="text-purple-500">Вы</strong>
                            </template>
                            <template v-else>
                                Выполнил(-а): {{ task.completed_user.name }}
                            </template>
                        </template>
                        <template v-else> Выполнено: </template>
                        {{ new Date(task.dateTime).toLocaleDateString() }}
                    </p>
                </div>
                <hr class="my-2" />
            </template>
        </div>
    </div>
</template>

<script>
import UpdateTask from "./UpdateTask.vue";
import LegendLabel from "../LegendLabel.vue";
import Pagination from "../Pagination.vue";

import useTasks from "../../composables/tasks";
import useGroups from "../../composables/groups";
import useGroupTasks from "../../composables/groupTasks";
import { computed, onMounted, ref, watch } from "vue";

export default {
    components: {
        UpdateTask,
        LegendLabel,
        Pagination,
    },
    props: {
        userId: {
            type: Number,
            required: true,
            default: 0,
        },
        perPage: {
            type: Number,
            default: 15,
        },
    },
    setup(props) {
        const selectedTask = ref({});
        const selectedGroup = ref({});
        const tasks = useTasks(props.perPage, selectedTask);

        const groups = ref([]);
        const { getGroupsForTasks: getGroups } = useGroups();

        const groupTasks = useGroupTasks(
            props.perPage,
            selectedTask,
            selectedGroup
        );

        onMounted(async () => {
            groups.value = await getGroups();
            selectedGroup.value = groups.value[0];
        });

        watch(selectedGroup, () => {
            if (selectedGroup.value.id) {
                groupTasks.getNotCompleted(selectedGroup.value.id);
                groupTasks.getCompleted(selectedGroup.value.id);
            } else {
                tasks.getNotCompleted();
                tasks.getCompleted();
            }
            selectedTask.value = {};
        });

        const computedTasks = computed(() => {
            return selectedGroup.value.id ? groupTasks : tasks;
        });

        const onCreate = async (data) => {
            await computedTasks.value.create(data);
        };

        const onUpdate = async (data) => {
            await computedTasks.value.update(data);
            selectedTask.value = {};
        };

        const onDelete = async (id, type) => {
            if (selectedTask.value.id === id) selectedTask.value = {};
            await computedTasks.value.destroy(id, type);
        };

        const onChangeCompleted = async (id, type) => {
            await computedTasks.value.changeCompleted(id, type);
        };

        const canEdit = (task) => {
            return !selectedGroup.value.id || task.canEdit;
        };

        const downloadTaskFile = (file) => {
            return computedTasks.value.downloadTaskFile(file);
        };

        const onSendPdfToMail = async () => {
            await computedTasks.value.sendPdfToMail();
        };

        return {
            perPage: props.perPage,
            usersId: props.userId,
            groups,
            tasks: computedTasks,
            selectedTask,
            selectedGroup,
            onCreate,
            onUpdate,
            onChangeCompleted,
            canEdit,
            downloadTaskFile,
            onDelete,
            onSendPdfToMail,
        };
    },
};
</script>

<style scoped>
.h-max {
    height: max-content;
}
.max-w-100-40 {
    max-width: calc(100% - 40px);
}
</style>
