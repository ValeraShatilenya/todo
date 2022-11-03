require("./bootstrap");

require("alpinejs");

import { createApp } from "vue";
import router from "./router/index";
import Notifier from "./components/notification/custom-notifier";
import Confirm from "./components/confirm/custom-confirm";
import App from './components/App.vue'

/* import the fontawesome core */
import { library } from "@fortawesome/fontawesome-svg-core";
/* import font awesome icon component */
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
/* import specific icons */
import { fas } from "@fortawesome/free-solid-svg-icons";
/* add icons to the library */
library.add(fas);

const app: HTMLInputElement | null = document.querySelector("#app");
let userId: number = 0;
if(app) {
    userId = +(app?.dataset?.auth || 0);
    userId && app.removeAttribute("data-auth");
}

createApp(App)
    .use(router(userId))
    .use(Notifier)
    .use(Confirm)
    .component("font-awesome-icon", FontAwesomeIcon)
    .mount("#app");
