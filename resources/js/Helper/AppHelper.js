export default {
    getDuration: function (duration) {
        if (duration === '1_month') {
            return "មួយខែ";
        }
        if (duration === '3_months') {
            return "បីខែ";
        }
        if (duration === '6_months') {
            return "ប្រាំមួយខែ";
        }
        if (duration === '1_year') {
            return "មួយឆ្នាំ";
        }
        return duration;
    },

    getSubStatus: function (status) {
        if (status === 'pending') {
            return "រង់ចាំ";
        }
        if (status === 'approved') {
            return "អនុញ្ញាត";
        }
        if (status === 'rejected') {
            return "បដិសេដ";
        }
        return status;
    },

    getPageTitle(titleArray, defaultTitle = "Page") {
        if (Array.isArray(titleArray) && titleArray.length > 0) {
            return titleArray[0].label || defaultTitle;
        }
        return defaultTitle;

    }
}
