export default {
    getReportTypeLabel(reportType) {
        const types = {
            'student_performance': 'Student Performance',
            'class_performance': 'Class Performance',
            'attendance': 'Attendance',
            'grade_distribution': 'Grade Distribution',
            'teacher_workload': 'Teacher Workload',
            'enrollment': 'Enrollment',
            'custom': 'Custom Report',
        };

        return types[reportType] || reportType;
    },

    getStatusLabel(status) {
        const statuses = {
            'pending': 'Pending',
            'generating': 'Generating',
            'completed': 'Completed',
            'failed': 'Failed',
        };

        return statuses[status] || status;
    },

    getStatusColor(status) {
        const colors = {
            'completed': 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
            'generating': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
            'failed': 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
            'pending': 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300',
        };

        return colors[status] || 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300';
    },

    getStatusIcon(status) {
        const icons = {
            'completed': 'fas fa-check-circle',
            'generating': 'fas fa-spinner fa-spin',
            'failed': 'fas fa-times-circle',
            'pending': 'fas fa-clock',
        };

        return icons[status] || 'fas fa-clock';
    },

    getReportTypeColor(reportType) {
        const colors = {
            'student_performance': 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
            'class_performance': 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
            'attendance': 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
            'grade_distribution': 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
            'teacher_workload': 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-300',
            'enrollment': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300',
            'custom': 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300',
        };

        return colors[reportType] || 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300';
    }
}

