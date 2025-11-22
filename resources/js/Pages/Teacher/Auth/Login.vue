<script setup>
import {ref} from 'vue';
import {useForm, Link} from '@inertiajs/vue3';
import {AlertError} from "../../../Utils/AlertUtil.js";

const isBusy = ref(false);
const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

async function submit() {
    isBusy.value = true;
    form.post('/teacher/login', {
        onSuccess: () => {
            isBusy.value = false;
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            AlertError({message: firstError || 'An error occurred'});
            isBusy.value = false;
        },
        onFinish: () => {
            isBusy.value = false;
        },
    });
}
</script>

<template>
    <div class="relative min-h-screen flex justify-center items-center py-16 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-cyan-50 to-teal-50"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-float-delayed"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-teal-500/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>

        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-md mx-4">
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-md border border-gray-200 p-8 md:p-10">
                <!-- Logo/Icon -->
                <div class="flex justify-center mb-8">
                    <div class="relative w-20 h-20 rounded-2xl overflow-hidden shadow-lg ring-2 ring-blue-200 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-4xl text-blue-600"></i>
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-2">
                        Teacher Portal
                    </h2>
                    <p class="text-gray-600">
                        Sign in to access your teacher account
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>Email<span class="text-red-500">*</span>
                        </label>
                        <input 
                            v-model="form.email" 
                            type="email" 
                            id="email"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                            placeholder="teacher@school.edu" 
                            required
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-cyan-500"></i>Password<span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-300"
                            placeholder="••••••••"
                            required
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-[42px] text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                            tabindex="-1"
                        >
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                        <p v-if="form.errors.password" class="text-red-500 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            id="remember"
                            class="w-4 h-4 bg-gray-50 border-gray-300 rounded text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all duration-300 cursor-pointer"
                        />
                        <label for="remember" class="ml-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition-colors">
                            <i class="fas fa-bookmark mr-1 text-blue-500"></i>Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="isBusy"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-lg hover:shadow-blue-500/50 transform hover:scale-[1.02] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed font-bold"
                    >
                        <span v-if="!isBusy" class="flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            Signing in...
                        </span>
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-6 text-center">
                    <Link :href="route('home')" class="text-blue-600 hover:text-blue-700 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Home
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

.animate-float { 
    animation: float 20s ease-in-out infinite; 
}

.animate-float-delayed { 
    animation: float 25s ease-in-out infinite; 
    animation-delay: -5s; 
}

.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.bg-grid-pattern {
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>


