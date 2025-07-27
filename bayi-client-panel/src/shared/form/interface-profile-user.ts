export interface IProfileUserForm {
    fullname: string
    email: string
    password: string
    password_confirmation: string
}

export interface IProfileUserFormErrors {
    fullname: string[]
    password: string[]
    password_confirmation: string[]
}
