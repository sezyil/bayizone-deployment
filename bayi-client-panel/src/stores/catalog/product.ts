import { ref } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
import { IProductForm, IProductFormErrors, IProductVariantDimensionValue, IProductVariant, ProductLanguageKeyBasedType, ProductVariantType } from '../../shared/product/interface-product';
import { IProductCategory } from '../../shared/product/interface-product-category';
import { IProductImage } from '../../shared/product/interface-product-image';
import { IProductAttribute } from '../../shared/product/interface-product-attribute';
import { add, update } from '/@src/request/request-product';
import { resetProductData, resetProductErrors } from '../../utils/form/product_form';
import { IProductOption } from '../../shared/product/interface-product-option';
import { extractErrors } from '/@src/utils/api/catchFormErrors';



export const useCatalogProductStore = defineStore('catalogProduct', () => {
    const productData = ref<IProductForm>(resetProductData());

    const formErrors = ref<IProductFormErrors>(resetProductErrors())

    const $reset = () => {
        resetPdata()
        resetErrors()
    }

    //reset product data to default
    const resetPdata = () => {
        productData.value = resetProductData()
    }

    const resetErrors = () => {
        formErrors.value = resetProductErrors()
    }



    //set partial
    const setPartial = (data: Partial<IProductForm>) => {
        productData.value = { ...productData.value, ...data }
    }


    const addCategory = (category: IProductCategory) => {
        productData.value.links.categories.push(category)
    }

    const removeCategory = (category: IProductCategory) => {
        const index = productData.value.links.categories.findIndex((item) => item.id === category.id)
        productData.value.links.categories.splice(index, 1)
    }

    const setCategories = (categories: IProductCategory[]) => {
        productData.value.links.categories = categories
    }

    //add option
    const addOption = (option: IProductOption) => {
        productData.value.options.push(option)
    }

    //remove option
    const removeOption = (index: number) => {
        productData.value.options.splice(index, 1)
    }

    const addOptionValueRow = (index: number) => {
        productData.value.options[index].values.push({
            id: 0,
            option_value_id: '',
            add_to_price: 1,
            price: '0'
        })
    }

    //remove option row
    const removeOptionRow = (_rowIndex: number, _valueIndex: number) => {
        productData.value.options[_rowIndex].values.splice(_valueIndex, 1)
    }


    //add image
    const addImage = async (image: IProductImage) => {
        productData.value.images.push(image)
    }

    //remove image
    const removeImage = async (uri: string) => {
        productData.value.images = productData.value.images.filter((item) => item.image !== uri)
    }

    const setImageUri = (index: number, uri: string) => {
        productData.value.images[index].image = uri
    }

    const addVariant = (_type: ProductVariantType) => {
        if (_type === 'COLOR') {

            productData.value.colors.push({
                id: '0',
                variant_id: '',
                variant_value_id: [],
            })
            return
        }
        else if (_type === 'DIMENSION') {
            const tmp: IProductVariantDimensionValue = {
                height: 0,
                width: 0,
                length: 0,
            }
            productData.value.dimensions.push({
                id: '0',
                variant_id: '',
                value: []
            })
        }
    }

    const addVariantValue = (index: number, _type: ProductVariantType) => {
        if (_type === 'COLOR') {
            productData.value.colors[index].variant_value_id.push('')
            return
        }
        else if (_type === 'DIMENSION') {
            const tmp: IProductVariantDimensionValue = {
                height: 0,
                width: 0,
                length: 0,
            }
            productData.value.dimensions[index].value.push(tmp)
        }
    }

    const resetVariantValue = (index: number, variant_id: string, _type: ProductVariantType) => {
        if (_type === 'COLOR') {
            productData.value.colors[index].variant_id = variant_id
            productData.value.colors[index].variant_value_id = []
            return
        }
        else if (_type === 'DIMENSION') {
            productData.value.dimensions[index].variant_id = variant_id
            productData.value.dimensions[index].value = []
        }
    }

    const removeVariant = (index: number, _type: ProductVariantType) => {
        if (_type === 'COLOR') {
            productData.value.colors.splice(index, 1)
            return
        }
        else if (_type === 'DIMENSION') {
            productData.value.dimensions.splice(index, 1)
        }
    }

    const removeVariantValue = (index: number, _valueIndex: number, _type: ProductVariantType) => {
        if (_type === 'COLOR') {
            productData.value.colors[index].variant_value_id.splice(_valueIndex, 1)
            return
        }
        else if (_type === 'DIMENSION') {
            productData.value.dimensions[index].value.splice(_valueIndex, 1)
        }
    }

    //add attribute
    const addAttribute = () => {
        const attribute: IProductAttribute = {
            id: 0,
            attribute_id: 0,
            text: '',
        }
        productData.value.attributes.push(attribute)
    }

    //remove attribute
    const removeAttribute = (_index: number) => {
        productData.value.attributes.splice(_index, 1)
    }

    //set errors
    const setErrors = (e: any) => {
        if (typeof e.params === 'object') return //for swal instance
        //reset errors
        resetErrors()
        //object loop
        formErrors.value = { ...formErrors.value, ...extractErrors(e) }
    }

    const updateProduct = async (id: number) => {
        return await update(productData.value, id);
    }

    //register product
    const registerProduct = async () => {
        return await add(productData.value);
    }

    return {
        productData,
        setPartial,
        addCategory,
        removeCategory,
        setCategories,
        addImage,
        removeImage,
        addAttribute,
        removeAttribute,
        addOption,
        removeOption,
        removeOptionRow,
        addOptionValueRow,
        updateProduct,
        addVariantValue,
        registerProduct,
        addVariant,
        removeVariantValue,
        removeVariant,
        resetVariantValue,
        $reset,
        formErrors,
        setErrors,
    }
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useCatalogProductStore, import.meta.hot))
}