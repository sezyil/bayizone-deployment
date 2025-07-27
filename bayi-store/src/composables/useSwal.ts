import SweetAlert, { SweetAlertOptions } from 'sweetalert2';
const swalInstance = SweetAlert;

export function useSwal(_options: {
    isToast?: boolean,
    timer?: number,
} = { isToast: false, timer: 3000 }) {
    const swalConfig: SweetAlertOptions = {
        confirmButtonText: 'Tamam',
        cancelButtonText: 'Kapat',
        denyButtonAriaLabel: 'Hayır',
    };
    if (_options.isToast) {
        swalConfig.toast = true;
        swalConfig.position = 'top-end';
        swalConfig.showConfirmButton = false;
        swalConfig.timer = _options.timer;
    }
    const swal = swalInstance.mixin(swalConfig);
    return swal;
}

export async function swalPermissionDenied(fn: Function) {
    await useSwal().fire({
        title: 'Erişim Engellendi',
        text: 'Bu işlemi yapmaya yetkiniz bulunmamaktadır.',
        icon: 'error',
        confirmButtonText: 'Tamam',
    }).then((result) => {
        fn();
    });
}