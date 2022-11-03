import { App, createApp } from "vue";
import Confirm from "./Confirm.vue";
import { CustomComponentPublicInstance } from "./interfaces";

export const injectionKeyConfirm = Symbol("confirm");

export default {
    install(app: App) {
        const mountPoint = document.createElement("div");
        document.body.appendChild(mountPoint);

        const confirm = createApp(Confirm).mount(mountPoint) as CustomComponentPublicInstance;

        app.provide(injectionKeyConfirm, confirm.open);
    },
};
