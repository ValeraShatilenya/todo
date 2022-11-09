import { inject, reactive, watch } from "vue";
import type { Ref } from "vue";

import axios from "axios";
// utils
import downloadBlob from "../utils/downloadBlob.utils";
import prepareFormData from "../utils/prepareFormData.utils";
// constants
import { PER_PAGE } from "../constants";
// notification
import { injectionKeyNotifier } from "../components/notification/custom-notifier";
import { INotify } from "../components/notification/interfaces";
// interfaces
import { ITask, IFile, Types, IMainTaskData } from "./taskInterfaces";
import { ITaskGroup } from "./groupInterfaces";

export default function (selectedTask: Ref<ITask | null>, selectedGroup: Ref<ITaskGroup | null>): IMainTaskData {
    const notify = inject(injectionKeyNotifier) as INotify;

    const data = reactive({
        completed: { data: [], page: 1, total: 0, sort: "dateTime" },
        notCompleted: { data: [], page: 1, total: 0, sort: "dateTime" },
    });

    const getNotCompleted = async (): Promise<any> => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            const { data: tasks } = await axios.get(
                `/group-task/${selectedGroup.value.id}/notCompleted`,
                {
                    params: {
                        page: data.notCompleted.page,
                        perPage: PER_PAGE,
                        sort: data.notCompleted.sort,
                    },
                }
            );
            data.notCompleted.data = tasks.data;
            data.notCompleted.total = tasks.total;
        } catch (e) {
            notify({
                message: "Ошибка получения не выполненных задач!",
                type: "error",
            });
        }
    };

    const getCompleted = async (): Promise<any> => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            const { data: tasks } = await axios.get(
                `/group-task/${selectedGroup.value.id}/completed`,
                {
                    params: {
                        page: data.completed.page,
                        perPage: PER_PAGE,
                        sort: data.completed.sort,
                    },
                }
            );
            data.completed.data = tasks.data;
            data.completed.total = tasks.total;
        } catch (e) {
            notify({
                message: "Ошибка получения выполненных задач!",
                type: "error",
            });
        }
    };

    const functionByType = {
        [Types.notCompleted]: getNotCompleted,
        [Types.completed]: getCompleted,
    };

    const otherType = {
        [Types.notCompleted]: Types.completed,
        [Types.completed]: Types.notCompleted,
    };

    const downloadTaskFile = async ({ id, name }: IFile): Promise<any> => {
        try {
            const { data } = await axios.get(
                `/group-task/file/${id}/download`,
                {
                    responseType: "arraybuffer",
                }
            );
            downloadBlob(data, name);
        } catch (e) {
            notify({
                message: "Ошибка скачивания данных!",
                type: "error",
            });
        }
    };

    const create = async (take: object): Promise<any> => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            await axios.post(
                `/group-task/group/${selectedGroup.value.id}`,
                prepareFormData(take),
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );
        } catch (e) {
            notify({
                message: "Ошибка создания!",
                type: "error",
            });
        }
        await getNotCompleted();
    };

    const changeCompleted = async (id: number, type: Types): Promise<any> => {
        try {
            await axios.patch(`/group-task/${id}/changeCompleted`, {
                type: type === "notCompleted",
            });
        } catch (e) {
            notify({
                message: "Ошибка обновления!",
                type: "error",
            });
        }
        if (data[type].data.length === 1 && data[type].page > 1) {
            data[type].page--;
        }

        await Promise.all([getCompleted(), getNotCompleted()]);
    };

    const update = async (take: object): Promise<any>  => {
        try {
            if(!selectedTask.value) {
                throw new Error("Задача не выбрана");
            }
            await axios.post(
                `/group-task/${selectedTask.value.id}`,
                prepareFormData(take),
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );
        } catch (e) {
            notify({
                message: "Ошибка обновления!",
                type: "error",
            });
        }
        await getNotCompleted();
    };

    const destroy = async (id: number, type: Types): Promise<any> => {
        try {
            await axios.delete(`/group-task/${id}`);
        } catch (e) {
            notify({
                message: "Ошибка удаления!",
                type: "error",
            });
        }
        if (data[type].data.length === 1 && data[type].page > 1) {
            data[type].page--;
        }
        await functionByType[type]?.();
    };

    const sendPdfToMail = async (): Promise<any> => {
        try {
            if(!selectedGroup.value) {
                throw new Error("Группа не выбрана!");
            }
            await axios.post(`/group-task/${selectedGroup.value.id}/pdf`);
            notify({
                message: "Файл отправлен на почту!",
            });
        } catch (e) {
            notify({
                message: "Ошибка!",
                type: "error",
            });
        }
    };

    return {
        data,
        functionByType,
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
