<script setup>
import {Link, router} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Promotions", href: route("admin.promotions.index")},
    {label: "Promotion Details", href: "#"}
];

const props = defineProps({
    promotion: Object,
});

const approvePromotion = (id) => {
    if (confirm('Are you sure you want to approve and execute this promotion?')) {
        router.post(route('admin.promotions.approve', id));
    }
};

const rejectPromotion = (id) => {
    const reason = prompt('Please provide a reason for rejection (optional):');
    router.post(route('admin.promotions.reject', id), {
        notes: reason || ''
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Promotion Details`"
            subtitle="View promotion information"
            icon="fas fa-graduation-cap"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.promotions.index')"
                        class="text-green-500 hover:text-green-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Promotions
                    </Link>
                    <div class="flex gap-2">
                        <button
                            v-if="promotion?.status === 'pending'"
                            @click="approvePromotion(promotion?.id)"
                            class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-check"></i>
                            Approve
                        </button>
                        <button
                            v-if="promotion?.status === 'pending'"
                            @click="rejectPromotion(promotion?.id)"
                            class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-times"></i>
                            Reject
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ promotion?.student?.user?.full_name || 'N/A' }}
                        </p>
                        <p v-if="promotion?.student?.student_id" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Student ID: {{ promotion.student.student_id }}
                        </p>
                        <p v-if="promotion?.student?.user?.email" class="text-sm text-gray-600 dark:text-gray-400">
                            Email: {{ promotion.student.user.email }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium',
                            promotion?.status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                            promotion?.status === 'approved' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                            promotion?.status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                            'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                        ]">
                            <i :class="
                                promotion?.status === 'completed' ? 'fas fa-check-circle' :
                                promotion?.status === 'approved' ? 'fas fa-check' :
                                promotion?.status === 'rejected' ? 'fas fa-times-circle' :
                                'fas fa-clock'
                            "></i>
                            {{ promotion?.status_label || promotion?.status }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">From Class</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ promotion?.from_class?.name || 'N/A' }}
                        </p>
                        <p v-if="promotion?.from_class?.code" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Code: {{ promotion.from_class.code }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">To Class</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ promotion?.to_class?.name || 'N/A' }}
                        </p>
                        <p v-if="promotion?.to_class?.code" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Code: {{ promotion.to_class.code }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ promotion?.from_academic_year || 'N/A' }} → {{ promotion?.to_academic_year || 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Promotion Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ promotion?.promotion_date ? new Date(promotion.promotion_date).toLocaleDateString() : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Promotion Type</h3>
                        <span :class="[
                            'inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium',
                            promotion?.promotion_type === 'automatic' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                            promotion?.promotion_type === 'conditional' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                            promotion?.promotion_type === 'repeated' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                            'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                        ]">
                            {{ promotion?.promotion_type_label || promotion?.promotion_type }}
                        </span>
                    </div>

                    <div v-if="promotion?.promoted_by" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Promoted By</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ promotion.promoted_by?.full_name || 'N/A' }}
                        </p>
                    </div>

                    <div v-if="promotion?.notes" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h3>
                        <p class="text-gray-900 dark:text-white">{{ promotion.notes }}</p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

