<script setup>
import {GoogleMap, Marker, CustomControl} from "vue3-google-map";
import {computed, onMounted, ref, watch} from "vue";
import LocationHelper from "../Helper/LocationHelper.js";
import BusyLoading from "./BusyLoading.vue";

const props = defineProps({
    latitude: Number,
    longitude: Number,
    mapKey: Number,
});

const emit = defineEmits(['on-update']);
const mapKeyValue = computed(() => props.mapKey);

onMounted(() => {
    getLocation();
});

const apiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;
const mapRef = ref(null);

const error = ref()
const currenLocation = ref();
const isLoading = ref(false);

const markerOptions = computed(() => {
    if (!currenLocation.value) {
        return null;
    }

    return {position: currenLocation.value, label: "", title: ""};
});

watch(mapKeyValue, (newValue) => {
    getLocation();
});

watch(currenLocation, (newValue) => {
    const newLatLng = {latitude: newValue.lat, longitude: newValue.lng};
    emit('on-update', newLatLng);
});

function getLocation() {
    if (props.latitude && props.longitude) {
        currenLocation.value = {
            lat: props.latitude,
            lng: props.longitude,
        }
        return;
    }

    gotoCurrentLocation();
}

function handleOnClick(event) {
    const latLng = event.latLng;
    currenLocation.value = {
        lat: latLng.lat(),
        lng: latLng.lng(),
    };
}

async function gotoCurrentLocation() {
    isLoading.value = true;
    const location = await LocationHelper.getCurrentLocation();
    isLoading.value = false;

    currenLocation.value = {
        lat: location.latitude,
        lng: location.longitude,
    };
}

</script>


<template>
    <div v-if="error">
        <p class="text-center text-danger">{{ error }}</p>
    </div>
    <div v-else>
        <div v-if="currenLocation" class="related-container">
            <GoogleMap
                class="google-map"
                ref="mapRef"
                :api-key="apiKey"
                :center="currenLocation"
                :zoom="15"
                :pan-control="true"
                :draggable="true"
                @click="handleOnClick"
                lang="km"
                :streetViewControl="false"
            >
                <CustomControl position="RIGHT_BOTTOM">
                    <button class="custom-btn" @click="gotoCurrentLocation">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </button>
                </CustomControl>
                <Marker
                    :options="markerOptions"
                    :draggable="true"
                ></Marker>
            </GoogleMap>
            <BusyLoading v-if="isLoading"/>
        </div>
        <div v-else>
            <div
                primaryColor="#f2f2f2"
                secondaryColor="#ffffff"
                width="100%"
                height="100"
            />
        </div>
    </div>

</template>

<style scoped>
.google-map {
    width: 100%;
    height: 700px;
    clip-path: inset(0px round 10px);
}

.custom-btn {
    box-sizing: border-box;
    background: white;
    height: 40px;
    width: 40px;
    border-radius: 2px;
    border: 0;
    margin: 10px;
    padding: 0;
    font-size: 1.25rem;
    text-transform: none;
    appearance: none;
    cursor: pointer;
    user-select: none;
    box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px;
    overflow: hidden;
}


</style>
