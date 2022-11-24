<template>
    <div>
        <div class="mb-3 flex gap-3">
            <legend-label name="Группа" class="rounded-lg w-full">
                <div
                    class="relative rounded-lg shadow-md border border-gray-300 text-gray-900 text-sm focus:border-blue-100 block"
                >
                    <select
                        v-model="selectedGroup"
                        class="w-full py-2 px-3 rounded-lg appearance-none focus:outline-none"
                    >
                        <option
                            v-for="group in groups"
                            :key="group.id"
                            :value="group"
                        >
                            {{ group.name }}
                        </option>
                    </select>
                    <font-awesome-icon
                        class="absolute right-3 top-3"
                        icon="fa-solid fa-chevron-down"
                        size="sm"
                    />
                </div>
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
                @unselectTask="selectedTask = null"
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
                    :page="tasks.data.notCompleted.page"
                    :total="tasks.data.notCompleted.total"
                    :totalCurrent="tasks.data.notCompleted.data.length"
                    class="flex justify-center"
                    @change="(page) => onChangePage(page, Types.notCompleted)"
                />
                <dropdown-sort
                    :data="taskSorts"
                    v-model="tasks.data.notCompleted.sort"
                    class="flex justify-end mt-1"
                />
                <hr class="mb-2" />
                <template
                    v-for="task in tasks.data.notCompleted.data"
                    :key="task.id"
                >
                    <main-item
                        :status="task.status"
                        :statuses="statusesByValue"
                        :class="
                            selectedTask === task ? 'bg-gray-100' : 'bg-gray-50'
                        "
                    >
                        <template #title>
                            {{ task.title }}
                        </template>
                        <template #description>
                            {{ task.description }}
                        </template>
                        <p
                            v-if="task.files.length"
                            class="flex flex-wrap gap-1 mt-1"
                        >
                            <template v-for="file in task.files" :key="file.id">
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
                        <template #buttons>
                            <button
                                title="Выполнено"
                                class="px-3 py-2 bg-green-600 border border-transparent h-max rounded-xl text-white hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-400 transition ease-in-out duration-150"
                                @click="
                                    onChangeCompleted(
                                        task.id,
                                        Types.notCompleted
                                    )
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
                                    @click="onDelete(task, Types.notCompleted)"
                                    class="px-3 py-2 bg-red-500 border border-transparent h-max rounded-xl text-white hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 transition ease-in-out duration-150"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-trash"
                                        size="sm"
                                    />
                                </button>
                            </template>
                        </template>
                        <template #footer>
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
                        </template>
                    </main-item>
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
                    :totalCurrent="tasks.data.completed.data.length"
                    class="flex justify-center"
                    @change="(page) => onChangePage(page, Types.completed)"
                />
                <dropdown-sort
                    :data="taskSorts"
                    v-model="tasks.data.completed.sort"
                    class="flex justify-end mt-1"
                />
                <hr class="mb-2" />
                <template
                    v-for="task in tasks.data.completed.data"
                    :key="task.id"
                >
                    <main-item
                        :status="task.status"
                        :statuses="statusesByValue"
                        class="bg-gray-50"
                    >
                        <template #title>
                            {{ task.title }}
                        </template>
                        <template #description>
                            {{ task.description }}
                        </template>

                        <p
                            v-if="task.files.length"
                            class="flex flex-wrap gap-1 mt-1"
                        >
                            <template v-for="file in task.files" :key="file.id">
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

                        <template #buttons>
                            <button
                                class="px-3 py-2 bg-green-600 border border-transparent h-max rounded-xl text-white hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-400 transition ease-in-out duration-150"
                                @click="
                                    onChangeCompleted(task.id, Types.completed)
                                "
                            >
                                <font-awesome-icon
                                    icon="fa-solid fa-hand-point-left"
                                    size="sm"
                                />
                            </button>
                            <template v-if="canEdit(task)">
                                <button
                                    @click="onDelete(task, Types.completed)"
                                    class="px-3 py-2 bg-red-500 border border-transparent h-max rounded-xl text-white hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 transition ease-in-out duration-150"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-trash"
                                        size="sm"
                                    />
                                </button>
                            </template>
                        </template>
                        <template #footer>
                            <template v-if="task.completed_user">
                                <template
                                    v-if="task.completed_user.id === userId"
                                >
                                    Выполнил(-а):
                                    <strong class="text-purple-500">Вы</strong>
                                </template>
                                <template v-else>
                                    Выполнил(-а): {{ task.completed_user.name }}
                                </template>
                            </template>
                            <template v-else> Выполнено: </template>
                            {{ new Date(task.dateTime).toLocaleDateString() }}
                        </template>
                    </main-item>
                    <hr class="my-2" />
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { computed, inject, onMounted, ref, watch } from "vue";
import type { Ref } from "vue";

import UpdateTask from "./UpdateTask.vue";
import LegendLabel from "../LegendLabel.vue";
import Pagination from "../Pagination.vue";
import MainItem from "../MainItem.vue";
import DropdownSort from "../DropdownSort.vue";

import { IStatuse, STATUSES, TASK_SORTS } from "../../constants";

import useTasks from "../../composables/tasks";
import useGroups from "../../composables/groups";
import useGroupTasks from "../../composables/groupTasks";

import { injectionKeyConfirm } from "../confirm/custom-confirm";
import { IConfirm } from "../confirm/interfaces";

import { injectionKeyLoading } from "../loading/custom-loading";
import { ILoading } from "../loading/interfaces";

import { ITaskGroup } from "../../composables/groupInterfaces";
import { IMainTaskData, ITask, Types } from "../../composables/taskInterfaces";

import useDownloadTaskFile from "../../composables/downloadTaskFile";

export default {
    components: {
        UpdateTask,
        LegendLabel,
        Pagination,
        MainItem,
        DropdownSort,
    },
    props: {
        userId: {
            type: Number,
            required: true,
            default: 0,
        },
    },
    setup(props: any) {
        const selectedTask: Ref<ITask | null> = ref(null);
        const selectedGroup: Ref<ITaskGroup | null> = ref(null);
        const tasks: IMainTaskData = useTasks(selectedTask);

        const groups: Ref = ref([]);
        const { getGroupsForTasks: getGroups } = useGroups();

        const groupTasks: IMainTaskData = useGroupTasks(
            selectedTask,
            selectedGroup
        );

        const downloadTaskFile = useDownloadTaskFile();

        interface IStatuseObject {
            [key: number]: IStatuse;
        }
        const statusesByValue = STATUSES.reduce(
            (obj, status) => ((obj[status.value] = status), obj),
            <IStatuseObject>{}
        );

        const confirm = inject(injectionKeyConfirm) as IConfirm;
        const loading = inject(injectionKeyLoading) as ILoading;

        onMounted(async () => {
            groups.value = await getGroups();
            selectedGroup.value = groups.value[0];
        });

        const computedTasks = computed(() => {
            return selectedGroup.value?.id ? groupTasks : tasks;
        });

        watch(selectedGroup, async () => {
            loading.open();
            selectedTask.value = null;
            await Promise.all([
                computedTasks.value.getNotCompleted(),
                computedTasks.value.getCompleted(),
            ]);
            loading.close();
        });

        const onCreate = async (data: object) => {
            loading.open();
            await computedTasks.value.create(data);
            loading.close();
        };

        const onUpdate = async (data: object) => {
            loading.open();
            await computedTasks.value.update(data);
            selectedTask.value = null;
            loading.close();
        };

        const onDelete = ({ id, title }: ITask, type: Types) => {
            confirm({
                confirmText: "Уверен",
                confirmColorClass:
                    "bg-red-600 hover:bg-red-700 active:bg-red-800 focus:border-red-900",
                title: "Удаление задачи",
                message: `Вы уверены, что желаете удалить задачу с заголовком <b>${title}</b>?`,
                onConfirm: async () => {
                    if (selectedTask.value?.id === id)
                        selectedTask.value = null;
                    loading.open();
                    await computedTasks.value.destroy(id, type);
                    loading.close();
                },
            });
        };

        const onChangeCompleted = async (id: number, type: Types) => {
            loading.open();
            await computedTasks.value.changeCompleted(id, type);
            loading.close();
        };

        const canEdit = (task: ITask) => {
            return !selectedGroup.value?.id || task.canEdit;
        };

        const onSendPdfToMail = async () => {
            await computedTasks.value.sendPdfToMail();
        };

        const onChangePage = async (newPage: number, type: Types) => {
            loading.open();
            computedTasks.value.data[type].page = newPage;
            await computedTasks.value.functionByType[type]();
            selectedTask.value = null;
            loading.close();
        };

        return {
            Types: Types,
            statusesByValue: statusesByValue,
            taskSorts: TASK_SORTS,
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
            onChangePage,
        };
    },
};
</script>
