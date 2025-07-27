import axios, { type RawAxiosRequestHeaders, type AxiosInstance } from 'axios'

import { useUserSession } from '/@src/stores/userSession'
import API_URIS from '/@src/utils/api/api_uris'
import { swalPermissionDenied } from './useSwal'
const baseUri = API_URIS.BASE_URL
let api: AxiosInstance

export function createApi() {
  // Here we set the base URL for all requests made to the api
  api = axios.create({
    //baseURL: import.meta.env.VITE_API_BASE_URL,
    baseURL: baseUri,
    //withCredentials: true,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'Access-Control-Allow-Origin': '*',
    },
  })

  // We set an interceptor for each request to
  // include Bearer token to the request if user is logged in
  api.interceptors.request.use(
    (config) => {
      const userSession = useUserSession()

      if (userSession.isLoggedIn) {
        config.headers = {
          ...((config.headers as RawAxiosRequestHeaders) ?? {}),
          Authorization: `Bearer ${userSession.token}`,
        }
      }

      return config
    },
    async (err) => {
      return Promise.reject(err)
    }
  )

  api.interceptors.response.use(
    (r) => r,
    async (err) => {
      if (err.response?.status === undefined) {
        if (err.response?.status === 401) {
          await useUserSession().logoutUser(true)
        } else if (err.response?.status === 403) {
          await swalPermissionDenied(() => location.href = '/app')
        }
      }
      return Promise.reject(err);
    });
  return api
}


export function useApi() {
  if (!api) {
    createApi()
  }
  return api
}
