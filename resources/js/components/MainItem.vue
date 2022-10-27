<template>
    <div class="rounded-xl">
        <div class="py-2 flex px-3">
            <div class="flex-1 pr-1 max-w-100-40">
                <h3 class="text-lg font-semibold">
                    <slot name="title" />
                </h3>
                <p class="text-gray-500">
                    <slot name="description" />
                </p>
                <slot />
            </div>
            <div class="flex flex-wrap gap-2">
                <slot name="buttons" />
            </div>
        </div>
        <p class="text-gray-400 text-xs text-right pr-1">
            <slot name="footer" />
        </p>
    </div>
</template>

<script>
export default {};
</script>

<style scoped>
.max-w-100-40 {
    max-width: calc(100% - 40px);
}
</style>
