<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [
    {label: "Fees", href: route("admin.fees.index")},
    {label: "Fee Details", href: "#"}
];

const props = defineProps({
    fee: Object,
    stats: Object,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount || 0);
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Fee: ${fee?.name || 'N/A'}`"
            subtitle="View fee details and payments"
            icon="fas fa-money-bill-wave"
            icon-color="text-yellow-500"
            icon-bg="from-yellow-500/20 to-amber-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.fees.index')"
                        class="text-yellow-500 hover:text-yellow-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Fees
                    </Link>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('admin.fee-payments.index', { fee_id: fee?.id })"
                            class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-credit-card"></i>
                            View All Payments
                        </Link>
                        <Link
                            :href="route('admin.fees.payments.create', fee?.id)"
                            class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-plus"></i>
                            Create Payment Record
                        </Link>
                        <Link
                            :href="route('admin.fees.edit', fee?.id)"
                            class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-edit"></i>
                            Edit Fee
                        </Link>
                    </div>
                </div>

                <!-- Fee Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Fee Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ fee?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Fee Type</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-sm font-medium capitalize">
                            {{ fee?.fee_type || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Amount</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatCurrency(fee?.amount) }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ fee?.class_model?.name || 'All Classes' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ fee?.academic_year || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Due Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ fee?.due_date ? new Date(fee.due_date).toLocaleDateString() : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            fee?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="fee?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                            {{ fee?.status || 'N/A' }}
                        </span>
                    </div>

                    <div v-if="fee?.is_recurring" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Recurring Period</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                            {{ fee?.recurring_period || 'N/A' }}
                        </p>
                    </div>

                    <div v-if="fee?.description" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ fee.description }}</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-yellow-500 to-amber-500 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm mb-1">Total Payments</p>
                                <p class="text-3xl font-bold">{{ stats?.total_payments || 0 }}</p>
                            </div>
                            <i class="fas fa-receipt text-4xl opacity-50"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm mb-1">Paid</p>
                                <p class="text-3xl font-bold">{{ stats?.paid_count || 0 }}</p>
                            </div>
                            <i class="fas fa-check-circle text-4xl opacity-50"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-amber-500 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm mb-1">Collected</p>
                                <p class="text-3xl font-bold">{{ formatCurrency(stats?.total_collected || 0) }}</p>
                            </div>
                            <i class="fas fa-dollar-sign text-4xl opacity-50"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-pink-500 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm mb-1">Pending</p>
                                <p class="text-3xl font-bold">{{ formatCurrency(stats?.total_pending || 0) }}</p>
                            </div>
                            <i class="fas fa-clock text-4xl opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Payments List -->
                <div v-if="fee?.payments && fee.payments.length > 0">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Payments</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Amount</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Paid</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Remaining</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="payment in fee.payments"
                                    :key="payment.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                >
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ payment.student?.user?.full_name || 'N/A' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ payment.student?.student_id || 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatCurrency(payment.amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(payment.amount_paid) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold" :class="payment.remaining_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                            {{ formatCurrency(payment.remaining_amount) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                            payment.status === 'paid' 
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                                : payment.status === 'partial'
                                                ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                                                : payment.status === 'overdue'
                                                ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                        ]">
                                            <i :class="[
                                                payment.status === 'paid' ? 'fas fa-check-circle' :
                                                payment.status === 'partial' ? 'fas fa-exclamation-circle' :
                                                payment.status === 'overdue' ? 'fas fa-times-circle' :
                                                'fas fa-clock'
                                            ]"></i>
                                            {{ payment.status || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="route('admin.fee-payments.show', payment.id)"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="View Details"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                            <Link
                                                v-if="payment.status !== 'paid'"
                                                :href="route('admin.fees.payments.record', [fee.id, payment.id])"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Record Payment"
                                            >
                                                <i class="fas fa-money-bill"></i>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <i class="fas fa-receipt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">No payments found for this fee.</p>
                    <Link
                        :href="route('admin.fees.payments.create', fee?.id)"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2 rounded-lg transition-all duration-200"
                    >
                        <i class="fas fa-plus"></i>
                        Create Payment Record
                    </Link>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

