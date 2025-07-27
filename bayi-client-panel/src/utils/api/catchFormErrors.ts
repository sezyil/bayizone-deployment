import Swal from "sweetalert2";
import { useUserSession } from "/@src/stores/userSession";
import { swalPermissionDenied } from "/@src/composables/useSwal";
const swalMessage = (msg: string) => Swal.fire({
    icon: 'error',
    title: 'Hata',
    text: msg,
    confirmButtonText: 'Tamam',
    cancelButtonAriaLabel: 'Vazgeç',
    denyButtonAriaLabel: 'Vazgeç'
})
export function catchFieldError(err: any, setFieldError?: any, redirectUrl?: string, fn: Function = () => { }) {
    if (err.response?.status === 422 || err.response?.status === 400) {
        const data = err.response.data

        const errors = data?.errors
        if (!errors) {
            return swalMessage(data?.msg)
        };
        if (!setFieldError) return errors;
        for (const field in errors) {
            let errorMsg = errors[field][0]
            if (err.response?.status == 400) errorMsg = errors[field]
            setFieldError(field, errorMsg)
        }
    } else if (err.response?.status === 401) {
        return useUserSession().logoutUser(true)
    } else if (err.response?.status === 403) {
        return swalPermissionDenied(fn() || (() => location.href = '/app'))
    }
    else if (err.response?.status === 404) {
        const uri = redirectUrl || '/app';
        return swalMessage("İşlem yapmak istediğiniz kayıt/sayfa bulunamadı").then(() => location.href = uri)
    } else {
        return swalMessage("Bir hata oluştu. Eğer bu hata devam ederse lütfen sistem yöneticiniz ile iletişime geçiniz.")
    }
}

/**
 * Extracts form errors from an object and returns them in a nested object.
 * @param obj is need to be an catchFieldError response object
 * @returns {Object} 
 */
export function extractErrors(obj: any): any {
    const errors: any = {};

    for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
            const parts = key.split('.'); // Anahtarı noktalara göre böler
            let current = errors;

            // Her parça için bir nesne oluşturur ve son parçayı hata mesajıyla eşler
            for (let i = 0; i < parts.length; i++) {
                const part = parts[i];
                const nextPart = parts[i + 1]; // Bir sonraki parçayı al

                if (i === parts.length - 1) {
                    // Son parça, hata mesajlarıdır
                    current[part] = obj[key];
                } else {
                    // Noktadan sonraki kısmın bir sayı olup olmadığını kontrol eder
                    const nextPartIsNumber = /^\d+$/.test(nextPart);

                    if (nextPartIsNumber) {
                        // Noktadan sonraki kısım bir sayıysa, bir dizi oluşturur
                        current[part] = current[part] || [];
                    } else {
                        // Değilse, bir nesne oluşturur
                        current[part] = current[part] || {};
                    }
                    
                    current = current[part];
                }
            }
        }
    }

    return errors;
    }

