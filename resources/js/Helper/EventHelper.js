export default {
    getJoin: function (value) {
        if (value === true) {
            return "Joined"
        }
        if (value === false) {
            return "Not Joined"
        }
        return value;
    }
}
