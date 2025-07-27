import { ref } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
import { ITransactionForm, ITransactionFormErrors } from '../shared/form/interface-transaction';

const resetTransactionForm = (): ITransactionForm => ({
    customer_order_id: null,
    company_customer_id: '',
    currency: 'tl',
    date: '',
    description: '',
    due_date: '',
    fiche_no: '',
    fiche_type: '',
    io_type: '',
    amount: 0.00,
    is_paid: false,
})




export const useTransactionStore = defineStore('transactionStore', () => {
    const transactionForm = ref<ITransactionForm>({ ...resetTransactionForm() })
    const editMode = ref(false)
    const formErrors = ref<ITransactionFormErrors>({} as ITransactionFormErrors)
    const _resetTransactionForm = () => transactionForm.value = { ...resetTransactionForm() }

    function $reset() {
        _resetTransactionForm()
        resetFormErrors()
    }

    const setTransactionForm = (data: ITransactionForm) => {
        transactionForm.value = data
    }

    const setEditMode = (value: boolean) => editMode.value = value


    const setFormErrors = (e: any) => {
        resetFormErrors()
        if (typeof e.params === 'object') return //for swal instance
        //reset errors
        //object loop
        for (const field in e) {
            //if field has dot notation
            if (field.includes('.')) {
                //split field
                const [fieldName, index, lineObjectName] = field.split('.')
                let keyName = index as keyof ITransactionFormErrors
                formErrors.value[keyName] = e[field]

            } else {
                let keyName = field as keyof ITransactionFormErrors
                formErrors.value[keyName] = e[field]
            }
        }
    }

    const resetFormErrors = () => {
        formErrors.value = {
            amount: [],
            customer_order_id: [],
            company_customer_id: [],
            currency: [],
            date: [],
            description: [],
            due_date: [],
            fiche_no: [],
            fiche_type: [],
            io_type: [],
            is_paid: [],
        }
    }

    return {
        transactionForm,
        editMode,
        formErrors,
        setTransactionForm,
        setFormErrors,
        resetFormErrors,
        setEditMode,
        $reset,
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
    import.meta.hot.accept(acceptHMRUpdate(useTransactionStore, import.meta.hot))
}
