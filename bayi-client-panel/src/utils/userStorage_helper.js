import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

export const userToken = (() => {
    const token_name = "client_token";
    const token = () => localStorage.getItem(token_name);
    const tokenSet = (token) => localStorage.setItem(token_name, token);
    const router = useRouter();
    const _clearToken = () => localStorage.clear(token_name);
    const _hasToken = () => (typeof token() !== "undefined" && token() != "undefined" && token()) ? true : false;

    return {
        hasToken: () => _hasToken(),
        getToken: () => _hasToken() ? token() : null,
        setToken: (token) => tokenSet(token),
        clearToken: () => _clearToken(),
        logout: () => {
            _clearToken();
            Swal.fire('Redirecting..', 'Logged Out', 'success').then(() => window.location = '/login')
        },
    }
})();