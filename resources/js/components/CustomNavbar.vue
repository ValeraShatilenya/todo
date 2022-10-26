<template>
    <div>
        <nav class="bg-white border-b border-gray-100 fixed w-full z-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between md:h-16">
                    <div class="flex">
                        <router-link
                            to="/"
                            tag="button"
                            class="button"
                            active-class="active"
                        >
                            <div class="flex items-center h-full">Главная</div>
                        </router-link>
                        <router-link
                            to="/group"
                            tag="button"
                            class="button"
                            active-class="active"
                        >
                            <div class="flex items-center h-full">Группы</div>
                        </router-link>
                    </div>

                    <div class="flex items-center tracking-widest font-medium">
                        TODO
                    </div>

                    <button class="button" @click="logout">
                        <div class="flex items-center h-full">Выйти</div>
                    </button>
                </div>
            </div>
        </nav>
        <div class="invisible md:h-16 w-full"></div>
    </div>
</template>

<script>
import axios from "axios";
export default {
    props: {
        name: {
            type: String,
            required: false,
            default: "",
        },
    },
    methods: {
        async logout() {
            try {
                await axios.post("/logout");
            } catch (e) {}
            location.href = "/";
        },
    },
};
</script>

<style scoped>
.button {
    cursor: pointer;
    transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
    padding: 1rem 1.5rem;
    position: relative;
}
.button::before,
.button::after {
    content: "";
    top: 0;
    width: 0.75rem;
    height: 100%;
    background: white;
    position: absolute;
}
.button::before {
    left: 0;
    border-radius: 0 0 0.75rem 0;
}
.button::after {
    right: 0;
    border-radius: 0 0 0 0.75rem;
}
.button:hover {
    --tw-bg-opacity: 1;
    background-color: rgba(229, 231, 235, var(--tw-bg-opacity));
}
.button.active {
    --tw-bg-opacity: 1;
    background-color: rgba(243, 244, 246, var(--tw-bg-opacity));
    cursor: not-allowed;
}
@media (max-width: 768px) {
    .button {
        padding: 0.3rem 1.5rem;
    }
}
</style>
