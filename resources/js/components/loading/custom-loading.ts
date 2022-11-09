import { App, createApp } from "vue";
import Loading from "./Loading.vue";
import { CustomComponentPublicInstance } from "./interfaces";

export const injectionKeyLoading = Symbol("loading");

export default {
    install(app: App) {
        const mountPoint = document.createElement("div");
        document.body.appendChild(mountPoint);

        const loading = createApp(Loading).mount(mountPoint) as CustomComponentPublicInstance;

        app.provide(injectionKeyLoading, {open: loading.open, close: loading.close});
    },
};
