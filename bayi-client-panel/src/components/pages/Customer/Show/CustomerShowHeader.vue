<script setup lang="ts">
import { ICompanyCustomerForm } from '/@src/shared/form/interface-company-customer';

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    customerData: {
        type: Object as PropType<ICompanyCustomerForm>,
        required: true
    }
})
const editUrl = computed(() => `/app/customers/${props?.id}/edit`);
const generatedImage = computed(() => {
    let _uri = "/images/user-logo.png";
    let company_name = props.customerData?.company_name;
    if (company_name) {
        _uri = `https://eu.ui-avatars.com/api/?name=${company_name}&background=252b36&color=fff&size=64`;
    }
    return _uri;
})
</script>
<template>
    <div class="card mb-0">
        <div class="card-body">
            <div class="media align-items-center text-center text-lg-left flex-column flex-lg-row m-0"
                style="position: relative;">
                <div class="mr-lg-3 mb-2 mb-lg-0">
                    <a href="#" class="profile-thumb">
                        <img :src="generatedImage" class="border-white rounded-circle" width="48" height="48" alt="">
                    </a>
                </div>

                <div class="media-body">
                    <h1 class="mb-0">{{ customerData?.company_name }}</h1>
                    <span class="d-block">{{ customerData?.email }}</span>
                </div>

                <div class="ml-lg-3 mt-2 mt-lg-0">
                    <ul class="list-inline list-inline-condensed mb-0">
                        <slot name="extra-actions"></slot>
                        <li class="list-inline-item">
                            <RouterLink :to="editUrl" class="btn btn-light border-transparent">
                                <i class="fas fa-cog mr-2"></i> Düzenle
                            </RouterLink>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>



<style scoped></style>