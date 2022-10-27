<template>
    <div>
        <div class="flex items-center">
            <button
                class="py-2 px-4 text-sm font-medium text-white bg-gray-800 rounded-l hover:bg-gray-900 disabled:opacity-50"
                :disabled="isFirstPage"
                @click="onClickPrev"
            >
                <font-awesome-icon icon="fa-solid fa-chevron-left" size="sm" />
            </button>
            <div
                class="flex items-center px-3 gap-1 text-sm text-gray-700 border h-full"
            >
                <template v-if="totalCurrent">
                    Показано с
                    <div class="font-semibold text-gray-900">
                        {{ perPage * (page - 1) + 1 }}
                    </div>
                    по
                    <div class="font-semibold text-gray-900">
                        {{ perPage * (page - 1) + totalCurrent }}
                    </div>
                    из
                    <div class="font-semibold text-gray-900">
                        {{ total }}
                    </div>
                    Записей
                </template>
                <template v-else> Нет записей! </template>
            </div>
            <button
                class="py-2 px-4 text-sm font-medium text-white bg-gray-800 rounded-r border-0 border-l border-gray-700 hover:bg-gray-900 disabled:opacity-50"
                :disabled="isLastPage"
                @click="onClickNext"
            >
                <font-awesome-icon icon="fa-solid fa-chevron-right" size="sm" />
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        total: {
            type: Number,
            required: true,
        },
        totalCurrent: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            default: 15,
        },
        page: {
            type: Number,
            required: true,
        },
    },
    computed: {
        isLastPage() {
            return this.page >= Math.ceil(this.total / this.perPage);
        },
        isFirstPage() {
            return this.page === 1;
        },
    },
    methods: {
        onClickNext() {
            if (!this.isLastPage) this.onChangePage(this.page + 1);
        },
        onClickPrev() {
            if (!this.isFirstPage) this.onChangePage(this.page - 1);
        },
        onChangePage(page) {
            this.$emit("update:page", page);
        },
    },
};
</script>
