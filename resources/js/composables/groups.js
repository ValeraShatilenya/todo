import { reactive, ref, watch } from "vue";
import axios from "axios";

export default function (perPage) {
    const selectedGroup = ref({});

    const groups = reactive({ data: [], page: 1, total: 0 });

    const users = ref([]);

    const getGroupsForTasks = async () => {
        try {
            const { data } = await axios.get("/group/forTasks");
            data.unshift({ id: 0, name: "Личный TODO" });
            return data;
        } catch (e) {
            window.alert("Ошибка взятия данных!");
        }
    };

    const getGroups = async () => {
        try {
            const { data } = await axios.get("/group/forGroups", {
                params: {
                    page: groups.page,
                    perPage,
                },
            });
            groups.data = data.data;
            groups.total = data.total;
        } catch (e) {
            window.alert("Ошибка взятия данных!");
        }
    };

    const getUsers = async () => {
        try {
            const { data } = await axios.get(
                `/group/${selectedGroup.value.id}/users`
            );
            users.value = data;
        } catch (e) {
            window.alert("Ошибка взятия данных!");
        }
    };

    const create = async (take) => {
        try {
            await axios.post("/group", take);
        } catch (e) {
            window.alert("Ошибка создания!");
        }
        await getGroups();
    };

    const addUser = async (email) => {
        try {
            await axios.post(`/group/${selectedGroup.value.id}/user`, {
                email,
            });
        } catch (e) {
            window.alert("Ошибка добавления!");
        }
        await getUsers();
    };

    const deleteUser = async (userId) => {
        try {
            await axios.delete(
                `/group/${selectedGroup.value.id}/user/${userId}`
            );
        } catch (e) {
            window.alert("Ошибка удаления!");
        }
        await getUsers();
    };

    const update = async (take) => {
        try {
            await axios.patch(`/group/${selectedGroup.value.id}`, take);
        } catch (e) {
            window.alert("Ошибка обновления!");
        }
        await getGroups();
    };

    const destroy = async (id) => {
        try {
            await axios.delete(`/group/${id}`);
        } catch (e) {
            window.alert("Ошибка удаления!");
        }
        if (groups.data.length === 1 && groups.page > 1) {
            groups.page--;
        } else await getGroups();
    };

    const unselectGroup = () => {
        selectedGroup.value = {};
        users.value = [];
    };

    watch(
        () => groups.page,
        () => {
            unselectGroup();
            getGroups();
        }
    );

    watch(selectedGroup, () => {
        if (selectedGroup.value.id) {
            getUsers(selectedGroup.value.id);
        }
    });

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
