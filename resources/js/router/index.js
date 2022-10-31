import { createRouter, createWebHistory } from "vue-router";

import Task from "../components/tasks/Index.vue";
import Group from "../components/groups/Index.vue";

import { PER_PAGE } from "../constants";

export default (props) =>
    createRouter({
        history: createWebHistory(),
        routes: [
            {
                path: "/",
                component: Task,
                name: "task",
                props: {
                    userId: props.userId,
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
