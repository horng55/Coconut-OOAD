export default {
    getStatus: function (status) {
        if (status === "active") {
            return "សកម្ម";
        }
        if (status === "inactive") {
            return "មិនសកម្ម";
        }
    },
}
