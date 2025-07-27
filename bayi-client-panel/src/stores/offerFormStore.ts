import { IOfferFormDetailErrors, IOfferFormLine, IOfferFormLineErrors } from './../shared/form/interface-offer';

import { ref, watch } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
import { IOfferForm, IOfferFormErrors } from '/@src/shared/form/interface-offer'
import { addOfferFormLineError, resetOfferForm, resetOfferFormErrors } from '/@src/utils/form/offer_form'
import { ICompanyCustomerForm } from '/@src/shared/form/interface-company-customer'

export const useOfferFormStore = defineStore('offerForm', () => {
    const offerForm = ref<IOfferForm>(resetOfferForm())
    const formErrors = ref<IOfferFormErrors>(resetOfferFormErrors())
    const customerInfo = ref<ICompanyCustomerForm | null>(null)
    const offerRequestMode = ref<boolean>(false)

    const _resetOfferForm = () => offerForm.value = resetOfferForm()
    const resetFormErrors = () => formErrors.value = resetOfferFormErrors()

    //$reset
    function $reset() {
        offerRequestMode.value = false
        customerInfo.value = null
        _resetOfferForm()
        resetFormErrors()
    }

    //set offer request mode
    const setOfferRequestMode = (mode: boolean) => offerRequestMode.value = mode


    const addLine = (formData: IOfferFormLine) => {
        offerForm.value.lines.push(formData)
    }

    const updateLine = (index: number, formData: IOfferFormLine) => {
        if (formData.color)
            if (formData.color.length == null) formData.color = null

        if (formData.dimension)
            if (formData.dimension.length == null) formData.dimension = null

        offerForm.value.lines[index] = formData
    }

    //delete line
    const deleteLine = (index: number) => {
        offerForm.value.lines.splice(index, 1)
    }

    const setCustomerInfo = (customer: ICompanyCustomerForm | null) => {
        customerInfo.value = customer
        if (offerForm.value.detail.billing_address) return
        setBillingAddress()
    }

    const setBillingAddress = () => {
        if (customerInfo.value) {
            offerForm.value.detail.billing_address = customerInfo.value.address
            offerForm.value.detail.billing_country_id = customerInfo.value.country_id
            offerForm.value.detail.billing_state_id = customerInfo.value.state_id
            offerForm.value.detail.billing_city_id = customerInfo.value.city_id
        } else {
            offerForm.value.detail.billing_address = ''
            offerForm.value.detail.billing_city_id = 0
            offerForm.value.detail.billing_country_id = 0
            offerForm.value.detail.billing_state_id = 0
        }
    }


    const getCustomerInfo = computed(() => {
        return customerInfo.value
    })

    //set errors
    const setErrors = async (e: any) => {
        resetFormErrors()
        if (typeof e.params === 'object') return //for swal instance
        //reset errors
        //object loop
        for (const field in e) {
            //if field has dot notation
            if (field.includes('.')) {
                //split field
                const [fieldName, index, lineObjectName] = field.split('.')
                if (fieldName == 'detail') {
                    let keyName = index as keyof IOfferFormDetailErrors
                    formErrors.value.detail[keyName] = e[field]
                } else if (fieldName == 'lines') {
                    let i = parseInt(index)
                    let keyName = lineObjectName as keyof IOfferFormLineErrors
                    if (!formErrors.value.lines[i]) {
                        formErrors.value.lines[i] = addOfferFormLineError()
                    }
                    formErrors.value.lines[i][keyName] = e[field]
                }
            }
        }
    }

    //watch offerForm.is_international and reset all tax
    watch(() => offerForm.value.detail.is_international, (is_international) => {
        if (is_international) {
            offerForm.value.lines.forEach((line, index) => {
                offerForm.value.lines[index].tax_rate = 0
            })
        }
    })

    const isInternational = computed(() => {
        return offerForm.value.detail.is_international
    })

    const sanitizeFormData = () => {
        let _form = { ...offerForm.value }
        console.log(_form)
        _form.lines = _form.lines.map((line) => {
            let _colors: any = []
            let _dimensions: any = []
            if (line.color) {
                line.color?.forEach((color) => {
                    if (color.value_id) _colors.push(color)
                })
            }
            if (line.dimension) {
                line.dimension?.forEach((dimension) => {
                    if (dimension.value_id) _dimensions.push(dimension)
                })
            }

            return {
                ...line,
                color: _colors,
                dimension: _dimensions
            };
        });

        return _form
    }



    const setFormData = (data: any) => {
        if (data?.lines) {
            let _lines = data.lines;
            _lines.forEach((line: any) => {
                let _color = line.color;
                if (_color) {
                    line.color = _color.map((item: any) => {
                        console.log(item)
                        let _x = {
                            product_variant_id: item.product_variant_id,
                            variant_id: item.variant_id,
                            value_id: item.variant_value_id,
                        }
                        return _x;
                    })
                }
                let _dimension = line.dimension;
                if (_dimension) {
                    line.dimension = _dimension.map((item: any) => {
                        let _x = {
                            product_variant_id: item.product_variant_id,
                            variant_id: item.variant_id,
                            value_id: item.variant_value_id,
                        }
                        return _x;
                    })
                }
            })

        }
        //check key is exist in offerForm
        Object.keys(data).forEach(key => {
            if (offerForm.value.hasOwnProperty(key)) {
                //@ts-ignore
                offerForm.value[key] = data[key]
            }

        })
    }

    return {
        offerForm,
        formErrors,
        isInternational,
        offerRequestMode,
        setOfferRequestMode,
        addLine,
        setFormData,
        getCustomerInfo,
        setCustomerInfo,
        sanitizeFormData,
        deleteLine,
        updateLine,
        resetFormErrors,
        setErrors,
        $reset
    } as const

})
/**
 * Pinia supports Hot Module replacement so you can edit your stores and
 * interact with them directly in your app without reloading the page.
 *
 * @see https://pinia.esm.dev/cookbook/hot-module-replacement.html
 * @see https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useOfferFormStore, import.meta.hot))
}
