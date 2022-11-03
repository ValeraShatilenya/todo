import { createApp } from "vue";
import Notification from "./Notification.vue";

export const injectionKeyNotifier = Symbol("notification");

export default {
    install(app) {
        const mountPoint = document.createElement("div");
        document.body.appendChild(mountPoint);

        const notification = createApp(Notification).mount(mountPoint);

        app.provide(injectionKeyNotifier, notification.notify);
    },
};
