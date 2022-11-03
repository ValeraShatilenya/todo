import { App, createApp } from "vue";
import Notification from "./Notification.vue";
import { CustomComponentPublicInstance } from "./interfaces";

export const injectionKeyNotifier = Symbol("notification");

export default {
    install(app: App) {
        const mountPoint = document.createElement("div");
        document.body.appendChild(mountPoint);

        const notification = createApp(Notification).mount(mountPoint) as CustomComponentPublicInstance;

        app.provide(injectionKeyNotifier, notification.notify);
    },
};
