interface IAuthLoginForm {
    email: string
    password: string
    code: string
}

interface IAuthLoginFormError {
    email: string[]
    password: string[]
    code: string[]
}

interface IAuthRegisterForm {
    fullname: string
    firm_name: string
    tax_no: string
    email: string
    password: string
    repass: string
    terms: boolean
}

interface IAuthRegisterFormError {
    fullname: string[]
    firm_name: string[]
    tax_no: string[]
    email: string[]
    password: string[]
    repass: string[]
    terms: string[]
}


export {
    IAuthLoginForm,
    IAuthLoginFormError,
    IAuthRegisterForm,
    IAuthRegisterFormError
}