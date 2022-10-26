<template>
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
                :perPage="perPage"
                :totalCurrent="groups.data.length"
                class="flex justify-center"
            />
            <hr class="my-2" />
            <template v-for="group in groups.data" :key="group.id">
                <div
                    class="rounded-xl"
                    :class="
                        selectedGroup === group ? 'bg-gray-100' : 'bg-gray-50'
                    "
                >
                    <div class="py-2 px-3 flex">
                        <div class="flex-1 pr-1 max-w-100-40">
                            <h3 class="text-lg font-semibold">
                                {{ group.name }}
                            </h3>
                            <p class="text-gray-500">
                                {{ group.description }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
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
                        </div>
                    </div>
                    <p class="text-gray-400 text-xs text-right pr-1">
                        Создано:
                        {{ new Date(group.dateTime).toLocaleDateString() }}
                    </p>
                </div>
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
                    :disabled="!selectedGroup.id"
                    type="text"
                    placeholder="Введите email..."
                    class="px-3 py-2 rounded-xl shadow w-full border-gray-300 hover:border-gray-400 focus:border-gray-100 disabled:opacity-50"
                />
                <template v-if="selectedGroup.canEdit">
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
                :perPage="perPage"
                :totalCurrent="filteredUsers.length"
                class="flex justify-center"
            />
            <hr class="my-2" />
            <template v-for="user in filteredUsers" :key="user.id">
                <div class="rounded-xl bg-gray-50">
                    <div class="py-2 px-3 flex">
                        <div class="flex-1 pr-1">
                            <h3 class="text-lg font-semibold">
                                {{ user.name }}
                            </h3>
                            <p class="text-gray-500">email: {{ user.email }}</p>
                        </div>
                        <template v-if="selectedGroup.canEdit">
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
                    </div>
                    <template v-if="user.isAdmin">
                        <p class="text-gray-400 text-xs text-right pr-1">
                            Администратор
                        </p>
                    </template>
                </div>
                <hr class="my-2" />
            </template>
        </div>
    </div>
</template>

<script>
import UpdateGroup from "./UpdateGroup.vue";
import LegendLabel from "../LegendLabel.vue";
import Pagination from "../Pagination.vue";

import useGroups from "../../composables/groups";
import { computed, onMounted, ref, watch } from "vue";

export default {
    components: {
        UpdateGroup,
        LegendLabel,
        Pagination,
    },
    props: {
        perPage: {
            type: Number,
            default: 15,
        },
    },
    setup(props) {
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
        } = useGroups(props.perPage);

        const userSearch = ref("");
        const userPage = ref(1);

        onMounted(async () => {
            await getGroups();
        });

        const filteredUsersSearch = computed(() => {
            const search = userSearch.value.replace(
                /[.*+?^${}()|[\]\\]/g,
                "\\$&"
            );
            const patter = new RegExp(search, "i");
            return users.value.filter(
                ({ name, email }) => patter.test(name) || patter.test(email)
            );
        });

        const filteredUsers = computed(() => {
            return filteredUsersSearch.value.slice(
                props.perPage * (userPage.value - 1),
                props.perPage * userPage.value
            );
        });

        watch(userSearch, () => {
            userPage.value = 1;
        });

        const onCreate = async (data) => {
            await create(data);
        };

        const onUpdate = async (data) => {
            await update(data);
            // const index = groups.data.findIndex(
            //     ({ id }) => id === selectedGroup.value.id
            // );
            // if (index > -1) {
            //     selectedGroup.value = groups.data[index];
            // } else {
            //     selectedGroup.value = {};
            // }
            selectedGroup.value = {};
        };

        const onAddUser = async () => {
            await addUser(userSearch.value);
            userSearch.value = "";
        };

        const onDeleteUser = async (id) => {
            await deleteUser(id);
            if (filteredUsers.value.length === 0 && userPage.value > 1) {
                userPage.value--;
            }
        };

        const onSelectGroup = (group) => {
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

<style scoped>
.h-max {
    height: max-content;
}
.max-w-100-40 {
    max-width: calc(100% - 40px);
}
</style>
