/* export IUserResponse = { */

export interface IUserFormExternalResponse {
    roles: IUserFormExternalRole[]
}

export interface IUserFormExternalRole {
    id: number
    name: string
}
