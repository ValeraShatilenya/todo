import { inject } from "vue";

import axios from "axios";
// utils
import downloadBlob from "../utils/downloadBlob.utils";
// notification
import { injectionKeyNotifier } from "../components/notification/custom-notifier";
import { INotify } from "../components/notification/interfaces";

// interfaces
import { IFile } from "./fileInterfaces";

export default function () {
    const notify = inject(injectionKeyNotifier) as INotify;

    return async ({ id, name }: IFile): Promise<any> => {
        try {
            const { data } = await axios.get(`/file/${id}/download`,
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

}
