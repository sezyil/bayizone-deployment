import { ref, watch } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
import { ICustomerOrderForm, ICustomerOrderFormErrors, ICustomerOrderFormProduct, ICustomerOrderFormDetailErrors, ICustomerOrderFormLineErrors, ICustomerOrderFormLine } from '/@src/shared/form/interface-customer-order'
import { addOrderFormLineError, resetOrderForm, resetOrderFormErrors } from '/@src/utils/form/customer_order_form'
import { ICompanyCustomerForm } from '/@src/shared/form/interface-company-customer'

export const useCustomerOrderFormStore = defineStore('customerOrderForm', () => {
    const orderForm = ref<ICustomerOrderForm>(resetOrderForm())
    const formErrors = ref<ICustomerOrderFormErrors>(resetOrderFormErrors())
    const customerInfo = ref<ICompanyCustomerForm | null>(null)
    const previewMode = ref<boolean>(false)


    const _resetOrderForm = () => orderForm.value = resetOrderForm()
    const resetFormErrors = () => formErrors.value = resetOrderFormErrors()

    //$reset
    function $reset() {
        customerInfo.value = null

        _resetOrderForm()
        resetFormErrors()
        previewMode.value = false
    }


    const addLine = (formData: ICustomerOrderFormLine) => {
        orderForm.value.lines.push(formData)
    }

    //delete line
    const deleteLine = (index: number) => {
        orderForm.value.lines.splice(index, 1)
    }

    const updateLine = (index: number, formData: ICustomerOrderFormLine) => {
        if (formData.color)
            if (formData.color.length == null) formData.color = null

        if (formData.dimension)
            if (formData.dimension.length == null) formData.dimension = null

        orderForm.value.lines[index] = formData
    }


    const isInternational = computed(() => {
        return orderForm.value.detail.is_international
    })




    const setCustomerInfo = (customer: ICompanyCustomerForm | null) => {
        customerInfo.value = customer
        if (orderForm.value.detail.billing_address) return
        setBillingAddress()
    }

    const setBillingAddress = () => {
        if (customerInfo.value) {
            orderForm.value.detail.billing_address = customerInfo.value.address
            orderForm.value.detail.billing_country_id = customerInfo.value.country_id
            orderForm.value.detail.billing_state_id = customerInfo.value.state_id
            orderForm.value.detail.billing_city_id = customerInfo.value.city_id
        } else {
            orderForm.value.detail.billing_address = ''
            orderForm.value.detail.billing_city_id = 0
            orderForm.value.detail.billing_country_id = 0
            orderForm.value.detail.billing_state_id = 0
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
                    let keyName = index as keyof ICustomerOrderFormDetailErrors
                    formErrors.value.detail[keyName] = e[field]
                } else if (fieldName == 'lines') {
                    let i = parseInt(index)
                    let keyName = lineObjectName as keyof ICustomerOrderFormLineErrors
                    if (!formErrors.value.lines[i]) {
                        formErrors.value.lines[i] = addOrderFormLineError()
                    }
                    formErrors.value.lines[i][keyName] = e[field]
                }
            }
        }
    }

    //watch orderForm.is_international and reset all tax
    watch(() => orderForm.value.detail.is_international, (is_international) => {
        if (is_international) {
            orderForm.value.lines.forEach((line, index) => {
                orderForm.value.lines[index].tax_rate = 0
            })
        }
    })


    const setFormData = (data: any) => {
        if (data?.lines) {
            let _lines = data.lines;
            _lines.forEach((line: any) => {
                let _color = line.color;
                if (_color) {
                    line.color = _color.map((item: any) => {
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
        //check key is exist in orderForm
        Object.keys(data).forEach(key => {
            if (orderForm.value.hasOwnProperty(key)) {
                orderForm.value[key as keyof typeof orderForm.value] = data[key]
            }
        })
    }

    const setPreviewMode = (mode: boolean) => {
        previewMode.value = mode
    }

    const sanitizeFormData = () => {
        let _form = { ...orderForm.value }
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

    return {
        orderForm,
        formErrors,
        previewMode,
        setPreviewMode,
        addLine,
        setFormData,
        getCustomerInfo,
        isInternational,
        setCustomerInfo,
        setBillingAddress,
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
    import.meta.hot.accept(acceptHMRUpdate(useCustomerOrderFormStore, import.meta.hot))
}
