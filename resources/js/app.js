require("./bootstrap");

require("alpinejs");

import { createApp } from "vue";
import router from "./router";
import CustomNavbar from "./components/CustomNavbar.vue";

/* import the fontawesome core */
import { library } from "@fortawesome/fontawesome-svg-core";
/* import font awesome icon component */
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
/* import specific icons */
import { fas } from "@fortawesome/free-solid-svg-icons";
/* add icons to the library */
library.add(fas);

const app = document.querySelector("#app");
const userId = +app?.dataset?.auth;
userId && app.removeAttribute("data-auth");
const perPage = 4;

createApp({})
    .use(router({ userId, perPage }))
    .component("font-awesome-icon", FontAwesomeIcon)
    .component("custom-navbar", CustomNavbar)
    .mount("#app");
