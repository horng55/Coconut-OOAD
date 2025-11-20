<script setup>
import {computed, ref} from "vue";

const props = defineProps({
    multiple: Boolean,
    images: Array,
});

const emit = defineEmits(["updateFile"]);

const images = props.multiple ? ref(props.images ?? []) : props.images ? ref([props.images]) : ref([]);
const files = ref([]);

async function readURL(event) {
    const files = event.target.files;
    if (!files) {
        return;
    }

    for (const file of files) {
        const reader = new FileReader();
        await reader.readAsDataURL(file);

        reader.onload = function (e) {
            const newFile = {
                file: file,
                path: e.target.result,
                name: file.name,
            };

            if (props.multiple) {
                images.value.push(newFile);
            }

            if (!props.multiple) {
                images.value = [newFile];
            }
            emit("updateFile", images.value);
        };
    }
}

function removeFile(index) {
    const dt = new DataTransfer();
    const tempFiles = files.value.files;
    for (let i = 0; i < tempFiles.length; i++) {
        const file = tempFiles[i];
        if (index !== i) {
            dt.items.add(file);
        }
    }

    files.value.files = dt.files;
    images.value.splice(index, 1);

    emit("updateFile", images.value);
}

const displayImage = computed(() => {
    return images.value.filter((item) => item?.path);
});
</script>

<template>
    <div class="grid gap-2">
        <div
            v-for="(image, index) in images"
            :key="index"
            class="relative group bg-gray-200 rounded-md shadow-md overflow-hidden">
            <div v-if="image?.path" class="w-full h-full">
                <img
                    :src="image?.path"
                    alt="Uploaded image"
                    class="w-full h-full object-cover rounded-md transition-all duration-300 transform hover:scale-105"
                />
            </div>
            <div v-else class="w-full h-full flex justify-center items-center text-gray-400 text-xl">
                <span>No Image</span>
            </div>
            <div><p class="text-xs mt-2 truncate">{{ image?.name }}</p></div>
            <button
                @click="removeFile(index)"
                class="absolute top-0 right-0 bg-red-500 text-white w-6 h-6 flex items-center justify-center rounded-full shadow-md hover:bg-red-700 transition duration-300">
                <i class="fa fa-times-circle"></i>
            </button>
        </div>
        <div class="col-auto mt-2">
            <div
                class="relative bg-gray-100 border-dashed border-2 border-gray-300 rounded-lg flex justify-center items-center transition-all duration-300 transform hover:border-blue-500">
                <input
                    class="absolute inset-0 opacity-0 cursor-pointer"
                    type="file"
                    ref="files"
                    accept=".jpg, .jpeg, .png"
                    @change="readURL"
                    :multiple="multiple"
                />
                <div class="text-center space-y-2 py-6">
                    <i class="fa-solid fa-cloud-arrow-up text-xl text-gray-400"></i>
                    <p class="text-sm text-gray-400">ជ្រើសរើសរូបភាព</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
div.relative:hover {
    border-color: #3490dc;
}

div.relative:hover .fa-cloud-arrow-up {
    color: #3490dc;
}

div.group:hover img {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

button {
    background-color: rgba(255, 0, 0, 0.8);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    top: 5px;
    right: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.3s;
}

button:hover {
    background-color: rgba(255, 0, 0, 1);
}

div.relative {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

input[type="file"]::-webkit-file-upload-button {
    cursor: pointer;
    font-size: 16px;
    padding: 10px 20px;
    background-color: #3490dc;
    border-radius: 5px;
    color: white;
}
</style>
