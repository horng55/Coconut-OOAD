<script setup>
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const isBusy = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    if (isBusy.value) {
        return;
    }

    isBusy.value = true;
    form.post(route("admin.register.store"), {
        onFinish: () => {
            isBusy.value = false;
        },
    });
};
</script>

<template>
    <div class="relative min-h-screen flex justify-center items-center py-16 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-b from-black via-gray-900 to-black"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-[#F87B1B]/20 rounded-full blur-3xl animate-float-delayed"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-pink-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>

        <!-- Register Card -->
        <div class="relative z-10 w-full max-w-md mx-4">
            <div class="bg-white/5 backdrop-blur-xl rounded-3xl shadow-md border border-white/10 p-8 md:p-10">
                <!-- Logo/Icon -->
                <div class="flex justify-center mb-8">
                    <div class="relative w-20 h-20 rounded-2xl overflow-hidden shadow-lg ring-2 ring-white/10 bg-gradient-to-br from-[#F87B1B]/20 to-pink-500/20 flex items-center justify-center">
                        <i class="fas fa-user-plus text-4xl text-white"></i>
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-[#F87B1B] via-pink-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        Create Account
                    </h2>
                    <p class="text-white/60 font-khmer-Battambang">
                        បំពេញព័ត៌មានដើម្បីបង្កើតគណនី
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name Field -->
                <div>
                        <label for="name" class="block text-sm font-medium text-white/80 mb-2">
                            <i class="fas fa-user mr-2 text-[#F87B1B]"></i>ឈ្មោះ / Name<span class="text-red-400">*</span>
                        </label>
                    <input
                        v-model="form.name"
                        type="text"
                            id="name"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#F87B1B]/50 focus:border-[#F87B1B]/50 transition-all duration-300"
                            placeholder="John Doe" 
                        required
                    />
                        <p v-if="form.errors.name" class="text-red-400 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.name }}
                        </p>
                </div>

                    <!-- Username Field -->
                <div>
                        <label for="username" class="block text-sm font-medium text-white/80 mb-2">
                            <i class="fas fa-at mr-2 text-pink-400"></i>ឈ្មោះអ្នកប្រើប្រាស់ / Username<span class="text-red-400">*</span>
                        </label>
                    <input
                        v-model="form.username"
                        type="text"
                            id="username"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500/50 transition-all duration-300"
                            placeholder="johndoe" 
                        required
                    />
                        <p v-if="form.errors.username" class="text-red-400 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.username }}
                        </p>
                </div>

                    <!-- Email Field -->
                <div>
                        <label for="email" class="block text-sm font-medium text-white/80 mb-2">
                            <i class="fas fa-envelope mr-2 text-purple-400"></i>អ៊ីមែល / Email<span class="text-red-400">*</span>
                        </label>
                    <input
                        v-model="form.email"
                        type="email"
                            id="email"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-300"
                            placeholder="john@example.com" 
                        required
                    />
                        <p v-if="form.errors.email" class="text-red-400 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.email }}
                        </p>
                </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-white/80 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-400"></i>ពាក្យសម្ងាត់ / Password<span class="text-red-400">*</span>
                        </label>
                    <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                        id="password"
                            class="w-full px-4 py-3 pr-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                            placeholder="••••••••"
                        required
                    />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-[42px] text-white/40 hover:text-white/60 focus:outline-none transition-colors"
                            tabindex="-1"
                        >
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                        <p v-if="form.errors.password" class="text-red-400 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.password }}
                        </p>
                </div>

                    <!-- Confirm Password Field -->
                    <div class="relative">
                        <label for="password_confirmation" class="block text-sm font-medium text-white/80 mb-2">
                            <i class="fas fa-lock mr-2 text-cyan-400"></i>បញ្ជាក់ពាក្យសម្ងាត់ / Confirm Password<span class="text-red-400">*</span>
                        </label>
                    <input
                            v-model="form.password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                        id="password_confirmation"
                            class="w-full px-4 py-3 pr-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition-all duration-300"
                            placeholder="••••••••"
                        required
                    />
                        <button
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute right-4 top-[42px] text-white/40 hover:text-white/60 focus:outline-none transition-colors"
                            tabindex="-1"
                        >
                            <i :class="showPasswordConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                </div>

                    <!-- Submit Button -->
                <button
                    type="submit"
                        :disabled="isBusy"
                        class="w-full bg-gradient-to-r from-[#F87B1B] to-pink-500 text-white py-3 rounded-xl hover:from-[#F87B1B]/90 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-[#F87B1B]/50 shadow-lg hover:shadow-[#F87B1B]/50 transform hover:scale-[1.02] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed font-bold"
                    >
                        <span v-if="!isBusy" class="flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            បង្កើតគណនី
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            កំពុងបង្កើត...
                        </span>
                </button>
            </form>

                <!-- Footer Link -->
                <div class="mt-8 text-center">
                    <p class="text-white/60 text-sm">
                        មានគណនីរួចហើយ? 
                        <Link :href="route('login')" class="text-[#F87B1B] hover:text-[#F87B1B]/80 font-semibold transition-colors">
                            ចូលគណនីទីនេះ
                </Link>
            </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Floating Animation */
@keyframes float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -30px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-float {
    animation: float 20s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float 25s ease-in-out infinite;
    animation-delay: -5s;
}

/* Grid Pattern */
.bg-grid-pattern {
    background-image: 
        linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
}

/* Text gradient support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
    -webkit-backdrop-filter: blur(24px);
    backdrop-filter: blur(24px);
}
</style>
