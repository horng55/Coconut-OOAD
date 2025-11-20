import './bootstrap';
import '../css/app.css';
import '../sass/app.scss';
import 'flowbite';
import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {createPinia} from 'pinia';
import AppHelper from './Helper/AppHelper.js';
import ImageHelper from './Helper/ImageHelper.js';
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

createInertiaApp({
    progress: {
        delay: 0,
        color: '#ffa237',
        includeCSS: true,
        showSpinner: false,
    },
    resolve: (name) => _resolvePageComponent(name, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(createPinia())
            .component('Swiper', Swiper)
            .component('SwiperSlide', SwiperSlide)
            .mixin({methods: AppHelper})
            .mixin({methods: ImageHelper})
            .mixin({methods: {route: window.route}})
            .mount(el);
    },
});

function _resolvePageComponent(name, pages) {
    for (const path in pages) {
        if (path.endsWith(`${name.replace('.', '/')}.vue`)) {
            return typeof pages[path] === 'function'
                ? pages[path]()
                : pages[path]
        }
    }

    throw new Error(`Page not found: ${name}`)
}
