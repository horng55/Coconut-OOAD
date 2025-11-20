<template>
    <GoogleMap
        class="google-map"
        :api-key="apiKey"
        :center="currentLocation"
        :zoom="15"
        :pan-control="false"
        :draggable="false"
        lang="km"
        :disable-default-ui="true">
        <Marker
            :options="markerOptions"
            :draggable="false"
        />
    </GoogleMap>
</template>

<script setup>
import {GoogleMap, Marker} from "vue3-google-map";
import {computed, ref, watch} from "vue";

const props = defineProps({
    latitude: Number,
    longitude: Number,
});

const apiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;
const currentLocation = ref({lat: props.latitude, lng: props.longitude});

watch(
    () => [props.latitude, props.longitude],
    ([newLat, newLng]) => {
        currentLocation.value = {lat: newLat, lng: newLng};
    },
    {immediate: true}
);

const markerOptions = computed(() => {
    return {
        position: currentLocation.value,
        label: "",
        title: "",
    };
});
</script>

<style scoped>
.google-map {
    width: 100%;
    height: 350px;
    clip-path: inset(0px round 10px);
}
</style>
