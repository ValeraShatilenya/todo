import { createRouter, createWebHistory } from "vue-router";

import Task from "../components/tasks/Index.vue";
import Group from "../components/groups/Index.vue";

export default (userId: number) =>
    createRouter({
        history: createWebHistory(),
        routes: [
            {
                path: "/",
                component: Task,
                name: "task",
                props: {
                    userId,
                },
            },
            {
                path: "/group",
                component: Group,
                name: "group",
            },
            {
                path: "/:catchAll(.*)",
                redirect: { name: "task" },
            },
        ],
    });
