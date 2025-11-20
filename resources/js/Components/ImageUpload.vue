<script setup>
import {ref, defineProps, defineEmits, watch} from 'vue';

const props = defineProps({
    existingImage: {
        type: String,
        required: false,
        default: null
    }
});

const emit = defineEmits();

const imageFile = ref(null);
const imagePreview = ref(null);
const error = ref(null);

const handleImageChange = (e) => {
    const file = e.target.files[0];

    if (file) {
        if (!file.type.startsWith('image/')) {
            error.value = 'Please select a valid image file.';
            imageFile.value = null;
            imagePreview.value = null;
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            error.value = 'File size should be less than 2MB.';
            imageFile.value = null;
            imagePreview.value = null;
            return;
        }

        error.value = null;

        imageFile.value = file;

        const reader = new FileReader();
        reader.onload = () => {
            imagePreview.value = reader.result;
        };
        reader.readAsDataURL(file);

        emit('update:image', file);
    }
};

watch(() => props.existingImage, (newImage) => {
    if (newImage) {
        imagePreview.value = newImage;
    }
});

if (props.existingImage) {
    imagePreview.value = props.existingImage;
}
</script>

<template>
    <div class="mt-1 block w-full p-2 border rounded-md">
        <input
            type="file"
            accept="image/*"
            @change="handleImageChange"
            :class="{'border-red-500': error}"
        />

        <div v-if="imagePreview || existingImage" class="mt-4">
            <img :src="imagePreview || existingImage" alt="Image Preview" class="max-w-full h-auto"/>
        </div>

        <span v-if="error" class="text-red-500 text-sm">{{ error }}</span>
    </div>
</template>

<style scoped>
</style>
