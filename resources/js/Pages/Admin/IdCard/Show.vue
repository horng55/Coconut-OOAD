<script setup>
import {ref, onMounted, watch} from "vue";
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import QRCode from "qrcode";

const title = [
    {label: "ID Cards", href: route("admin.id-cards.index")},
    {label: "ID Card Details", href: "#"}
];

const props = defineProps({
    idCard: Object,
});

const qrCodeDataUrl = ref(null);

// Generate QR code from card number
const generateQRCode = async () => {
    if (!props.idCard?.card_number) return;
    
    try {
        const dataUrl = await QRCode.toDataURL(props.idCard.card_number, {
            width: 200,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        });
        qrCodeDataUrl.value = dataUrl;
    } catch (error) {
        console.error('Error generating QR code:', error);
    }
};

onMounted(() => {
    generateQRCode();
});

watch(() => props.idCard?.card_number, () => {
    generateQRCode();
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`ID Card: ${idCard?.card_number || 'N/A'}`"
            subtitle="View ID card details and information"
            icon="fas fa-id-card"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.id-cards.index')"
                        class="text-purple-500 hover:text-purple-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to ID Cards
                    </Link>
                    <Link
                        :href="route('admin.id-cards.edit', idCard?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit ID Card
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-if="idCard?.photo_path" class="md:col-span-2 flex justify-center">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <img 
                                :src="`/storage/${idCard.photo_path}`" 
                                alt="ID Card Photo" 
                                class="w-48 h-48 object-cover rounded-lg border-2 border-gray-300 dark:border-gray-600"
                            >
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Card Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ idCard?.card_number || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Card Type</h3>
                        <span :class="[
                            'inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium',
                            idCard?.card_type === 'student' 
                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                        ]">
                            <i :class="idCard?.card_type === 'student' ? 'fas fa-user-graduate' : 'fas fa-chalkboard-teacher'"></i>
                            {{ idCard?.card_type ? idCard.card_type.charAt(0).toUpperCase() + idCard.card_type.slice(1) : 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Owner Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ idCard?.card_type === 'student' 
                                ? (idCard?.student?.user?.full_name || 'N/A')
                                : (idCard?.teacher?.user?.full_name || 'N/A')
                            }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                            {{ idCard?.card_type === 'student' ? 'Student ID' : 'Employee ID' }}
                        </h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ idCard?.card_type === 'student' 
                                ? (idCard?.student?.student_id || 'N/A')
                                : (idCard?.teacher?.employee_id || 'N/A')
                            }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ idCard?.card_type === 'student' 
                                ? (idCard?.student?.user?.email || 'N/A')
                                : (idCard?.teacher?.user?.email || 'N/A')
                            }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Issue Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ idCard?.issue_date ? new Date(idCard.issue_date).toLocaleDateString() : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Expiry Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ idCard?.expiry_date ? new Date(idCard.expiry_date).toLocaleDateString() : 'No expiry' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium',
                            idCard?.status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                            idCard?.status === 'expired' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                            'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                        ]">
                            <i :class="
                                idCard?.status === 'active' ? 'fas fa-check-circle' :
                                idCard?.status === 'expired' ? 'fas fa-exclamation-circle' :
                                'fas fa-ban'
                            "></i>
                            {{ idCard?.status ? idCard.status.charAt(0).toUpperCase() + idCard.status.slice(1) : 'N/A' }}
                        </span>
                    </div>

                    <div v-if="idCard?.notes" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h3>
                        <p class="text-gray-900 dark:text-white">{{ idCard.notes }}</p>
                    </div>

                    <!-- QR Code Section -->
                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex flex-col items-center">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border-2 border-purple-300 dark:border-purple-600 inline-block">
                                <img 
                                    v-if="qrCodeDataUrl" 
                                    :src="qrCodeDataUrl" 
                                    alt="QR Code"
                                    class="w-32 h-32"
                                >
                                <div v-else class="w-32 h-32 flex items-center justify-center">
                                    <i class="fas fa-spinner fa-spin text-purple-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Scan to verify card number</p>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

