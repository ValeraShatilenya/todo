<template>
    <div
        class="z-50 fixed inset-0 pointer-events-none flex justify-center mt-2 md:mt-6"
    >
        <TransitionGroup
            name="list"
            tag="div"
            class="flex flex-col items-center gap-2"
        >
            <div
                v-for="[id, { message, type }] in messages.entries()"
                :key="id"
            >
                <div
                    class="px-5 py-3 rounded-full w-max max-w-full text-white"
                    :class="backgroundClass(type)"
                >
                    <div v-html="message" />
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script>
import { reactive } from "vue";

export default {
    setup() {
        const messages = reactive(new Map());
        let id = 0;

        const notify = (message) => {
            const newId = ++id;
            messages.set(newId, message);
            message.duration ||= 2000;
            message.duration !== "Infinity" &&
                setTimeout(() => {
                    messages.delete(newId);
                }, message.duration);
        };

        const backgroundClass = (type) => {
            if (type === "error") return "bg-red-500/90";
            return "bg-neutral-800/90";
        };

        return {
            messages,
            notify,
            backgroundClass,
        };
    },
};
</script>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
    transition: all 0.2s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateY(-60px);
}

.list-move:first-child:last-child {
    transform: translateX(-50%);
}

.list-leave-active {
    position: absolute;
}
</style>
