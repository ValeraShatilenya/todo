import { reactive, watch } from "vue";
import axios from "axios";
import downloadBlob from "../utils/downloadBlob.utils";
import prepareFormData from "../utils/prepareFormData.utils";

import { PER_PAGE } from "../constants";

export default function (selectedTask) {
    console.log(PER_PAGE);

    const data = reactive({
        completed: { data: [], page: 1, total: 0, sort: "dateTime" },
        notCompleted: { data: [], page: 1, total: 0, sort: "dateTime" },
    });

    const getNotCompleted = async () => {
        try {
            const { data: tasks } = await axios.get("/task/notCompleted", {
                params: {
                    page: data.notCompleted.page,
                    perPage: PER_PAGE,
                    sort: data.notCompleted.sort,
                },
            });
            data.notCompleted.data = tasks.data;
            data.notCompleted.total = tasks.total;
        } catch (e) {
            window.alert("Ошибка взятия данных!");
        }
    };

    const getCompleted = async () => {
        try {
            const { data: tasks } = await axios.get("/task/completed", {
                params: {
                    page: data.completed.page,
                    perPage: PER_PAGE,
                    sort: data.completed.sort,
                },
            });
            data.completed.data = tasks.data;
            data.completed.total = tasks.total;
        } catch (e) {
            window.alert("Ошибка взятия данных!");
        }
    };

    const downloadTaskFile = async ({ id, name }) => {
        try {
            const { data } = await axios.get(`/task/file/${id}/download`, {
                responseType: "arraybuffer",
            });
            downloadBlob(data, name);
        } catch (e) {
            window.alert("Ошибка скачивания данных!");
        }
    };

    const functionByType = {
        notCompleted: getNotCompleted,
        completed: getCompleted,
    };

    const create = async (take) => {
        try {
            await axios.post("/task", prepareFormData(take), {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
        } catch (e) {
            console.error(e);
            window.alert("Ошибка создания!");
        }
        await getNotCompleted();
    };

    const changeCompleted = async (id, type) => {
        try {
            await axios.patch(`/task/${id}/changeCompleted`, {
                type: type === "notCompleted",
            });
        } catch (e) {
            window.alert("Ошибка обновления!");
        }
        const otherType = Object.keys(functionByType).find(
            (key) => key !== type
        );
        if (data[type].data.length === 1 && data[type].page > 1) {
            data[type].page--;
            functionByType[otherType]?.();
        } else {
            await Promise.all([getCompleted(), getNotCompleted()]);
        }
    };

    const update = async (take) => {
        try {
            await axios.post(
                `/task/${selectedTask.value.id}`,
                prepareFormData(take),
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );
        } catch (e) {
            window.alert("Ошибка обновления!");
        }
        await getNotCompleted();
    };

    const destroy = async (id, type) => {
        try {
            await axios.delete(`/task/${id}`);
        } catch (e) {
            window.alert("Ошибка удаления!");
        }
        if (data[type].data.length === 1 && data[type].page > 1) {
            data[type].page--;
        } else await functionByType[type]?.();
    };

    const sendPdfToMail = async () => {
        try {
            await axios.post("/task/pdf");
            window.alert("Файл отправлен на почту!");
        } catch (e) {
            window.alert("Ошибка!");
        }
    };

    watch([() => data.notCompleted.page, () => data.notCompleted.sort], () => {
        getNotCompleted();
        selectedTask.value = {};
    });

    watch([() => data.completed.page, () => data.completed.sort], () => {
        getCompleted();
        selectedTask.value = {};
    });

    return {
        data,
        getNotCompleted,
        getCompleted,
        downloadTaskFile,
        create,
        changeCompleted,
        update,
        destroy,
        sendPdfToMail,
    };
}
