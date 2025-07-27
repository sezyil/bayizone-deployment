import axios, { type RawAxiosRequestHeaders, type AxiosInstance } from 'axios'
import { uuid } from 'vue-uuid'

import API_URIS from '/@src/utils/api/api_uris'
import { useI18n } from 'vue-i18n';
const baseUri = API_URIS.BASE_URL;
let api: AxiosInstance

export function createApi(options: {
  locale: string
  userUuid?: string
} = { locale: 'tr' }) {
  // Here we set the base URL for all requests made to the api
  api = axios.create({
    //baseURL: import.meta.env.VITE_API_BASE_URL,
    baseURL: baseUri,
    //withCredentials: true,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'Access-Control-Allow-Origin': '*',
      'Accept-Language': options.locale,
      'User-Uuid': options.userUuid,
    },
  })
  return api
}


export function useApi() {

  const userUuid = useStorage('userUuid', uuid.v4())
  const defaultLocale = useStorage('locale', 'tr')
  api = createApi({ locale: defaultLocale.value, userUuid: userUuid.value })
  return api
}
