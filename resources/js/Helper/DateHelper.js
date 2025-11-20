import {lunarDate} from '@kdamdev/khmerformat';

export default {
    khmerDateFormat(date) {
        const newDate = new Date(date);
        const day = newDate.getDate();
        const month = newDate.getMonth() + 1;
        const year = newDate.getFullYear();

        return `ថ្ងៃទី${this.formatDateNumber(day)} ខែ${this.getKhmerMonth(month)} ឆ្នាំ${year}`;
    },
    khmerDateTimeFormat(date) {
        const newDate = new Date(date);
        const day = newDate.getDate();
        const month = newDate.getMonth() + 1;
        const year = newDate.getFullYear();
        const minute = newDate.getMinutes();
        const hour = newDate.getHours();
        const amPm = hour >= 12 ? 'PM' : 'AM';

        return `ថ្ងៃទី${this.formatDateNumber(day)} ខែ${this.getKhmerMonth(month)} ឆ្នាំ${year} | ${this.formatDateNumber(hour % 12)}:${this.formatDateNumber(minute)} ${amPm}`;
    },
    simpleKhmerDateTimeFormat(date) {
        const newDate = new Date(date);
        const day = newDate.getDate();
        const month = newDate.getMonth() + 1;
        const year = newDate.getFullYear();
        const minute = newDate.getMinutes();
        const hour = newDate.getHours();
        const amPm = hour >= 12 ? 'PM' : 'AM';

        return `${this.formatDateNumber(day)}/${this.getKhmerMonth(month)}/${year} | ${this.formatDateNumber(hour % 12)}:${this.formatDateNumber(minute)} ${amPm}`;
    },
    dateTimeFormat(date) {
        const newDate = new Date(date);
        const day = newDate.getDate();
        const month = newDate.getMonth() + 1;
        const year = newDate.getFullYear();
        const minute = newDate.getMinutes();
        const hour = newDate.getHours();
        const amPm = hour >= 12 ? 'PM' : 'AM';

        return `${this.formatDateNumber(day)}/${this.formatDateNumber(month)}/${year} | ${this.formatDateNumber(hour % 12)}:${this.formatDateNumber(minute)}${amPm}`;
    },
    dateFormat(date, separator = "/") {
        const newDate = new Date(date);
        const day = newDate.getDate();
        const month = newDate.getMonth() + 1;
        const year = newDate.getFullYear();

        return `${this.formatDateNumber(day)}${separator}${this.formatDateNumber(month)}${separator}${year}`;
    },
    formatDateNumber(value) {
        return value.toString().padStart(2, '0');
    },
    getKhmerMonth(month) {
        const newMonth = `${month + 1}`;
        switch (newMonth) {
            case "1":
                return "មករា";
            case "2":
                return "កុម្ភៈ";
            case "3":
                return "មីនា";
            case "4":
                return "មេសា";
            case "5":
                return "ឧសភា";
            case "6":
                return "មិថុនា";
            case "7":
                return "កក្កដា";
            case "8":
                return "សីហា";
            case "9":
                return "កញ្ញា";
            case "10":
                return "តុលា";
            case "11":
                return "វិច្ឆិកា";
            case "12":
                return "ធ្នូ";
        }
        return "";
    },
    toYmdDate(date) {
        const month = this.pad2(date.getMonth() + 1);
        const day = this.pad2(date.getDate());
        const year = date.getFullYear();

        return year + "-" + month + "-" + day;
    },
    pad2(n) {
        return (n < 10 ? '0' : '') + n;
    },

    lunarDateFormat(date) {
        const lunar = lunarDate(new Date(date));
        return lunar.toString();
    },

}
