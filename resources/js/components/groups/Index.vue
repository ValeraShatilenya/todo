<template>
    <div>
        <div class="mb-6">
            <update-group
                :group="selectedGroup"
                @create="onCreate"
                @update="onUpdate"
                @unselectGroup="unselectGroup"
            />
        </div>

        <div
            class="overflow-hidden overflow-x-auto break-words min-w-full grid md:grid-cols-2 gap-6 align-middle sm:rounded-md"
        >
            <div>
                <h2 class="text-2xl font-medium tracking-wider text-center">
                    Группы
                </h2>
                <hr class="my-2" />
                <pagination
                    v-model:page="groups.page"
                    :total="groups.total"
                    :totalCurrent="groups.data.length"
                    class="flex justify-center"
                />
                <hr class="my-2" />
                <template v-for="group in groups.data" :key="group.id">
                    <main-item
                        :class="
                            selectedGroup === group
                                ? 'bg-gray-100'
                                : 'bg-gray-50'
                        "
                    >
                        <template #title>
                            {{ group.name }}
                        </template>
                        <template #description>
                            {{ group.description }}
                        </template>
                        <template #buttons>
                            <button
                                title="Выбрать"
                                class="py-2 w-10 border border-transparent h-max rounded-xl text-white focus:outline-none focus:ring transition ease-in-out duration-150"
                                :class="
                                    selectedGroup === group
                                        ? 'bg-red-400 hover:bg-red-500 active:bg-red-700 focus:border-red-600 ring-red-400'
                                        : 'bg-green-600 hover:bg-green-700 active:bg-green-900 focus:border-green-900 ring-green-400'
                                "
                                @click="onSelectGroup(group)"
                            >
                                <font-awesome-icon
                                    :icon="`fa-solid fa-${
                                        selectedGroup === group
                                            ? 'times'
                                            : 'check'
                                    }`"
                                    size="sm"
                                />
                            </button>
                            <template v-if="group.canEdit">
                                <button
                                    title="Удалить"
                                    @click="destroy(group.id)"
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
                            Создано:
                            {{ new Date(group.dateTime).toLocaleDateString() }}
                        </template>
                    </main-item>
                    <hr class="my-2" />
                </template>
            </div>
            <div>
                <h2 class="text-2xl font-medium tracking-wider text-center">
                    Пользователи
                </h2>
                <hr class="my-2" />
                <div class="flex space-x-2 px-1">
                    <input
                        v-model.trim="userSearch"
                        :disabled="!selectedGroup"
                        type="text"
                        placeholder="Введите email..."
                        class="px-3 py-2 rounded-xl shadow w-full border border-gray-300 hover:border-gray-400 focus:border-gray-500 focus:outline-none disabled:opacity-50"
                    />
                    <template v-if="selectedGroup?.canEdit">
                        <button
                            title="Добавить"
                            :disabled="!userSearch"
                            @click="onAddUser"
                            class="px-3 py-2 bg-purple-500 border border-transparent rounded-xl text-white disabled:opacity-50 hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 transition ease-in-out duration-150"
                        >
                            Добавить
                        </button>
                    </template>
                </div>
                <hr class="my-2" />
                <pagination
                    v-model:page="userPage"
                    :total="filteredUsersSearch.length"
                    :totalCurrent="filteredUsers.length"
                    class="flex justify-center"
                />
                <hr class="my-2" />
                <template v-for="user in filteredUsers" :key="user.id">
                    <main-item class="bg-gray-50">
                        <template #title>
                            {{ user.name }}
                        </template>
                        <template #description>
                            {{ user.email }}
                        </template>
                        <template #buttons>
                            <template v-if="selectedGroup?.canEdit">
                                <button
                                    title="Удалить"
                                    @click="onDeleteUser(user.id)"
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
                            <template v-if="user.isAdmin">
                                Администратор
                            </template>
                        </template>
                    </main-item>
                    <hr class="my-2" />
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import UpdateGroup from "./UpdateGroup.vue";
import LegendLabel from "../LegendLabel.vue";
import Pagination from "../Pagination.vue";
import MainItem from "../MainItem.vue";

import { PER_PAGE } from "../../constants";

import useGroups from "../../composables/groups";
import { computed, ComputedRef, onMounted, Ref, ref, watch } from "vue";

import { IGroup, IUsers } from "../../composables/groupInterfaces";

export default {
    components: {
        UpdateGroup,
        LegendLabel,
        Pagination,
        MainItem,
    },
    setup() {
        const {
            groups,
            users,
            selectedGroup,
            unselectGroup,
            getGroups,
            create,
            addUser,
            deleteUser,
            update,
            destroy,
        } = useGroups();

        const userSearch: Ref<string> = ref("");
        const userPage: Ref<number> = ref(1);

        onMounted(async () => {
            await getGroups();
        });

        const filteredUsersSearch: ComputedRef<IUsers> = computed(() => {
            const search = userSearch.value.replace(
                /[.*+?^${}()|[\]\\]/g,
                "\\$&"
            );
            const patter = new RegExp(search, "i");
            return users.value.filter(
                ({ name, email }) => patter.test(name) || patter.test(email)
            );
        });

        const filteredUsers: ComputedRef<IUsers> = computed(() => {
            return filteredUsersSearch.value.slice(
                PER_PAGE * (userPage.value - 1),
                PER_PAGE * userPage.value
            );
        });

        watch(userSearch, () => {
            userPage.value = 1;
        });

        const onCreate = async (data: object) => {
            await create(data);
        };

        const onUpdate = async (data: object) => {
            await update(data);
            // const index = groups.data.findIndex(
            //     ({ id }) => id === selectedGroup.value.id
            // );
            // if (index > -1) {
            //     selectedGroup.value = groups.data[index];
            // } else {
            //     selectedGroup.value = {};
            // }
            selectedGroup.value = null;
        };

        const onAddUser = async () => {
            await addUser(userSearch.value);
            userSearch.value = "";
        };

        const onDeleteUser = async (id: number) => {
            await deleteUser(id);
            if (filteredUsers.value.length === 0 && userPage.value > 1) {
                userPage.value--;
            }
        };

        const onSelectGroup = (group: IGroup) => {
            selectedGroup.value === group
                ? unselectGroup()
                : (selectedGroup.value = group);
        };

        return {
            groups,
            filteredUsers,
            filteredUsersSearch,
            userPage,
            userSearch,
            selectedGroup,
            onSelectGroup,
            unselectGroup,
            onCreate,
            onUpdate,
            onDeleteUser,
            onAddUser,
            destroy,
        };
    },
};
</script>
