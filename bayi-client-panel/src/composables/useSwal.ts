import SweetAlert from 'sweetalert2';
const swalInstance = SweetAlert;
const swal = swalInstance.mixin({
    confirmButtonText: 'Tamam',
    cancelButtonText: 'Kapat',
    denyButtonAriaLabel: 'Hayır',
    denyButtonText: 'Hayır',
});
interface ISwalToastOptions {
    timer?: number;
}
const swalToast = (options?: ISwalToastOptions) => {
    return swalInstance.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: options?.timer || 3000,
        timerProgressBar: true,
        background: '#f1f1f1',
    });
}

export function useSwal() {
    return swal;
}

export function useSwalToast(options?: ISwalToastOptions) {
    return swalToast(options);
}

export async function swalPermissionDenied(fn: Function) {
    await swal.fire({
        title: 'Erişim Engellendi',
        text: 'Bu işlemi yapmaya yetkiniz bulunmamaktadır.',
        icon: 'error',
        confirmButtonText: 'Tamam',
    }).then((result) => {
        fn();
    });
}