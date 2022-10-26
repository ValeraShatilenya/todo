import { createRouter, createWebHistory } from "vue-router";

import Task from "../components/tasks/Index.vue";
import Group from "../components/groups/Index.vue";

export default (props) =>
    createRouter({
        history: createWebHistory(),
        routes: [
            {
                path: "/",
                component: Task,
                name: "task",
                props: {
                    perPage: props.perPage,
                    userId: props.userId,
                },
            },
            {
                path: "/group",
                component: Group,
                name: "group",
                props: {
                    perPage: props.perPage,
                },
            },
            {
                path: "/:catchAll(.*)",
                redirect: { name: "task" },
            },
        ],
    });
