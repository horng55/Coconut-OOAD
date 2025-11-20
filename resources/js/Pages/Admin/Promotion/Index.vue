<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Promotions", href: route("admin.promotions.index")}];

const props = defineProps({
    promotions: Object,
    classes: Array,
    filters: Object,
    defaultAcademicYear: String,
});

const searchQuery = ref(props.filters?.search || "");
const selectedStatus = ref(props.filters?.status || "");
const selectedPromotionType = ref(props.filters?.promotion_type || "");
const selectedFromAcademicYear = ref(props.filters?.from_academic_year || "");
const selectedToAcademicYear = ref(props.filters?.to_academic_year || "");

const promotionTypes = [
    { value: 'automatic', label: 'Automatic' },
    { value: 'manual', label: 'Manual' },
    { value: 'conditional', label: 'Conditional' },
    { value: 'repeated', label: 'Repeated' },
];

const statuses = [
    { value: 'pending', label: 'Pending' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'completed', label: 'Completed' },
];

const handleSearch = () => {
    router.get(route('admin.promotions.index'), {
        search: searchQuery.value,
        status: selectedStatus.value,
        promotion_type: selectedPromotionType.value,
        from_academic_year: selectedFromAcademicYear.value,
        to_academic_year: selectedToAcademicYear.value,
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deletePromotion = (id) => {
    if (confirm('Are you sure you want to delete this promotion?')) {
        router.delete(route('admin.promotions.destroy', id));
    }
};

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
            title="Promotion Management"
            :subtitle="`Total: ${promotions?.total || 0} promotion records`"
            icon="fas fa-graduation-cap"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search by student name, class..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    />
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    >
                        <option value="">All Status</option>
                        <option v-for="status in statuses" :key="status.value" :value="status.value">
                            {{ status.label }}
                        </option>
                    </select>
                    <select
                        v-model="selectedPromotionType"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    >
                        <option value="">All Types</option>
                        <option v-for="type in promotionTypes" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </option>
                    </select>
                    <input
                        v-model="selectedFromAcademicYear"
                        @change="handleSearch"
                        type="text"
                        placeholder="From Academic Year"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50 w-40"
                    />
                    <input
                        v-model="selectedToAcademicYear"
                        @change="handleSearch"
                        type="text"
                        placeholder="To Academic Year"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50 w-40"
                    />
                    <Link
                        :href="route('admin.promotions.create')"
                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add Promotion
                    </Link>
                    <Link
                        :href="route('admin.promotions.bulk-promote')"
                        class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-users"></i>
                        Bulk Promote
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">From Class</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">To Class</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Academic Year</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Promotion Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(promotion, index) in promotions?.data || []" 
                            :key="promotion.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-green-500/10 dark:bg-green-500/30 rounded-lg text-green-500 dark:text-green-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">
                                        {{ promotion.student?.user?.full_name || 'N/A' }}
                                    </span>
                                    <span v-if="promotion.student?.student_id" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        ID: {{ promotion.student.student_id }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ promotion.from_class?.name || 'N/A' }}</span>
                                    <span v-if="promotion.from_class?.code" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        {{ promotion.from_class.code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ promotion.to_class?.name || 'N/A' }}</span>
                                    <span v-if="promotion.to_class?.code" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        {{ promotion.to_class.code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ promotion.from_academic_year }} → {{ promotion.to_academic_year }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ promotion.promotion_date ? new Date(promotion.promotion_date).toLocaleDateString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    promotion.promotion_type === 'automatic' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                    promotion.promotion_type === 'conditional' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                    promotion.promotion_type === 'repeated' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                    'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                ]">
                                    {{ promotion.promotion_type_label || promotion.promotion_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    promotion.status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                    promotion.status === 'approved' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                    promotion.status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                    'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                                ]">
                                    <i :class="
                                        promotion.status === 'completed' ? 'fas fa-check-circle' :
                                        promotion.status === 'approved' ? 'fas fa-check' :
                                        promotion.status === 'rejected' ? 'fas fa-times-circle' :
                                        'fas fa-clock'
                                    "></i>
                                    {{ promotion.status_label || promotion.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.promotions.show', promotion.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <button
                                        v-if="promotion.status === 'pending'"
                                        @click="approvePromotion(promotion.id)"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Approve"
                                    >
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button
                                        v-if="promotion.status === 'pending'"
                                        @click="rejectPromotion(promotion.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Reject"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button
                                        v-if="promotion.status !== 'completed'"
                                        @click="deletePromotion(promotion.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!promotions?.data || promotions.data.length === 0">
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-graduation-cap text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No promotion records found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="promotions?.links" :links="promotions.links"/>
        </AdminPageWrapper>
    </App>
</template>

