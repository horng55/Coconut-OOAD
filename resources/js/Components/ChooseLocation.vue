<script setup>
import {computed, onMounted, ref, watch} from "vue";
import axios from "axios";
import {debounce} from "lodash";
import GoogleMapsPicker from "./GoogleMapsPicker.vue";

const props = defineProps({
    latitude: {
        type: Number,
        required: true,
    },
    longitude: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['on-choose']);

onMounted(() => {
    loadRelatedLocation();
});

const currenLocation = ref({
    latitude: props.latitude,
    longitude: props.longitude,
});
const mapKey = ref(1);
const search = ref('');
const locationList = ref([]);
const isLoading = ref(false);

watch(search, debounce((newValue) => {
    if (newValue) {
        searchLocationByName();
    } else {
        loadRelatedLocation();
    }
}, 500));

async function loadRelatedLocation() {
    isLoading.value = true;
    try {
        const response = await axios.post(route('google-location.near-by'), {
            latitude: currenLocation.value.latitude,
            longitude: currenLocation.value.longitude,
        });
        locationList.value = response.data;
    } catch (error) {
        console.error("Error loading related locations", error);
    } finally {
        isLoading.value = false;
    }
}

async function searchLocationByName() {
    isLoading.value = true;
    try {
        const response = await axios.post(route('google-location.search'), {
            search: search.value,
        });
        locationList.value = response.data;
    } catch (error) {
        console.error("Error searching for location", error);
    } finally {
        isLoading.value = false;
    }
}

function onUpdateLocation(latLng) {
    currenLocation.value = {
        latitude: latLng.latitude,
        longitude: latLng.longitude,
    };
}

function chooseLocation() {
    emit('on-choose', currenLocation.value);
}

function chooseLocationBy(item) {
    const nearByLocation = item.geometry.location;
    const nearByLatLng = {
        latitude: nearByLocation.lat,
        longitude: nearByLocation.lng,
    };
    onUpdateLocation(nearByLatLng);
    mapKey.value += 1;
}

const isCurrentLocation = computed(() => (item) => {
    return item.lat === currenLocation.value.latitude && item.lng === currenLocation.value.longitude;
});
</script>

<template>
    <div class="choose-location flex gap-6">
        <div class="w-1/3 bg-white rounded-lg shadow-lg">
            <div class="info-header p-4">
                <input
                    type="search"
                    class="form-control p-2 w-full rounded border border-gray-300"
                    placeholder="ស្វែងរកទីតាំង..."
                    v-model="search"
                    :disabled="isLoading"
                />
                <button
                    class="btn-create mt-4 w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600"
                    @click="chooseLocation"
                    :disabled="isLoading"
                >
                    ជ្រើសរើសទីតាំងនេះ
                </button>
            </div>
            <div class="p-4">
                <div v-if="isLoading" class="text-center py-3">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <p class="mt-2">Loading...</p>
                </div>

                <ul v-if="!isLoading && locationList?.length" class="space-y-4">
                    <li v-for="item in locationList" :key="item.place_id">
                        <div
                            class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-gray-100"
                            @click="chooseLocationBy(item)"
                        >
                            <img :src="item.icon" alt="icon" class="w-6 h-6">
                            <p
                                class="text-sm"
                                :class="{'bg-blue-500 text-white rounded-full py-1 px-3': isCurrentLocation(item.geometry.location)}"
                            >
                                {{ item.name }}
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="w-2/3 h-full">
            <GoogleMapsPicker
                :mapKey="mapKey"
                :latitude="currenLocation.latitude"
                :longitude="currenLocation.longitude"
                @on-update="onUpdateLocation"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
