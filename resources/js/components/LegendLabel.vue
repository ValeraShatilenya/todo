<template>
    <div class="relative">
        <label
            class="absolute block truncate left-4 text-xs bg-white z-10 px-px -top-2"
            >{{ name }}
        </label>
        <slot />
    </div>
</template>

<script lang="ts">
export default {
    props: {
        name: {
            type: String,
            required: false,
            default: "",
        },
    },
};
</script>

<style scoped>
label {
    max-width: calc(100% - 2em);
}
label::before {
    content: "";
    display: block;
    position: absolute;
    top: 0.65em;
    left: 0;
    right: 0;
    height: 0.375em;
    background-color: white;
    z-index: -1;
}
</style>
