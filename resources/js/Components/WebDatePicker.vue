<template>
    <div class="date-picker">
        <VueDatePicker
            inputClassName="form-control date-picker"
            v-model="date"
            :enableTimePicker="false"
            :placeholder="placeholder"
            :hideInputIcon="false"
            :autoApply="true"
            :closeOnAutoApply="false"
            :yearRange="yearRange"
        />
    </div>
</template>

<script setup lang="ts">
import {ref, type PropType} from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import {watch} from "vue";

const props = defineProps({
    placeholder: {
        type: String,
        default: 'កាលបរិច្ឆេប',
    },
    date: {
        type: Date,
    },
    yearRange: {
        type: Array as PropType<number[]>,
    },
});
const emit = defineEmits(['update:model-value']);
const date = ref(props.date);

watch(date, (newValue) => {
    emit('update:model-value', newValue);
});

</script>

<style lang="scss">
@import '@vuepic/vue-datepicker/dist/main.css';

.date-picker {
    font-family: Roboto, 'battambang', sans-serif;
}

.dp__input {
    background-color: #ffffff;
    border-radius: var(--bs-border-radius);
    border-color: #f3f3f3;
}

.date-picker {
    margin-top: 0 !important;
}

.dp__input_icons {
    align-content: end !important;
}
</style>
