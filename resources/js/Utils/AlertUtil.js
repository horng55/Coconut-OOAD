import Swal from 'sweetalert2';
import Constants from "../Constants/Constants.js";

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});


async function AlertError({message = Constants.OPERATION_FAIL}) {
    await Toast.fire({
        icon: 'error', title: message,
    });
}

async function AlertWarning({message = Constants.OPERATION_FAIL}) {
    await Toast.fire({
        icon: 'warning', title: message,
    });
}


async function AlertResponse(data) {
    const code = data.code;
    const message = data.message;
    if (code === 200) {
        await Toast.fire({
            icon: 'success',
            title: message,
        });
    } else {
        await Toast.fire({
            icon: 'error',
            title: message,
        });
    }
}


async function AlertSuccess({message = Constants.OPERATION_SUCCESS}) {
    await Toast.fire({
        icon: 'success',
        title: message,
    });
}

async function ConfirmDelete({confirm, message = Constants.DO_YOU_WANT_DELETE}) {
    await Swal.fire({
        icon: "warning",
        title: `<p class="text-black fs-4 fw-bold">${message}</p>`,
        html: `<p class="text-grey">ទិន្នន័យនឹងមិនអាចត្រឡប់មកវិញបានទេ!</p>`,
        showCloseButton: false,
        focusConfirm: true,
        showCancelButton: true,
        confirmButtonText: Constants.AGREE,
        cancelButtonText: Constants.CANCEL,
        confirmButtonColor: "#00ACEE",
        cancelButtonColor: "#d33",
        showLoaderOnConfirm: true,
        customClass: {popup: 'custom-swal-popup'},
        preConfirm: () => confirm(),
        reverseButtons: true,
    });
}

async function ConfirmDialog({confirm, message, title = null}) {
    await Swal.fire({
        icon: "question",
        title: title && `<p class="text-black fs-4 fw-bold">${title}</p>`,
        html: `<p class="text-black-50 fs-5">${message}</p>`,
        showCloseButton: false,
        showCancelButton: true,
        focusConfirm: true,
        confirmButtonText: Constants.AGREE,
        cancelButtonText: Constants.CANCEL,
        confirmButtonColor: '#42A5F5',
        cancelButtonColor: '#EF5350',
        showLoaderOnConfirm: true,
        reverseButtons: true,
        customClass: {popup: 'custom-swal-popup',},
        preConfirm: () => confirm(),
    });
}

async function NotFoundDialog(message = "ស្វែងរកលទ្ធផលមិនឃើញ !") {
    await Swal.fire({
        position: 'center',
        text: message,
        width: '300px',
        timer: 1500,
        heightAuto: false,
        customClass: {popup: 'custom-swal-popup', icon: 'swal-icon-custom'},
        showConfirmButton: false,
        iconHtml: `<img src="/assets/images/not-found.png" alt="icon">`,
    });
}


export {
    AlertError, AlertSuccess, ConfirmDelete, ConfirmDialog, AlertResponse, NotFoundDialog, AlertWarning,
}
