import SweetAlert from 'sweetalert2';
const swal = SweetAlert;
export const useSwal = () => swal;

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