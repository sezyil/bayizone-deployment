import { useApi } from "../composables/useApi"
import { IAuthLoginForm, IAuthRegisterForm } from "../shared/form/interface-auth"
const api = useApi()

export const getUserInfo = async () => await api.get('/auth/getInfo');

export const requestLogin = async (data: IAuthLoginForm) => {
    return await api.post("/auth/login", data)
}
export const requestRegister = async (data: IAuthRegisterForm) => {
    return await api.post("/auth/register", data)
}