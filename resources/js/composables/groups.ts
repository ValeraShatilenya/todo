import { inject, reactive, ref, watch } from "vue";
import type { Ref } from "vue";
import axios from "axios";

import { PER_PAGE } from "../constants";

import { IGroup, IGroups, ITaskGroup, IUsers } from "./groupInterfaces";
import { INotify } from "../components/notification/interfaces";
import { injectionKeyNotifier } from "../components/notification/custom-notifier";

export default function () {
    const notify = inject(injectionKeyNotifier) as INotify;

    const selectedGroup: Ref<IGroup | null> = ref(null);

    const groups: {data: IGroups, page: number, total: number} = reactive({ data: [], page: 1, total: 0 });

    const users: Ref<IUsers>  = ref([]);

    const getGroupsForTasks = async () => {
        let data: ITaskGroup[] = [];
        try {
            const { data: groups } = await axios.get("/group/forTasks");
            data = groups;
        } catch (e) {
            notify({
                message: "Ошибка взятия данных!",
                type: "error",
            });
        }
        data.unshift({ id: 0, name: "Личный TODO" });
        return data;
    };

    const getGroups = async () => {
        try {
            const { data } = await axios.get("/group/forGroups", {
                params: {
                    page: groups.page,
                    perPage: PER_PAGE,
                },
            });
            groups.data = data.data;
            groups.total = data.total;
        } catch (e) {
            groups.data = [];
            groups.total = 0;
            notify({
                message: "Ошибка взятия данных!",
                type: "error",
            });
        }
    };

    const getUsers = async () => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            const { data } = await axios.get(
                `/group/${selectedGroup.value.id}/users`
                );
                users.value = data;
            } catch (e) {
            users.value = [];
            notify({
                message: "Ошибка взятия пользователей!",
                type: "error",
            });
        }
    };

    const create = async (take: object) => {
        try {
            await axios.post("/group", take);
        } catch (e) {
            notify({
                message: "Ошибка создания!",
                type: "error",
            });
        }
        await getGroups();
    };

    const addUser = async (email: string) => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            await axios.post(`/group/${selectedGroup.value.id}/user`, {
                email,
            });
        } catch (e) {
            notify({
                message: "Ошибка добавления!",
                type: "error",
            });
        }
        await getUsers();
    };

    const deleteUser = async (userId: number) => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            await axios.delete(
                `/group/${selectedGroup.value.id}/user/${userId}`
            );
        } catch (e) {
            notify({
                message: "Ошибка удаления пользователя!",
                type: "error",
            });
        }
        await getUsers();
    };

    const update = async (take: object) => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            await axios.patch(`/group/${selectedGroup.value.id}`, take);
        } catch (e) {
            notify({
                message: "Ошибка обновления!",
                type: "error",
            });
        }
        await getGroups();
    };

    const destroy = async (id: number) => {
        try {
            await axios.delete(`/group/${id}`);
        } catch (e) {
            notify({
                message: "Ошибка удаления!",
                type: "error",
            });
        }
        if (groups.data.length === 1 && groups.page > 1) {
            groups.page--;
        }
        await getGroups();
    };

    const unselectGroup = () => {
        selectedGroup.value = null;
        users.value = [];
    };

    return {
        groups,
        users,
        selectedGroup,
        unselectGroup,
        getGroupsForTasks,
        getGroups,
        getUsers,
        create,
        addUser,
        deleteUser,
        update,
        destroy,
    };
}
