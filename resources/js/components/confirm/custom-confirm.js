import { createApp } from "vue";
import Confirm from "./Confirm.vue";

export const injectionKeyConfirm = Symbol("confirm");

export default {
    install(app) {
        const mountPoint = document.createElement("div");
        document.body.appendChild(mountPoint);

        const confirm = createApp(Confirm).mount(mountPoint);

        app.provide(injectionKeyConfirm, confirm.open);
    },
};
