interface IAuthLoginForm {
    email: string
    password: string
}

interface IAuthLoginFormError {
    email: string[]
    password: string[]
}

interface IAuthRegisterForm {
    fullname: string
    email: string
    password: string
    terms: boolean
    phone: string
}

interface IAuthRegisterFormError {
    fullname: string[]
    email: string[]
    password: string[]
    terms: string[]
    phone: string[]
}


export {
    IAuthLoginForm,
    IAuthLoginFormError,
    IAuthRegisterForm,
    IAuthRegisterFormError
}