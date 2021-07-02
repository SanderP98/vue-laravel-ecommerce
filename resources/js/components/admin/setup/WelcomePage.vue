<template>
    <div>
        <Card>
            <template #title>
                Welcome
            </template>
            <template #content>
                <p class="p-text-secondary">Before your store is ready, you'll need to define some more information. Click on "next" to proceed.</p>
            </template>
            <template #footer>
                <div class="p-grid p-nogutter p-justify-end">
                    <Button label="Next" @click="nextPage()" icon="pi pi-angle-right" iconPos="right"/>
                </div>
            </template>
        </Card>
    </div>
</template>

<script>

export default {
    methods : {
        nextPage() {
            this.$emit('nextPage', {pageIndex: 0});
        },
    }
}
</script>


